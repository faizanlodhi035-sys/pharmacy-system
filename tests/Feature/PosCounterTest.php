<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Category;
use Livewire\Livewire;
use App\Livewire\Pos\PosCounter;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_medicine_to_cart_in_pos()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Tablets', 'slug' => 'tablets', 'product_type' => 'medicine']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Panadol 500mg',
            'product_type' => 'medicine',
            'unit_price' => 25.00,
            'base_unit' => 'Tablet',
        ]);

        \App\Models\MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'TEST-BATCH-001',
            'quantity' => 100,
            'purchase_price' => 15.00,
            'selling_price' => 25.00,
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(PosCounter::class)
            ->call('addToCart', $medicine->id)
            ->assertSet('cart', function ($cart) {
                return count($cart) === 1;
            })
            ->call('checkout')
            ->assertSet('showInvoiceModal', true)
            ->assertSet('cart', []);

        $this->assertDatabaseHas('sales', [
            'total_amount' => 25.00,
        ]);
        $this->assertDatabaseHas('sale_items', [
            'medicine_id' => $medicine->id,
            'quantity' => 1,
            'base_quantity' => 1,
        ]);
    }
}
