<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Unit;

$categories = Category::all()->keyBy('slug');
$units = Unit::all()->keyBy('unit_id');

// Common Medicines
$meds = [
    ['name' => 'Augmentin 625mg', 'cat' => 'antibiotics', 'generic' => 'Amoxicillin + Clavulanate', 'brand' => 'Augmentin', 'strength' => '625mg', 'form' => 'Tablet', 'mfg' => 'GSK'],
    ['name' => 'Augmentin 1g', 'cat' => 'antibiotics', 'generic' => 'Amoxicillin + Clavulanate', 'brand' => 'Augmentin', 'strength' => '1g', 'form' => 'Tablet', 'mfg' => 'GSK'],
    ['name' => 'Brufen 400mg', 'cat' => 'pain-relief', 'generic' => 'Ibuprofen', 'brand' => 'Brufen', 'strength' => '400mg', 'form' => 'Tablet', 'mfg' => 'Abbott'],
    ['name' => 'Brufen Syrup', 'cat' => 'pain-relief', 'generic' => 'Ibuprofen', 'brand' => 'Brufen', 'strength' => '100mg/5ml', 'form' => 'Syrup', 'mfg' => 'Abbott'],
    ['name' => 'Calpol Syrup', 'cat' => 'pain-relief', 'generic' => 'Paracetamol', 'brand' => 'Calpol', 'strength' => '120mg/5ml', 'form' => 'Syrup', 'mfg' => 'GSK'],
    ['name' => 'Ponstan Forte', 'cat' => 'pain-relief', 'generic' => 'Mefenamic Acid', 'brand' => 'Ponstan', 'strength' => '500mg', 'form' => 'Tablet', 'mfg' => 'Pfizer'],
    ['name' => 'Arinac', 'cat' => 'respiratory', 'generic' => 'Ibuprofen + Pseudoephedrine', 'brand' => 'Arinac', 'strength' => '400mg/60mg', 'form' => 'Tablet', 'mfg' => 'Abbott'],
    ['name' => 'Rigix', 'cat' => 'respiratory', 'generic' => 'Cetirizine', 'brand' => 'Rigix', 'strength' => '10mg', 'form' => 'Tablet', 'mfg' => 'AGP'],
    ['name' => 'Softin', 'cat' => 'respiratory', 'generic' => 'Loratadine', 'brand' => 'Softin', 'strength' => '10mg', 'form' => 'Tablet', 'mfg' => 'PharmEvo'],
    ['name' => 'Concor 5mg', 'cat' => 'cardiovascular', 'generic' => 'Bisoprolol', 'brand' => 'Concor', 'strength' => '5mg', 'form' => 'Tablet', 'mfg' => 'Merck'],
    ['name' => 'Lipget 10mg', 'cat' => 'cardiovascular', 'generic' => 'Atorvastatin', 'brand' => 'Lipget', 'strength' => '10mg', 'form' => 'Tablet', 'mfg' => 'Getz Pharma'],
    ['name' => 'Risek 20mg', 'cat' => 'gastrointestinal', 'generic' => 'Omeprazole', 'brand' => 'Risek', 'strength' => '20mg', 'form' => 'Capsule', 'mfg' => 'Getz Pharma'],
    ['name' => 'Risek 40mg', 'cat' => 'gastrointestinal', 'generic' => 'Omeprazole', 'brand' => 'Risek', 'strength' => '40mg', 'form' => 'Capsule', 'mfg' => 'Getz Pharma'],
    ['name' => 'Nexum 40mg', 'cat' => 'gastrointestinal', 'generic' => 'Esomeprazole', 'brand' => 'Nexum', 'strength' => '40mg', 'form' => 'Capsule', 'mfg' => 'Martin Dow'],
    ['name' => 'Flagyl 400mg', 'cat' => 'antibiotics', 'generic' => 'Metronidazole', 'brand' => 'Flagyl', 'strength' => '400mg', 'form' => 'Tablet', 'mfg' => 'Sanofi'],
    ['name' => 'Ciproxin 500mg', 'cat' => 'antibiotics', 'generic' => 'Ciprofloxacin', 'brand' => 'Ciproxin', 'strength' => '500mg', 'form' => 'Tablet', 'mfg' => 'Bayer'],
    ['name' => 'Leflox 250mg', 'cat' => 'antibiotics', 'generic' => 'Levofloxacin', 'brand' => 'Leflox', 'strength' => '250mg', 'form' => 'Tablet', 'mfg' => 'Getz Pharma'],
    ['name' => 'Glucophage 500mg', 'cat' => 'diabetes-care', 'generic' => 'Metformin', 'brand' => 'Glucophage', 'strength' => '500mg', 'form' => 'Tablet', 'mfg' => 'Martin Dow'],
    ['name' => 'Amaryl 2mg', 'cat' => 'diabetes-care', 'generic' => 'Glimepiride', 'brand' => 'Amaryl', 'strength' => '2mg', 'form' => 'Tablet', 'mfg' => 'Sanofi'],
    ['name' => 'Surbex Z', 'cat' => 'vitamins-supplements', 'generic' => 'Multivitamins + Zinc', 'brand' => 'Surbex Z', 'strength' => 'Base', 'form' => 'Tablet', 'mfg' => 'Abbott'],
    ['name' => 'CAC 1000 Plus', 'cat' => 'vitamins-supplements', 'generic' => 'Calcium + Vitamin D', 'brand' => 'CAC 1000', 'strength' => '1000mg', 'form' => 'Effervescent Tablet', 'mfg' => 'GSK'],
    ['name' => 'Gaviscon Syrup', 'cat' => 'gastrointestinal', 'generic' => 'Sodium Alginate + Antacid', 'brand' => 'Gaviscon', 'strength' => '120ml', 'form' => 'Syrup', 'mfg' => 'Reckitt'],
    ['name' => 'Mucaine Syrup', 'cat' => 'gastrointestinal', 'generic' => 'Oxethazaine + Antacid', 'brand' => 'Mucaine', 'strength' => '120ml', 'form' => 'Syrup', 'mfg' => 'Wyeth'],
    ['name' => 'Motilium', 'cat' => 'gastrointestinal', 'generic' => 'Domperidone', 'brand' => 'Motilium', 'strength' => '10mg', 'form' => 'Tablet', 'mfg' => 'Janssen'],
    ['name' => 'Gravinate', 'cat' => 'gastrointestinal', 'generic' => 'Dimenhydrinate', 'brand' => 'Gravinate', 'strength' => '50mg', 'form' => 'Tablet', 'mfg' => 'Searle'],
];

