<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_user_with_sales_history_via_soft_delete()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);

        // Create sale associated with cashier
        Sale::create([
            'invoice_number' => 'INV-TEST-001',
            'user_id' => $cashier->id,
            'subtotal' => 100,
            'total_amount' => 100,
            'paid_amount' => 100,
            'change_amount' => 0,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.settings.users.destroy', $cashier->id));

        $response->assertRedirect(route('admin.settings.users.index'));
        $response->assertSessionHas('message', 'User deleted successfully.');

        // User should be soft deleted
        $this->assertSoftDeleted('users', ['id' => $cashier->id]);

        // Sale record should still exist in database
        $this->assertDatabaseHas('sales', ['invoice_number' => 'INV-TEST-001', 'user_id' => $cashier->id]);
    }

    public function test_user_cannot_delete_own_account()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.settings.users.destroy', $admin->id));

        $response->assertSessionHas('error', 'You cannot delete your own account.');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }
}
