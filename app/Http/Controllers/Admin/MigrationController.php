<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrationController extends Controller
{
    private function checkToken(Request $request)
    {
        if ($request->query('token') !== env('MIGRATION_TEST_TOKEN')) {
            abort(403, 'Unauthorized migration access. Invalid or missing token.');
        }
    }

    public function index(Request $request)
    {
        $this->checkToken($request);

        $medicinesSourceCount = 0;
        $medicinesDestCount = 0;
        $totalSourceRows = 0;
        $totalDestRows = 0;

        try {
            $medicinesSourceCount = DB::connection('sqlite')->table('medicines')->count();
            $tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tables as $t) {
                if (!in_array($t->name, ['migrations', 'failed_jobs', 'cache', 'cache_locks', 'jobs', 'job_batches', 'sessions', 'personal_access_tokens'])) {
                    $totalSourceRows += DB::connection('sqlite')->table($t->name)->count();
                }
            }
        } catch (\Exception $e) {
            Log::error('SQLite connection error in migration index: ' . $e->getMessage());
        }

        try {
            if (\Illuminate\Support\Facades\Schema::connection('pgsql')->hasTable('medicines')) {
                $medicinesDestCount = DB::connection('pgsql')->table('medicines')->count();
            }
            
            $tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tables as $t) {
                if (!in_array($t->name, ['migrations', 'failed_jobs', 'cache', 'cache_locks', 'jobs', 'job_batches', 'sessions', 'personal_access_tokens'])) {
                    if (\Illuminate\Support\Facades\Schema::connection('pgsql')->hasTable($t->name)) {
                        $totalDestRows += DB::connection('pgsql')->table($t->name)->count();
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('PgSQL connection error in migration index: ' . $e->getMessage());
        }

        $sqlitePath = database_path('database.sqlite');
        $sqliteExists = file_exists($sqlitePath);
        $sqliteSize = $sqliteExists ? round(filesize($sqlitePath) / 1024 / 1024, 2) : 0;

        return view('admin.migration.index', compact(
            'medicinesSourceCount', 'medicinesDestCount', 'totalSourceRows', 'totalDestRows', 'sqlitePath', 'sqliteExists', 'sqliteSize'
        ));
    }

    public function dryRun(Request $request)
    {
        $this->checkToken($request);

        Artisan::call('db:transfer-to-pg', ['--dry-run' => true]);
        $output = Artisan::output();
        return back()->with('dry_run_output', $output);
    }

    public function realTransfer(Request $request)
    {
        $this->checkToken($request);
        
        $lock = Cache::lock('migration_transfer_lock', 600);

        if (!$lock->get()) {
            return back()->with('error', 'Another migration is currently running. Please wait.');
        }

        try {
            Artisan::call('db:transfer-to-pg');
            $output = Artisan::output();
            
            if (strpos($output, 'ERRORS DETECTED') !== false || strpos($output, 'FAIL') !== false) {
                 return back()->with('real_transfer_output', $output)->with('error', 'Migration completed with some errors.');
            }

            return back()->with('real_transfer_output', $output)->with('success', 'Real Data Migration completed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        } finally {
            $lock->release();
        }
    }
}
