<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_has_full_access_to_user_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.settings.users.index'));
        $response->assertStatus(200);
    }

    public function test_pharmacist_cannot_access_user_management()
    {
        $pharmacist = User::factory()->create(['role' => 'pharmacist']);

        $response = $this->actingAs($pharmacist)->get(route('admin.settings.users.index'));
        $response->assertStatus(403);
    }

    public function test_cashier_cannot_access_user_management_or_reports()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $response1 = $this->actingAs($cashier)->get(route('admin.settings.users.index'));
        $response1->assertStatus(403);

        $response2 = $this->actingAs($cashier)->get(route('reports.index'));
        $response2->assertStatus(403);
    }

    public function test_cashier_can_access_pos_counter()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($cashier)->get('/pos');
        $response->assertStatus(200);
    }

    public function test_user_permission_helpers()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pharmacist = User::factory()->create(['role' => 'pharmacist']);
        $cashier = User::factory()->create(['role' => 'cashier']);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasPermission('manage_users'));

        $this->assertTrue($pharmacist->isPharmacist());
        $this->assertTrue($pharmacist->hasPermission('manage_medicines'));
        $this->assertFalse($pharmacist->isAdmin());

        $this->assertTrue($cashier->isCashier());
        $this->assertTrue($cashier->hasPermission('process_pos'));
        $this->assertFalse($cashier->hasPermission('manage_purchases'));
    }
}
