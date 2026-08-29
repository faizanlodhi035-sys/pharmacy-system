<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockLedgerService
{
    /**
     * Record a stock movement and synchronize the inventory record.
     * 
     * Types:
     * - PURCHASE (+)
     * - SALE (-)
     * - PURCHASE_RETURN (-)
     * - SALE_RETURN (+)
     * - ADJUSTMENT_IN (+)
     * - ADJUSTMENT_OUT (-)
     * - DAMAGE (-)
     * - EXPIRED (-)
     * - OPENING_STOCK (+)
     */
    public function recordMovement(
        int $medicineId,
        ?int $batchId,
        string $type,
        ?int $referenceId,
        ?string $referenceType,
        ?int $selectedUnitId,
        float $quantity,
        float $conversionToBase,
        float $baseQuantity,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        // Calculate previous and new stock in base units
        $inventory = Inventory::firstOrCreate(
            ['medicine_id' => $medicineId],
            [
                'total_base_quantity' => 0,
                'reserved_base_quantity' => 0,
                'available_base_quantity' => 0,
            ]
        );

        $prevStock = (float) $inventory->total_base_quantity;

        // Is it an incoming or outgoing movement?
        $incomingTypes = ['PURCHASE', 'SALE_RETURN', 'ADJUSTMENT_IN', 'OPENING_STOCK'];
        $outgoingTypes = ['SALE', 'PURCHASE_RETURN', 'ADJUSTMENT_OUT', 'DAMAGE', 'EXPIRED'];

        if (in_array(strtoupper($type), $incomingTypes)) {
            $newStock = $prevStock + abs($baseQuantity);
        } elseif (in_array(strtoupper($type), $outgoingTypes)) {
            $newStock = max(0, $prevStock - abs($baseQuantity));
        } else {
            $newStock = $prevStock;
        }

        // Create immutable movement entry
        $movement = StockMovement::create([
            'medicine_id' => $medicineId,
            'batch_id' => $batchId,
            'type' => strtoupper($type),
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'selected_unit_id' => $selectedUnitId,
            'quantity' => $quantity,
            'conversion_to_base' => $conversionToBase,
            'base_quantity' => $baseQuantity,
            'previous_stock' => $prevStock,
            'new_stock' => $newStock,
            'user_id' => $userId ?: (auth()->id() ?? 1),
            'notes' => $notes,
            'created_at' => now(),
        ]);

        // Sync inventory table directly from medicine_batches to guarantee single source of truth
        $this->syncInventory($medicineId);

        return $movement;
    }

    /**
     * Synchronize inventory row from active batches for a medicine.
     */
    public function syncInventory(int $medicineId): Inventory
    {
        $totalBase = (float) MedicineBatch::where('medicine_id', $medicineId)->sum('quantity');

        $inventory = Inventory::updateOrCreate(
            ['medicine_id' => $medicineId],
            [
                'total_base_quantity' => $totalBase,
                'available_base_quantity' => $totalBase,
            ]
        );

        return $inventory;
    }

    /**
     * Process Damaged stock
     */
    public function recordDamage(
        Medicine $medicine,
        MedicineBatch $batch,
        $packagingOrUnit,
        float $quantity,
        ?string $reason = null
    ): StockMovement {
        $pkgService = app(PackagingService::class);
        $conv = $pkgService->getConversionFactor($medicine, $packagingOrUnit);
        $baseQty = $pkgService->convertToBaseQuantity($medicine, $packagingOrUnit, $quantity);

        if ((float) $batch->quantity < $baseQty) {
            throw new \Exception("Batch does not have enough stock. Available: {$batch->quantity} base units.");
        }

        return DB::transaction(function () use ($medicine, $batch, $quantity, $conv, $baseQty, $reason) {
            $batch->decrement('quantity', $baseQty);

            return $this->recordMovement(
                medicineId: $medicine->id,
                batchId: $batch->id,
                type: 'DAMAGE',
                referenceId: null,
                referenceType: null,
                selectedUnitId: null,
                quantity: $quantity,
                conversionToBase: $conv,
                baseQuantity: $baseQty,
                notes: $reason ?: 'Damaged goods write-off'
            );
        });
    }

    /**
     * Process Expired stock write-off
     */
    public function recordExpired(
        Medicine $medicine,
        MedicineBatch $batch,
        ?string $notes = null
    ): StockMovement {
        $baseQty = (float) $batch->quantity;

        return DB::transaction(function () use ($medicine, $batch, $baseQty, $notes) {
            $batch->update([
                'quantity' => 0,
                'status' => 'expired'
            ]);

            return $this->recordMovement(
                medicineId: $medicine->id,
                batchId: $batch->id,
                type: 'EXPIRED',
                referenceId: null,
                referenceType: null,
                selectedUnitId: null,
                quantity: $baseQty,
                conversionToBase: 1.0,
                baseQuantity: $baseQty,
                notes: $notes ?: "Expired batch write-off for Batch {$batch->batch_number}"
            );
        });
    }

    /**
     * Process Manual Stock Adjustment
     */
    public function recordAdjustment(
        Medicine $medicine,
        MedicineBatch $batch,
        string $direction, // 'in' or 'out'
        $packagingOrUnit,
        float $quantity,
        ?string $reason = null
    ): StockMovement {
        $pkgService = app(PackagingService::class);
        $conv = $pkgService->getConversionFactor($medicine, $packagingOrUnit);
        $baseQty = $pkgService->convertToBaseQuantity($medicine, $packagingOrUnit, $quantity);

        $type = strtoupper($direction) === 'IN' ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';

        if ($type === 'ADJUSTMENT_OUT' && (float)$batch->quantity < $baseQty) {
            throw new \Exception("Batch does not have enough stock for adjustment. Available: {$batch->quantity} base units.");
        }

        return DB::transaction(function () use ($medicine, $batch, $type, $quantity, $conv, $baseQty, $reason) {
            if ($type === 'ADJUSTMENT_IN') {
                $batch->increment('quantity', $baseQty);
            } else {
                $batch->decrement('quantity', $baseQty);
            }

            return $this->recordMovement(
                medicineId: $medicine->id,
                batchId: $batch->id,
                type: $type,
                referenceId: null,
                referenceType: null,
                selectedUnitId: null,
                quantity: $quantity,
                conversionToBase: $conv,
                baseQuantity: $baseQty,
                notes: $reason ?: 'Manual stock adjustment'
            );
        });
    }
}
