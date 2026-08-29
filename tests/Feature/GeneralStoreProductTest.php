<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneralStoreProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['id' => 1]);
        $this->category = Category::create([
            'name' => 'General Personal Care',
            'slug' => 'general-personal-care',
            'product_type' => 'both',
        ]);
    }

    /**
     * TEST SCENARIO 1: Lux Soap (Piece)
     * Purchase: 50 Pieces -> Expected stock: 50 Pieces
     * Sell: 3 Pieces -> Expected stock: 47 Pieces
     * Return: 1 Piece -> Expected stock: 48 Pieces
     */
    public function test_lux_soap_workflow(): void
    {
        // 1. Create General Product Lux Soap
        $soap = Medicine::create([
            'category_id' => $this->category->id,
            'product_type' => 'general',
            'name' => 'Lux Soap',
            'brand' => 'Lux',
            'base_unit' => 'Piece',
            'has_expiry' => false,
            'track_batches' => false,
            'purchase_price' => 80,
            'unit_price' => 100,
            'base_unit_selling_price' => 100,
            'alert_quantity' => 10,
        ]);

        // 2. Purchase 50 Pieces
        $supplier = Supplier::create(['name' => 'Unilever Supplier', 'phone' => '03001234567']);
        $batch = MedicineBatch::create([
            'medicine_id' => $soap->id,
            'supplier_id' => $supplier->id,
            'batch_number' => 'GEN-SOAP-01',
            'quantity' => 50,
            'purchase_price' => 80,
            'selling_price' => 100,
            'expiry_date' => null,
        ]);

        $this->assertEquals(50, $soap->fresh()->total_stock);

        // 3. Sell 3 Pieces
        $sale = Sale::create([
            'invoice_number' => 'INV-SOAP-01',
            'user_id' => $this->user->id,
            'subtotal' => 300,
            'total_amount' => 300,
            'paid_amount' => 300,
        ]);

        $saleItem = SaleItem::create([
            'sale_id' => $sale->id,
            'medicine_id' => $soap->id,
            'batch_id' => $batch->id,
            'unit' => 'Piece',
            'quantity' => 3,
            'base_quantity' => 3,
            'unit_price' => 100,
            'subtotal' => 300,
        ]);

        $batch->decrement('quantity', 3);
        $this->assertEquals(47, $soap->fresh()->total_stock);

        // 4. Return 1 Piece
        $salesReturn = SalesReturn::create([
            'sale_id' => $sale->id,
            'return_number' => 'SRET-SOAP-01',
            'return_date' => now()->toDateString(),
            'total_amount' => 100,
        ]);

        SalesReturnItem::create([
            'sales_return_id' => $salesReturn->id,
            'sale_item_id' => $saleItem->id,
            'medicine_id' => $soap->id,
            'batch_id' => $batch->id,
            'unit' => 'Piece',
            'quantity' => 1,
            'base_quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
        ]);

        $batch->increment('quantity', 1);
        $this->assertEquals(48, $soap->fresh()->total_stock);
    }

    /**
     * TEST SCENARIO 2: Water (1 Carton = 12 Bottles)
     * Purchase: 5 Cartons -> Expected stock: 60 Bottles
     * Sell: 2 Bottles -> Expected stock: 58 Bottles
     * Sell: 1 Carton -> Expected stock: 46 Bottles
     */
    public function test_water_carton_to_bottle_workflow(): void
    {
        // 1. Create Water product
        $water = Medicine::create([
            'category_id' => $this->category->id,
            'product_type' => 'general',
            'name' => 'Mineral Water 1.5L',
            'primary_unit' => 'Carton',
            'base_unit' => 'Bottle',
            'primary_unit_to_secondary' => 1,
            'secondary_unit_to_base' => 12,
            'has_expiry' => false,
            'track_batches' => false,
            'purchase_price' => 500,
            'unit_price' => 50,
            'base_unit_selling_price' => 50,
            'primary_unit_selling_price' => 600,
        ]);

        // Purchase 5 Cartons (5 * 12 = 60 Bottles)
        $batch = MedicineBatch::create([
            'medicine_id' => $water->id,
            'batch_number' => 'GEN-WATER-01',
            'quantity' => 60, // base units
            'purchase_price' => 500,
            'selling_price' => 600,
        ]);

        $this->assertEquals(60, $water->fresh()->total_stock);

        // Sell 2 Bottles
        $batch->decrement('quantity', 2);
        $this->assertEquals(58, $water->fresh()->total_stock);

        // Sell 1 Carton (12 Bottles)
        $cartonBaseQty = $water->convertToBaseQuantity(1, 'Carton');
        $this->assertEquals(12, $cartonBaseQty);

        $batch->decrement('quantity', $cartonBaseQty);
        $this->assertEquals(46, $water->fresh()->total_stock);
    }

    /**
     * TEST SCENARIO 3: Lux Soap Box (1 Box = 24 Pieces)
     * Purchase: 3 Boxes -> Expected stock: 72 Pieces
     * Sell: 5 Pieces -> Expected stock: 67 Pieces
     */
    public function test_soap_box_to_piece_workflow(): void
    {
        $soapBox = Medicine::create([
            'category_id' => $this->category->id,
            'product_type' => 'general',
            'name' => 'Lux Soap Pack Box',
            'primary_unit' => 'Box',
            'base_unit' => 'Piece',
            'primary_unit_to_secondary' => 1,
            'secondary_unit_to_base' => 24,
            'has_expiry' => false,
            'track_batches' => false,
            'purchase_price' => 1800,
            'unit_price' => 100,
            'base_unit_selling_price' => 100,
            'primary_unit_selling_price' => 2400,
        ]);

        // Purchase 3 Boxes (3 * 24 = 72 Pieces)
        $purchasedBaseQty = $soapBox->convertToBaseQuantity(3, 'Box');
        $this->assertEquals(72, $purchasedBaseQty);

        $batch = MedicineBatch::create([
            'medicine_id' => $soapBox->id,
            'batch_number' => 'GEN-BOX-01',
            'quantity' => $purchasedBaseQty,
            'purchase_price' => 1800,
            'selling_price' => 2400,
        ]);

        $this->assertEquals(72, $soapBox->fresh()->total_stock);

        // Sell 5 Pieces
        $batch->decrement('quantity', 5);
        $this->assertEquals(67, $soapBox->fresh()->total_stock);
    }
}