foreach ($meds as $m) {
    $cat = isset($categories[$m['cat']]) ? $categories[$m['cat']]->id : 1;
    $unit = (stripos($m['form'], 'Syrup') !== false || stripos($m['form'], 'Drop') !== false) ? $units['bottle']->id : $units['tablet']->id;
    $base_unit_name = (stripos($m['form'], 'Syrup') !== false || stripos($m['form'], 'Drop') !== false) ? 'Bottle' : 'Tablet';

    Medicine::updateOrCreate(
        ['name' => $m['name']],
        [
            'category_id' => $cat,
            'product_type' => 'medicine',
            'generic_name' => $m['generic'],
            'brand' => $m['brand'],
            'strength' => $m['strength'],
            'dosage_form' => $m['form'],
            'dosage_unit' => $base_unit_name,
            'base_unit_id' => $unit,
            'manufacturer' => $m['mfg'],
            'alert_quantity' => 20,
            'has_expiry' => true,
            'track_batches' => true,
            'base_unit' => $base_unit_name,
            'unit_price' => 10,
            'purchase_price' => 8,
            'base_unit_selling_price' => 10,
            'status' => 'active'
        ]
    );
}

// General Store Items
$general = [
    ['name' => 'Lux Beauty Soap 100g', 'cat' => 'personal-care', 'brand' => 'Lux', 'mfg' => 'Unilever'],
    ['name' => 'Dettol Soap Original 100g', 'cat' => 'personal-care', 'brand' => 'Dettol', 'mfg' => 'Reckitt'],
    ['name' => 'Safeguard Soap 100g', 'cat' => 'personal-care', 'brand' => 'Safeguard', 'mfg' => 'P&G'],
    ['name' => 'Dove Shampoo 340ml', 'cat' => 'personal-care', 'brand' => 'Dove', 'mfg' => 'Unilever'],
    ['name' => 'Head & Shoulders 360ml', 'cat' => 'personal-care', 'brand' => 'Head & Shoulders', 'mfg' => 'P&G'],
    ['name' => 'Colgate Maximum Cavity Protection 120g', 'cat' => 'personal-care', 'brand' => 'Colgate', 'mfg' => 'Colgate-Palmolive'],
    ['name' => 'Sensodyne Fresh Mint 100g', 'cat' => 'personal-care', 'brand' => 'Sensodyne', 'mfg' => 'GSK'],
    ['name' => 'Pampers Size 4 (Maxi) 60s', 'cat' => 'baby-care', 'brand' => 'Pampers', 'mfg' => 'P&G'],
    ['name' => 'Johnson Baby Powder 200g', 'cat' => 'baby-care', 'brand' => 'Johnsons', 'mfg' => 'J&J'],
    ['name' => 'Nestle Lactogen 1 400g', 'cat' => 'baby-care', 'brand' => 'Lactogen', 'mfg' => 'Nestle'],
    ['name' => 'Nestle Nido Fortigrow 900g', 'cat' => 'baby-care', 'brand' => 'Nido', 'mfg' => 'Nestle'],
    ['name' => 'Ensure Vanilla 400g', 'cat' => 'vitamins-supplements', 'brand' => 'Ensure', 'mfg' => 'Abbott'],
    ['name' => 'Nestle Pure Life Water 1.5L', 'cat' => 'beverages-water', 'brand' => 'Pure Life', 'mfg' => 'Nestle'],
    ['name' => 'Aquafina Water 1.5L', 'cat' => 'beverages-water', 'brand' => 'Aquafina', 'mfg' => 'PepsiCo'],
    ['name' => 'Red Bull Energy Drink 250ml', 'cat' => 'beverages-water', 'brand' => 'Red Bull', 'mfg' => 'Red Bull'],
    ['name' => 'Lays French Cheese 40g', 'cat' => 'snacks-confectionery', 'brand' => 'Lays', 'mfg' => 'PepsiCo'],
    ['name' => 'Dairy Milk Chocolate 30g', 'cat' => 'snacks-confectionery', 'brand' => 'Dairy Milk', 'mfg' => 'Mondelez'],
];

foreach ($general as $g) {
    $cat = isset($categories[$g['cat']]) ? $categories[$g['cat']]->id : 1;
    
    Medicine::updateOrCreate(
        ['name' => $g['name']],
        [
            'category_id' => $cat,
            'product_type' => 'general',
            'brand' => $g['brand'],
            'manufacturer' => $g['mfg'],
            'base_unit_id' => $units['piece']->id,
            'alert_quantity' => 10,
            'has_expiry' => true,
            'track_batches' => false,
            'base_unit' => 'Piece',
            'unit_price' => 100,
            'purchase_price' => 80,
            'base_unit_selling_price' => 100,
            'status' => 'active'
        ]
    );
}

echo 'Seeded ' . count($meds) . ' medicines and ' . count($general) . ' general items.';
