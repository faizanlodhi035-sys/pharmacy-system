<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteToPostgres extends Command
{
    protected $signature = 'db:transfer-to-pg {--dry-run : Only verify schema and row counts without transferring}';
    protected $description = 'Safely transfer data from SQLite to PostgreSQL with strict constraints and chunking';

    public function handle()
    {
        $this->info('Starting strictly safe data transfer from SQLite to PostgreSQL...');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('--- DRY RUN MODE ---');
        }

        try {
            DB::connection('sqlite')->getPdo();
            $pgsqlPdo = DB::connection('pgsql')->getPdo();
        } catch (\Exception $e) {
            $this->error('Database connection error: ' . $e->getMessage());
            return 1;
        }

        $tables = collect(DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"))
            ->pluck('name')
            ->toArray();

        $excludedTables = ['migrations', 'failed_jobs', 'cache', 'cache_locks', 'jobs', 'job_batches', 'sessions'];
        $tables = array_diff($tables, $excludedTables);

        // Strict ordering for foreign key constraints without disabling them
        $order = [
            'users', 'categories', 'suppliers', 'customers', 'units', 'medicines', 
            'medicine_packaging', 'medicine_batches', 'inventory', 'stock_movements', 
            'purchases', 'purchase_invoices', 'purchase_invoice_items', 'purchase_returns', 
            'purchase_return_items', 'sales', 'sale_items', 'sales_returns', 'sales_return_items', 
            'hold_invoices'
        ];
        
        usort($tables, function($a, $b) use ($order) {
            $posA = array_search($a, $order);
            $posB = array_search($b, $order);
            if ($posA === false && $posB === false) return 0;
            if ($posA === false) return 1;
            if ($posB === false) return -1;
            return $posA <=> $posB;
        });

        $allPassed = true;
        $totalTables = count($tables);
        $successTables = 0;
        $failedTables = 0;
        $totalSourceRows = 0;
        $totalDestRows = 0;

        foreach ($tables as $table) {
            $this->info("--------------------------------------------------");
            $this->info("Processing table: $table");

            if (!Schema::connection('pgsql')->hasTable($table)) {
                $this->error("FAIL: Table '$table' does not exist in PostgreSQL. Ensure migrations are run.");
                $allPassed = false;
                $failedTables++;
                continue;
            }

            $sourceCount = DB::connection('sqlite')->table($table)->count();
            $totalSourceRows += $sourceCount;

            $indexes = Schema::connection('sqlite')->getIndexes($table);
            $primaryKey = null;
            foreach ($indexes as $index) {
                if ($index['primary']) {
                    $primaryKey = $index['columns'];
                    break;
                }
            }

            if (!$primaryKey) {
                $this->error("FAIL: Table '$table' has no primary key. Idempotent transfer cannot be guaranteed safely.");
                $allPassed = false;
                $failedTables++;
                continue;
            }

            if ($isDryRun) {
                $destCount = DB::connection('pgsql')->table($table)->count();
                $this->line("Dry Run OK: $table - Source: $sourceCount | Dest: $destCount | PK: " . implode(',', $primaryKey));
                $successTables++;
                $totalDestRows += $destCount;
                continue;
            }

            DB::connection('pgsql')->beginTransaction();
            try {
                $orderByCol = $primaryKey[0];
                
                DB::connection('sqlite')->table($table)->orderBy($orderByCol)->chunk(500, function ($records) use ($table, $primaryKey) {
                    $chunk = $records->map(function ($item) {
                        return (array) $item;
                    })->toArray();
                    
                    DB::connection('pgsql')->table($table)->upsert($chunk, $primaryKey);
                });

                if (count($primaryKey) === 1 && $primaryKey[0] === 'id') {
                    $hasSequence = DB::connection('pgsql')->selectOne(
                        "SELECT count(*) as count FROM pg_class WHERE relkind = 'S' AND relname = '{$table}_id_seq'"
                    )->count > 0;
                    
                    if ($hasSequence) {
                        $maxId = DB::connection('pgsql')->table($table)->max('id');
                        if ($maxId) {
                            DB::connection('pgsql')->statement("SELECT setval('{$table}_id_seq', {$maxId})");
                        }
                    }
                }

                DB::connection('pgsql')->commit();
            } catch (\Exception $e) {
                DB::connection('pgsql')->rollBack();
                $this->error("FAIL: Transfer error on table '$table': " . $e->getMessage());
                $allPassed = false;
                $failedTables++;
                continue;
            }

            $finalDestCount = DB::connection('pgsql')->table($table)->count();
            $totalDestRows += $finalDestCount;

            if ($sourceCount !== $finalDestCount) {
                $this->error("VERIFICATION FAIL: $table - Source($sourceCount) != Dest($finalDestCount)");
                $allPassed = false;
                $failedTables++;
            } else {
                $this->info("SUCCESS: $table - Source: $sourceCount | Dest: $finalDestCount");
                $successTables++;
            }
        }

        $this->info("==================================================");
        $this->info("FINAL SUMMARY");
        $this->info("TOTAL TABLES: $totalTables");
        $this->info("SUCCESSFUL TABLES: $successTables");
        $this->info("FAILED TABLES: $failedTables");
        $this->info("TOTAL SOURCE ROWS: $totalSourceRows");
        $this->info("TOTAL DESTINATION ROWS: $totalDestRows");

        if (!$allPassed) {
            $this->error("ERRORS DETECTED: Migration verification failed.");
            return 1;
        }

        $this->info("Data transfer verified and completed safely.");
        return 0;
    }
}
