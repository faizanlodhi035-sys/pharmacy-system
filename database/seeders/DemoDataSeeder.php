<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@pharmacy.com'],
            [
                'name' => 'Muhammad Faizan Khan Lodhi',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Also ensure test@example.com exists
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Categories
        $categories = [
            ['name' => 'Tablets & Capsules', 'slug' => 'tablets-capsules', 'description' => 'Oral solid dosages', 'product_type' => 'medicine'],
            ['name' => 'Syrups & Suspensions', 'slug' => 'syrups-suspensions', 'description' => 'Liquid oral medications', 'product_type' => 'medicine'],
            ['name' => 'Injections', 'slug' => 'injections', 'description' => 'Intravenous & Intramuscular injections', 'product_type' => 'medicine'],
            ['name' => 'Ointments & Creams', 'slug' => 'ointments-creams', 'description' => 'Topical skin applications', 'product_type' => 'medicine'],
            ['name' => 'Antibiotics', 'slug' => 'antibiotics', 'description' => 'Bacterial infection medicines', 'product_type' => 'medicine'],
            ['name' => 'Personal Care & Hygiene', 'slug' => 'personal-care', 'description' => 'Soaps, shampoos & toiletries', 'product_type' => 'general'],
            ['name' => 'Beverages & Water', 'slug' => 'beverages-water', 'description' => 'Mineral water and drinks', 'product_type' => 'general'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $tabletCategory = Category::where('slug', 'tablets-capsules')->first();
        $syrupCategory = Category::where('slug', 'syrups-suspensions')->first();
        $antibioticCategory = Category::where('slug', 'antibiotics')->first();
        $personalCareCategory = Category::where('slug', 'personal-care')->first();
        $beveragesCategory = Category::where('slug', 'beverages-water')->first();

        // 3. Suppliers
        $supplier1 = Supplier::updateOrCreate(
            ['phone' => '03001234567'],
            [
                'name' => 'GSK Pharma Pakistan',
                'contact_person' => 'Tariq Mahmood',
                'email' => 'contact@gskpharma.pk',
                'address' => 'Plot 14, I-9 Industrial Area, Islamabad',
                'gst_number' => 'GST-01293847',
                'opening_balance' => 45000.00
            ]
        );

        $supplier2 = Supplier::updateOrCreate(
            ['phone' => '03219876543'],
            [
                'name' => 'Sami Pharmaceuticals',
                'contact_person' => 'Kamran Shah',
                'email' => 'sales@samiphama.com',
                'address' => 'Korangi Industrial Area, Karachi',
                'gst_number' => 'GST-98765432',
                'opening_balance' => 12500.00
            ]
        );

        // 4. Medicines & General Store Products
        $medicinesData = [
            [
                'name' => 'Paracetamol 500mg',
                'product_type' => 'medicine',
                'generic_name' => 'Paracetamol',
                'brand' => 'Panadol',
                'dosage_unit' => '500mg Tablet',
                'unit_price' => 21.00,
                'purchase_price' => 15.00,
                'category_id' => $tabletCategory->id,
                'manufacturer' => 'GSK',
                'barcode' => '8901234567890',
                'alert_quantity' => 20,
                'batch_no' => 'BAT-PAR-2026',
                'qty' => 345,
                'expiry' => '2027-12-31',
                'primary_unit' => 'Pack',
                'secondary_unit' => 'Strip',
                'base_unit' => 'Tablet',
                'primary_to_sec' => 10,
                'sec_to_base' => 10,
            ],
            [
                'name' => 'Panadol Extra',
                'product_type' => 'medicine',
                'generic_name' => 'Paracetamol + Caffeine',
                'brand' => 'Panadol',
                'dosage_unit' => '500mg/65mg Tablet',
                'unit_price' => 25.00,
                'purchase_price' => 18.00,
                'category_id' => $tabletCategory->id,
                'manufacturer' => 'GSK',
                'barcode' => '8901234567891',
                'alert_quantity' => 15,
                'batch_no' => 'BAT-PAN-8812',
                'qty' => 106,
                'expiry' => '2027-08-15',
                'primary_unit' => 'Pack',
                'secondary_unit' => 'Strip',
                'base_unit' => 'Tablet',
                'primary_to_sec' => 10,
                'sec_to_base' => 10,
            ],
            [
                'name' => 'Lux Beauty Soap 100g',
                'product_type' => 'general',
                'generic_name' => null,
                'brand' => 'Lux',
                'dosage_unit' => 'Piece',
                'unit_price' => 120.00,
                'purchase_price' => 95.00,
                'category_id' => $personalCareCategory->id,
                'manufacturer' => 'Unilever',
                'barcode' => '8901234567899',
                'alert_quantity' => 10,
                'batch_no' => 'BAT-LUX-2026',
                'qty' => 240, // 10 Boxes of 24 Pieces
                'expiry' => null,
                'has_expiry' => false,
                'track_batches' => false,
                'primary_unit' => 'Box',
                'secondary_unit' => null,
                'base_unit' => 'Piece',
                'primary_to_sec' => 24,
                'sec_to_base' => 1,
            ],
            [
                'name' => 'Nestle Pure Life 1.5L',
                'product_type' => 'general',
                'generic_name' => null,
                'brand' => 'Nestle',
                'dosage_unit' => 'Bottle',
                'unit_price' => 90.00,
                'purchase_price' => 70.00,
                'category_id' => $beveragesCategory->id,
                'manufacturer' => 'Nestle Pakistan',
                'barcode' => '8901234567888',
                'alert_quantity' => 12,
                'batch_no' => 'BAT-[#001]',
                'qty' => 120, // 10 Cartons of 12 Bottles
                'expiry' => null,
                'has_expiry' => false,
                'track_batches' => false,
                'primary_unit' => 'Carton',
                'secondary_unit' => null,
                'base_unit' => 'Bottle',
                'primary_to_sec' => 12,
                'sec_to_base' => 1,
            ],
        ];

        foreach ($medicinesData as $medData) {
            $medicine = Medicine::updateOrCreate(
                ['name' => $medData['name']],
                [
                    'category_id' => $medData['category_id'],
                    'product_type' => $medData['product_type'],
                    'generic_name' => $medData['generic_name'],
                    'brand' => $medData['brand'],
                    'dosage_unit' => $medData['dosage_unit'],
                    'primary_unit' => $medData['primary_unit'],
                    'secondary_unit' => $medData['secondary_unit'],
                    'base_unit' => $medData['base_unit'],
                    'primary_unit_to_secondary' => $medData['primary_to_sec'],
                    'secondary_unit_to_base' => $medData['sec_to_base'],
                    'unit_price' => $medData['unit_price'],
                    'purchase_price' => $medData['purchase_price'],
                    'base_unit_selling_price' => $medData['unit_price'],
                    'manufacturer' => $medData['manufacturer'],
                    'barcode' => $medData['barcode'],
                    'alert_quantity' => $medData['alert_quantity'],
                    'has_expiry' => $medData['has_expiry'] ?? true,
                    'track_batches' => $medData['track_batches'] ?? true,
                ]
            );

            MedicineBatch::updateOrCreate(
                [
                    'medicine_id' => $medicine->id,
                    'batch_number' => $medData['batch_no']
                ],
                [
                    'quantity' => $medData['qty'],
                    'expiry_date' => $medData['expiry'],
                    'purchase_price' => $medData['purchase_price'],
                    'selling_price' => $medData['unit_price']
                ]
            );
        }

        // 5. Seed Demo Sales
        if (Sale::count() === 0) {
            $sale = Sale::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
                'user_id' => $admin->id,
                'customer_id' => null,
                'subtotal' => 1830.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'total_amount' => 1830.00,
                'paid_amount' => 1830.00,
                'change_amount' => 0.00,
                'payment_method' => 'cash'
            ]);

            $med = Medicine::first();
            $batch = MedicineBatch::where('medicine_id', $med->id)->first();
            if ($med && $batch) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'medicine_id' => $med->id,
                    'batch_id' => $batch->id,
                    'quantity' => 10,
                    'unit_price' => $med->unit_price,
                    'subtotal' => 10 * $med->unit_price
                ]);
            }
        }
    }
}
