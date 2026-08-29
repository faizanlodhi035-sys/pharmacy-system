<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicinePackaging;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\StockMovement;
use App\Models\Inventory;
use App\Services\PackagingService;
use App\Services\StockLedgerService;

class PackagingHierarchySystemTest extends TestCase
{
    protected PackagingService $packagingService;
    protected StockLedgerService $stockLedgerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->packagingService = app(PackagingService::class);
        $this->stockLedgerService = app(StockLedgerService::class);
    }

    /**
     * Test 1: Verify Standard Units exist
     */
    public function test_standard_units_exist()
    {
        $tablet = Unit::where('unit_id', 'tablet')->first();
        $strip = Unit::where('unit_id', 'strip')->first();
        $pack = Unit::where('unit_id', 'pack')->first();
        $bottle = Unit::where('unit_id', 'bottle')->first();

        $this->assertNotNull($tablet);
        $this->assertNotNull($strip);
        $this->assertNotNull($pack);
        $this->assertNotNull($bottle);
    }

    /**
     * Test 2: Full Panadol 500mg Scenario (Tablet -> Strip=10 -> Pack=100)
     */
    public function test_complete_packaging_hierarchy_and_transaction_flow()
    {
        $tabUnit = Unit::where('unit_id', 'tablet')->firstOrFail();
        $stripUnit = Unit::where('unit_id', 'strip')->firstOrFail();
        $packUnit = Unit::where('unit_id', 'pack')->firstOrFail();

        $category = Category::firstOrCreate(['name' => 'Pain Relief'], ['slug' => 'pain-relief']);
        $supplier = Supplier::firstOrCreate(['name' => 'GSK Pharma'], ['phone' => '1234567890']);
        $customer = Customer::firstOrCreate(['name' => 'Test Customer'], ['phone' => '03001234567']);

        // 1. Create Medicine with Base Unit: Tablet
        $medicine = Medicine::create([
            'name' => 'Panadol 500mg Test ' . time(),
            'generic_name' => 'Paracetamol',
            'category_id' => $category->id,
            'base_unit' => 'Tablet',
            'base_unit_id' => $tabUnit->id,
            'unit_price' => 12.00,
            'purchase_price' => 8.00,
            'has_expiry' => true,
        ]);

        // Level 1: Base Unit (Tablet)
        $pkgTablet = MedicinePackaging::create([
            'medicine_id' => $medicine->id,
            'unit_id' => $tabUnit->id,
            'level' => 1,
            'parent_packaging_id' => null,
            'quantity_in_parent' => 1.0,
            'conversion_to_base' => 1.0,
            'purchase_price' => 8.00,
            'sale_price' => 12.00,
            'barcode' => 'BAR-TAB-' . time(),
            'is_base_unit' => true,
            'allow_purchase' => true,
            'allow_sale' => true,
        ]);

        // Level 2: Secondary Unit (1 Strip = 10 Tablets)
        $pkgStrip = MedicinePackaging::create([
            'medicine_id' => $medicine->id,
            'unit_id' => $stripUnit->id,
            'level' => 2,
            'parent_packaging_id' => $pkgTablet->id,
            'quantity_in_parent' => 10.0,
            'conversion_to_base' => 10.0,
            'purchase_price' => 75.00,
            'sale_price' => 110.00,
            'barcode' => 'BAR-STRIP-' . time(),
            'is_base_unit' => false,
            'allow_purchase' => true,
            'allow_sale' => true,
        ]);

        // Level 3: Primary Unit (1 Pack = 10 Strips = 100 Tablets)
        $pkgPack = MedicinePackaging::create([
            'medicine_id' => $medicine->id,
            'unit_id' => $packUnit->id,
            'level' => 3,
            'parent_packaging_id' => $pkgStrip->id,
            'quantity_in_parent' => 10.0,
            'conversion_to_base' => 100.0,
            'purchase_price' => 700.00,
            'sale_price' => 1000.00,
            'barcode' => 'BAR-PACK-' . time(),
            'is_base_unit' => false,
            'allow_purchase' => true,
            'allow_sale' => true,
        ]);

        // 2. Verify Conversion Calculations
        $this->assertEquals(1.0, $this->packagingService->convertToBaseQuantity($medicine->id, $pkgTablet->id, 1));
        $this->assertEquals(10.0, $this->packagingService->convertToBaseQuantity($medicine->id, $pkgStrip->id, 1));
        $this->assertEquals(100.0, $this->packagingService->convertToBaseQuantity($medicine->id, $pkgPack->id, 1));
        $this->assertEquals(500.0, $this->packagingService->convertToBaseQuantity($medicine->id, $pkgPack->id, 5));

        // 3. Purchase 5 Packs (= 500 Tablets)
        $purchaseQty = 5;
        $packConversion = 100.0;
        $basePurchased = $purchaseQty * $packConversion; // 500 Tablets

        $purchaseInvoice = PurchaseInvoice::create([
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PINV-' . time(),
            'purchase_date' => now()->toDateString(),
            'subtotal' => $purchaseQty * 700.00,
            'grand_total' => $purchaseQty * 700.00,
        ]);

        $purchaseItem = PurchaseInvoiceItem::create([
            'purchase_invoice_id' => $purchaseInvoice->id,
            'medicine_id' => $medicine->id,
            'packaging_id' => $pkgPack->id,
            'unit_id' => $packUnit->id,
            'batch_number' => 'BAT-' . time(),
            'unit' => 'Pack',
            'conversion_to_base' => $packConversion,
            'quantity' => $purchaseQty,
            'base_quantity' => $basePurchased,
            'purchase_price' => 700.00,
            'selling_price' => 1000.00,
            'expiry_date' => now()->addMonths(12)->toDateString(),
            'total' => $purchaseQty * 700.00,
        ]);

        $batch1 = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => $purchaseItem->batch_number,
            'quantity' => $basePurchased,
            'purchase_price' => 700.00,
            'selling_price' => 1000.00,
            'purchase_price_per_base_unit' => 7.00,
            'selling_price_per_base_unit' => 10.00,
            'expiry_date' => now()->addMonths(12)->toDateString(),
            'supplier_id' => $supplier->id,
            'status' => 'active',
        ]);

        $this->stockLedgerService->recordMovement(
            medicineId: $medicine->id,
            batchId: $batch1->id,
            type: 'PURCHASE',
            referenceId: $purchaseItem->id,
            referenceType: PurchaseInvoiceItem::class,
            selectedUnitId: $packUnit->id,
            quantity: $purchaseQty,
            conversionToBase: $packConversion,
            baseQuantity: $basePurchased,
            userId: 1,
            notes: "Purchased 5 Packs"
        );

        $inventory = Inventory::where('medicine_id', $medicine->id)->first();
        $this->assertEquals(500.0, (float)$inventory->total_base_quantity);
        $this->assertEquals('5 Packs (Total: 500 Tablets)', $this->packagingService->formatStockBreakdown($medicine->id, 500));

        // 4. POS Sale 1: Sell 2 Packs (= 200 Tablets)
        $batch1->decrement('quantity', 200);
        $sale1 = Sale::create([
            'invoice_number' => 'INV-S1-' . time(),
            'user_id' => 1,
            'customer_id' => $customer->id,
            'subtotal' => 2000,
            'total_amount' => 2000,
            'paid_amount' => 2000,
        ]);

        $saleItem1 = SaleItem::create([
            'sale_id' => $sale1->id,
            'medicine_id' => $medicine->id,
            'batch_id' => $batch1->id,
            'packaging_id' => $pkgPack->id,
            'unit_id' => $packUnit->id,
            'unit' => 'Pack',
            'conversion_to_base' => $packConversion,
            'quantity' => 2,
            'base_quantity' => 200,
            'unit_price' => 1000.00,
            'subtotal' => 2000.00,
        ]);

        $this->stockLedgerService->recordMovement(
            medicineId: $medicine->id,
            batchId: $batch1->id,
            type: 'SALE',
            referenceId: $saleItem1->id,
            referenceType: SaleItem::class,
            selectedUnitId: $packUnit->id,
            quantity: 2,
            conversionToBase: $packConversion,
            baseQuantity: 200,
            userId: 1,
            notes: "Sold 2 Packs"
        );

        $this->assertEquals(300.0, (float)$inventory->fresh()->total_base_quantity);
        $this->assertEquals('3 Packs (Total: 300 Tablets)', $this->packagingService->formatStockBreakdown($medicine->id, 300));

        // 5. POS Sale 2: Sell 3 Strips (= 30 Tablets)
        $batch1->decrement('quantity', 30);
        $sale2 = Sale::create([
            'invoice_number' => 'INV-S2-' . time(),
            'user_id' => 1,
            'customer_id' => $customer->id,
            'subtotal' => 330,
            'total_amount' => 330,
            'paid_amount' => 330,
        ]);

        $saleItem2 = SaleItem::create([
            'sale_id' => $sale2->id,
            'medicine_id' => $medicine->id,
            'batch_id' => $batch1->id,
            'packaging_id' => $pkgStrip->id,
            'unit_id' => $stripUnit->id,
            'unit' => 'Strip',
            'conversion_to_base' => 10.0,
            'quantity' => 3,
            'base_quantity' => 30,
            'unit_price' => 110.00,
            'subtotal' => 330.00,
        ]);

        $this->stockLedgerService->recordMovement(
            medicineId: $medicine->id,
            batchId: $batch1->id,
            type: 'SALE',
            referenceId: $saleItem2->id,
            referenceType: SaleItem::class,
            selectedUnitId: $stripUnit->id,
            quantity: 3,
            conversionToBase: 10.0,
            baseQuantity: 30,
            userId: 1,
            notes: "Sold 3 Strips"
        );

        $this->assertEquals(270.0, (float)$inventory->fresh()->total_base_quantity);
        $this->assertEquals('2 Packs, 7 Strips (Total: 270 Tablets)', $this->packagingService->formatStockBreakdown($medicine->id, 270));

        // 6. POS Sale 3: Sell 5 Tablets (= 5 Tablets)
        $batch1->decrement('quantity', 5);
        $sale3 = Sale::create([
            'invoice_number' => 'INV-S3-' . time(),
            'user_id' => 1,
            'customer_id' => $customer->id,
            'subtotal' => 60,
            'total_amount' => 60,
            'paid_amount' => 60,
        ]);

        $saleItem3 = SaleItem::create([
            'sale_id' => $sale3->id,
            'medicine_id' => $medicine->id,
            'batch_id' => $batch1->id,
            'packaging_id' => $pkgTablet->id,
            'unit_id' => $tabUnit->id,
            'unit' => 'Tablet',
            'conversion_to_base' => 1.0,
            'quantity' => 5,
            'base_quantity' => 5,
            'unit_price' => 12.00,
            'subtotal' => 60.00,
        ]);

        $this->stockLedgerService->recordMovement(
            medicineId: $medicine->id,
            batchId: $batch1->id,
            type: 'SALE',
            referenceId: $saleItem3->id,
            referenceType: SaleItem::class,
            selectedUnitId: $tabUnit->id,
            quantity: 5,
            conversionToBase: 1.0,
            baseQuantity: 5,
            userId: 1,
            notes: "Sold 5 Tablets"
        );

        $this->assertEquals(265.0, (float)$inventory->fresh()->total_base_quantity);
        $this->assertEquals('2 Packs, 6 Strips, 5 Tablets (Total: 265 Tablets)', $this->packagingService->formatStockBreakdown($medicine->id, 265));

        // 7. Sales Return: Customer returns 1 Strip from Sale 2
        $salesReturn = SalesReturn::create([
            'sale_id' => $sale2->id,
            'customer_id' => $customer->id,
            'return_number' => 'SRET-' . time(),
            'return_date' => now()->toDateString(),
            'total_amount' => 110.00,
            'reason' => 'Customer over-purchased',
        ]);

        $returnItem = SalesReturnItem::create([
            'sales_return_id' => $salesReturn->id,
            'sale_item_id' => $saleItem2->id,
            'medicine_id' => $medicine->id,
            'batch_id' => $batch1->id,
            'packaging_id' => $saleItem2->packaging_id,
            'unit_id' => $saleItem2->unit_id,
            'unit' => $saleItem2->unit,
            'conversion_to_base' => $saleItem2->conversion_to_base,
            'quantity' => 1,
            'base_quantity' => 10,
            'unit_price' => 110.00,
            'subtotal' => 110.00,
        ]);

        $batch1->increment('quantity', 10);
        $this->stockLedgerService->recordMovement(
            medicineId: $medicine->id,
            batchId: $batch1->id,
            type: 'SALE_RETURN',
            referenceId: $returnItem->id,
            referenceType: SalesReturnItem::class,
            selectedUnitId: $saleItem2->unit_id,
            quantity: 1,
            conversionToBase: $saleItem2->conversion_to_base,
            baseQuantity: 10,
            userId: 1,
            notes: "Returned 1 Strip"
        );

        $this->assertEquals(275.0, (float)$inventory->fresh()->total_base_quantity);
        $this->assertEquals('2 Packs, 7 Strips, 5 Tablets (Total: 275 Tablets)', $this->packagingService->formatStockBreakdown($medicine->id, 275));

        // 8. Test Immutability: Change packaging configuration
        $pkgPack->update(['conversion_to_base' => 999.0]);

        $historicalSaleItem = SaleItem::find($saleItem1->id);
        $this->assertEquals(100.0, (float)$historicalSaleItem->conversion_to_base);
        $this->assertEquals(200.0, (float)$historicalSaleItem->base_quantity);

        // 9. Test Barcode Lookup
        $scanned = $this->packagingService->findByBarcode($pkgStrip->barcode);
        $this->assertNotNull($scanned);
        $this->assertEquals($medicine->id, $scanned['medicine']->id);
        $this->assertEquals(110.0, (float)$scanned['packaging']->sale_price);

        // 10. Damage Stock Movement
        $this->stockLedgerService->recordDamage($medicine, $batch1, $pkgStrip, 1, "Damaged in storage");
        $this->assertEquals(265.0, (float)$batch1->fresh()->quantity);

        // 11. Expired Stock Movement
        $this->stockLedgerService->recordExpired($medicine, $batch1, "Expired write-off");
        $this->assertEquals(0.0, (float)$batch1->fresh()->quantity);
        $this->assertEquals('expired', $batch1->fresh()->status);
    }
}
