<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MigrationWebTriggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_access_migration_page()
    {
        $response = $this->get('/admin/migration?token=' . env('MIGRATION_TEST_TOKEN'));
        $response->assertRedirect('/login');

        // Test pharmacist (non-admin)
        $pharmacist = User::factory()->create(['role' => 'pharmacist']);
        $response = $this->actingAs($pharmacist)->get('/admin/migration?token=' . env('MIGRATION_TEST_TOKEN'));
        $response->assertStatus(403);
    }

    public function test_admin_without_token_cannot_access_migration_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin/migration');
        $response->assertStatus(403);
        $response->assertSee('Unauthorized migration access');
    }

    public function test_admin_with_token_can_access_migration_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin/migration?token=' . env('MIGRATION_TEST_TOKEN'));
        $response->assertStatus(200);
        $response->assertSee('Database Migration Utility');
    }

    public function test_get_request_cannot_execute_real_transfer()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Standard Laravel routing will throw 405 Method Not Allowed
        $response = $this->actingAs($admin)->get('/admin/migration/real-transfer?token=' . env('MIGRATION_TEST_TOKEN'));
        $response->assertStatus(405);
    }

    public function test_csrf_protection_on_post_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Without CSRF token in payload, should fail with 419 Page Expired
        $response = $this->actingAs($admin)->withSession(['migration_authorized' => true])->post('/admin/migration/dry-run');
        $response->assertStatus(419);
    }

    public function test_dry_run_executes_safely()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Mock Artisan to ensure the command is called with dry-run
        Artisan::shouldReceive('call')
            ->with('db:transfer-to-pg', ['--dry-run' => true])
            ->once();
            
        Artisan::shouldReceive('output')
            ->once()
            ->andReturn('Dry Run OK');

        $response = $this->actingAs($admin)->withSession(['migration_authorized' => true])->post('/admin/migration/dry-run', [
            '_token' => csrf_token()
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('dry_run_output', 'Dry Run OK');
    }

    public function test_migration_lock_prevents_simultaneous_transfers()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Force lock to simulate another running process
        Cache::lock('migration_transfer_lock', 600)->get();

        $response = $this->actingAs($admin)->withSession(['migration_authorized' => true])->post('/admin/migration/real-transfer', [
            '_token' => csrf_token()
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Another migration is currently running. Please wait.');
        
        // Release for other tests
        Cache::lock('migration_transfer_lock', 600)->forceRelease();
    }
}
