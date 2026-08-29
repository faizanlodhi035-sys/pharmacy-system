<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DiscountReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_discount_analysis_report()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a sale with discount
        Sale::create([
            'user_id' => $admin->id,
            'invoice_number' => 'INV-TEST-001',
            'subtotal' => 1000.00,
            'discount' => 100.00,
            'tax' => 0.00,
            'total_amount' => 900.00,
            'paid_amount' => 900.00,
            'change_amount' => 0.00,
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($admin)->get('/reports/discounts');

        $response->assertStatus(200);
        $response->assertSee('Discount Analysis Report');
        $response->assertSee('INV-TEST-001');
        $response->assertSee('100.00');
    }
}
