<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBatch;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Category
        $category = Category::firstOrCreate(
            ['slug' => 'tablets-capsules'],
            [
                'name' => 'Tablets & Capsules',
                'description' => 'General oral medications'
            ]
        );

        // 2. Create Medicine
        $medicine = Medicine::firstOrCreate(
            ['barcode' => '8964000123456'],
            [
                'category_id' => $category->id,
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Acetaminophen',
                'brand' => 'Panadol',
                'dosage_unit' => 'Tablet',
                'unit_price' => 20.00,
                'purchase_price' => 15.00,
                'manufacturer' => 'GSK',
                'alert_quantity' => 10
            ]
        );

        // 3. Create Medicine Batch
        MedicineBatch::firstOrCreate(
            [
                'medicine_id' => $medicine->id,
                'batch_number' => 'BATCH-2026-01'
            ],
            [
                'quantity' => 150,
                'expiry_date' => '2028-12-31',
                'purchase_price' => 15.00,
                'selling_price' => 20.00
            ]
        );
    }
}