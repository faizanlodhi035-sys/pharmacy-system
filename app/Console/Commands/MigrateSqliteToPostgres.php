<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateSqliteToPostgres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:transfer-to-pg';
    protected $description = 'Transfer data from SQLite to PostgreSQL';

    public function handle()
    {
        $this->info('Starting data transfer from SQLite to PostgreSQL...');

        // Ensure both connections are available
        try {
            \DB::connection('sqlite')->getPdo();
            \DB::connection('pgsql')->getPdo();
        } catch (\Exception $e) {
            $this->error('Database connection error: ' . $e->getMessage());
            return;
        }

        // Tables to transfer in order of dependencies (parents first)
        $tables = [
            'users',
            'categories',
            'suppliers',
            'customers',
            'units',
            'medicines',
            'medicine_packaging',
            'medicine_batches',
            'inventory',
            'stock_movements',
            'purchases',
            'purchase_invoices',
            'purchase_invoice_items',
            'purchase_returns',
            'purchase_return_items',
            'sales',
            'sale_items',
            'sales_returns',
            'sales_return_items',
            'hold_invoices',
        ];

        \DB::connection('pgsql')->statement('SET session_replication_role = replica;');

        foreach ($tables as $table) {
            $this->info("Transferring table: $table");
            
            // Delete existing records in PG to avoid duplication
            \DB::connection('pgsql')->table($table)->delete();

            $records = \DB::connection('sqlite')->table($table)->get()->map(function ($item) {
                return (array) $item;
            })->toArray();

            $chunks = array_chunk($records, 500);
            $bar = $this->output->createProgressBar(count($records));
            $bar->start();

            foreach ($chunks as $chunk) {
                \DB::connection('pgsql')->table($table)->insert($chunk);
                $bar->advance(count($chunk));
            }
            $bar->finish();
            $this->line('');

            // Update Postgres Sequence for the table so auto-increment works
            try {
                $maxId = \DB::connection('pgsql')->table($table)->max('id');
                if ($maxId) {
                    \DB::connection('pgsql')->statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$maxId})");
                }
            } catch (\Exception $e) {
                // Some tables might not have 'id' or sequence, ignore.
            }
        }

        \DB::connection('pgsql')->statement('SET session_replication_role = origin;');

        $this->info('Data transfer completed successfully!');
    }
}
