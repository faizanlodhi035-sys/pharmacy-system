<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = \App\Models\Category::firstOrCreate(['slug' => 'pain-relief'], ['name' => 'Pain Relief']);
$u = \App\Models\Unit::firstOrCreate(['unit_id' => 'tablet'], ['name' => 'Tablet', 'symbol' => 'Tab']);
\App\Models\Medicine::updateOrCreate(
    ['name' => 'Panadol Extra'],
    [
        'category_id' => $c->id,
        'product_type' => 'medicine',
        'generic_name' => 'Paracetamol + Caffeine',
        'brand' => 'Panadol Extra',
        'strength' => '500mg/65mg',
        'dosage_form' => 'Tablet',
        'dosage_unit' => 'Tablet',
        'base_unit_id' => $u->id,
        'manufacturer' => 'GSK',
        'barcode' => '8961123456789',
        'alert_quantity' => 20,
        'reorder_level' => 20,
        'tax_rate' => 0,
        'has_expiry' => true,
        'track_batches' => true,
        'base_unit' => 'Tablet',
        'unit_price' => 5,
        'purchase_price' => 4,
        'base_unit_selling_price' => 5,
        'status' => 'active'
    ]
);
\App\Models\Medicine::updateOrCreate(
    ['name' => 'Paracetamol 500mg'],
    [
        'category_id' => $c->id,
        'product_type' => 'medicine',
        'generic_name' => 'Paracetamol',
        'brand' => 'Paracetamol',
        'strength' => '500mg',
        'dosage_form' => 'Tablet',
        'dosage_unit' => 'Tablet',
        'base_unit_id' => $u->id,
        'manufacturer' => 'Local Pharma',
        'alert_quantity' => 50,
        'has_expiry' => true,
        'track_batches' => true,
        'base_unit' => 'Tablet',
        'unit_price' => 2,
        'purchase_price' => 1,
        'base_unit_selling_price' => 2,
        'status' => 'active'
    ]
);
echo 'Done';
