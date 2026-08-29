<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicinePackaging;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Services\StockLedgerService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MigrateExistingMedicinesPackagingSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = Medicine::with(['batches', 'packagings'])->get();

        foreach ($medicines as $medicine) {
            $defaultBase = $medicine->is_general ? 'Piece' : 'Tablet';
            $baseName = $medicine->base_unit ?: ($medicine->dosage_unit ?: $defaultBase);
            $baseSlug = Str::slug($baseName);

            $baseUnit = Unit::firstOrCreate(
                ['unit_id' => $baseSlug],
                [
                    'name' => $baseName,
                    'symbol' => substr($baseName, 0, 4),
                    'allow_decimal' => in_array($baseSlug, ['ml', 'liter', 'gram', 'kg']),
                    'status' => 'active',
                ]
            );

            if (!$medicine->base_unit_id) {
                $medicine->base_unit_id = $baseUnit->id;
                $medicine->save();
            }

            $basePrice = (float) ($medicine->base_unit_selling_price ?? $medicine->unit_price ?? 0);
            $basePurchase = (float) ($medicine->purchase_price ?? 0);

            // 1. Create Base Unit Packaging
            $basePkg = MedicinePackaging::updateOrCreate(
                [
                    'medicine_id' => $medicine->id,
                    'unit_id' => $baseUnit->id,
                ],
                [
                    'conversion_to_base' => 1.0,
                    'quantity_in_parent' => 1.0,
                    'parent_packaging_id' => null,
                    'display_name' => $baseName,
                    'barcode' => $medicine->barcode,
                    'purchase_price' => $basePurchase,
                    'sale_price' => $basePrice,
                    'allow_purchase' => true,
                    'allow_sale' => true,
                    'status' => 'active',
                ]
            );

            $secPkg = null;
            // 2. Create Secondary Unit Packaging if present
            if (!empty($medicine->secondary_unit) && $medicine->is_medicine) {
                $secName = $medicine->secondary_unit;
                $secSlug = Str::slug($secName);
                $secUnit = Unit::firstOrCreate(
                    ['unit_id' => $secSlug],
                    ['name' => $secName, 'symbol' => substr($secName, 0, 4), 'allow_decimal' => false, 'status' => 'active']
                );

                $secConversion = (float) max(1, (int)($medicine->secondary_unit_to_base ?: 1));
                $secPrice = (float) ($medicine->secondary_unit_selling_price ?? round($basePrice * $secConversion, 2));

                $secPkg = MedicinePackaging::updateOrCreate(
                    [
                        'medicine_id' => $medicine->id,
                        'unit_id' => $secUnit->id,
                    ],
                    [
                        'conversion_to_base' => $secConversion,
                        'quantity_in_parent' => $secConversion,
                        'parent_packaging_id' => $basePkg->id,
                        'display_name' => "{$secName} ({$secConversion} {$baseName}s)",
                        'purchase_price' => round($basePurchase * $secConversion, 2),
                        'sale_price' => $secPrice,
                        'allow_purchase' => true,
                        'allow_sale' => true,
                        'status' => 'active',
                    ]
                );
            }

            // 3. Create Primary Unit Packaging if present
            if (!empty($medicine->primary_unit)) {
                $primName = $medicine->primary_unit;
                $primSlug = Str::slug($primName);
                $primUnit = Unit::firstOrCreate(
                    ['unit_id' => $primSlug],
                    ['name' => $primName, 'symbol' => substr($primName, 0, 4), 'allow_decimal' => false, 'status' => 'active']
                );

                $p2s = max(1, (int)($medicine->primary_unit_to_secondary ?: 1));
                $s2b = max(1, (int)($medicine->secondary_unit_to_base ?: 1));
                $primConversion = (float) ($p2s * $s2b);
                $primPrice = (float) ($medicine->primary_unit_selling_price ?? round($basePrice * $primConversion, 2));

                MedicinePackaging::updateOrCreate(
                    [
                        'medicine_id' => $medicine->id,
                        'unit_id' => $primUnit->id,
                    ],
                    [
                        'conversion_to_base' => $primConversion,
                        'quantity_in_parent' => (float)$p2s,
                        'parent_packaging_id' => $secPkg ? $secPkg->id : $basePkg->id,
                        'display_name' => "{$primName} ({$primConversion} {$baseName}s)",
                        'purchase_price' => round($basePurchase * $primConversion, 2),
                        'sale_price' => $primPrice,
                        'allow_purchase' => true,
                        'allow_sale' => true,
                        'status' => 'active',
                    ]
                );
            }

            // 4. Sync Inventory
            app(StockLedgerService::class)->syncInventory($medicine->id);
        }
    }
}
