<?php

echo "============================================\n";
echo " PHARMACY MANAGEMENT SYSTEM - FULL AUDIT\n";
echo "============================================\n\n";

echo "===== ENVIRONMENT =====\n";
echo "Laravel: ";
passthru('php artisan --version');
echo "PHP: " . PHP_VERSION . "\n\n";

function scanDirFiles($dir)
{
    if (!is_dir($dir)) return [];

    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $files[] = $file->getPathname();
        }
    }

    sort($files);
    return $files;
}

function section($title)
{
    echo "\n\n============================================\n";
    echo " $title\n";
    echo "============================================\n";
}

$dirs = [
    'MODELS'      => 'app/Models',
    'CONTROLLERS' => 'app/Http/Controllers',
    'LIVEWIRE'    => 'app/Livewire',
    'VIEWS'       => 'resources/views',
    'MIGRATIONS'  => 'database/migrations',
    'ROUTES'      => 'routes',
];

foreach ($dirs as $name => $dir) {
    section($name);

    $files = scanDirFiles($dir);

    foreach ($files as $file) {
        echo str_replace('\\', '/', $file) . "\n";
    }

    echo "TOTAL FILES: " . count($files) . "\n";
}

/*
|--------------------------------------------------------------------------
| Source Code Summary
|--------------------------------------------------------------------------
*/

section('PHP SOURCE SUMMARY');

$phpDirs = [
    'app/Models',
    'app/Http/Controllers',
    'app/Livewire',
];

foreach ($phpDirs as $dir) {

    foreach (scanDirFiles($dir) as $file) {

        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
            continue;
        }

        $content = @file_get_contents($file);

        echo "\n--- " . str_replace('\\', '/', $file) . " ---\n";

        if ($content === false) {
            echo "READ ERROR\n";
            continue;
        }

        if (preg_match('/namespace\s+([^;]+);/', $content, $m)) {
            echo "Namespace: " . trim($m[1]) . "\n";
        }

        if (preg_match('/class\s+(\w+)/', $content, $m)) {
            echo "Class: " . $m[1] . "\n";
        }

        if (preg_match_all('/function\s+(\w+)\s*\(/', $content, $m)) {
            echo "Methods: " . implode(', ', array_unique($m[1])) . "\n";
        }

        if (preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\]/s', $content, $m)) {
            echo "Fillable: " . preg_replace('/\s+/', ' ', trim($m[1])) . "\n";
        }

        if (preg_match_all('/\$this->(hasMany|belongsTo|hasOne|belongsToMany)\s*\(\s*([A-Za-z0-9_\\\\]+)::class/', $content, $m, PREG_SET_ORDER)) {
            echo "Relationships:\n";
            foreach ($m as $rel) {
                echo "  - {$rel[1]} -> {$rel[2]}\n";
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Blade Summary
|--------------------------------------------------------------------------
*/

section('BLADE VIEW SUMMARY');

foreach (scanDirFiles('resources/views') as $file) {

    if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
        continue;
    }

    $content = @file_get_contents($file);

    echo "\n--- " . str_replace('\\', '/', $file) . " ---\n";

    if ($content === false) {
        echo "READ ERROR\n";
        continue;
    }

    $forms = substr_count($content, '<form');
    $routes = [];

    if (preg_match_all('/(?:action|href)=["\']([^"\']+)["\']/', $content, $m)) {
        $routes = array_unique($m[1]);
    }

    echo "Forms: $forms\n";

    if ($routes) {
        echo "Links/Actions:\n";
        foreach ($routes as $route) {
            echo "  - $route\n";
        }
    }

    if (strpos($content, '@livewire') !== false) {
        echo "Uses Livewire: YES\n";
    }

    if (strpos($content, 'wire:') !== false) {
        echo "Uses wire directives: YES\n";
    }
}

/*
|--------------------------------------------------------------------------
| Migration Summary
|--------------------------------------------------------------------------
*/

section('DATABASE MIGRATION SUMMARY');

foreach (scanDirFiles('database/migrations') as $file) {

    if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
        continue;
    }

    $content = @file_get_contents($file);

    echo "\n--- " . basename($file) . " ---\n";

    if (preg_match_all("/Schema::create\\(['\"]([^'\"]+)/", $content, $m)) {
        echo "Creates: " . implode(', ', array_unique($m[1])) . "\n";
    }

    if (preg_match_all("/Schema::table\\(['\"]([^'\"]+)/", $content, $m)) {
        echo "Alters: " . implode(', ', array_unique($m[1])) . "\n";
    }

    if (preg_match_all("/\\->foreignId\\(['\"]([^'\"]+)/", $content, $m)) {
        echo "Foreign Keys: " . implode(', ', array_unique($m[1])) . "\n";
    }
}

/*
|--------------------------------------------------------------------------
| Route Target Check
|--------------------------------------------------------------------------
*/

section('ROUTE TARGET CHECK');

ob_start();
passthru('php artisan route:list --except-vendor');
$routeOutput = ob_get_clean();

echo $routeOutput;

/*
|--------------------------------------------------------------------------
| Database Counts
|--------------------------------------------------------------------------
*/

section('DATABASE RECORD COUNTS');

try {

    $artisan = <<<'ARTISAN'
<?php
echo "Users: " . \App\Models\User::count() . PHP_EOL;
echo "Categories: " . \App\Models\Category::count() . PHP_EOL;
echo "Medicines: " . \App\Models\Medicine::count() . PHP_EOL;
echo "Medicine Batches: " . \App\Models\MedicineBatch::count() . PHP_EOL;
echo "Suppliers: " . \App\Models\Supplier::count() . PHP_EOL;
echo "Purchases: " . \App\Models\Purchase::count() . PHP_EOL;
echo "Purchase Invoices: " . \App\Models\PurchaseInvoice::count() . PHP_EOL;
echo "Purchase Invoice Items: " . \App\Models\PurchaseInvoiceItem::count() . PHP_EOL;
echo "Customers: " . \App\Models\Customer::count() . PHP_EOL;
echo "Sales: " . \App\Models\Sale::count() . PHP_EOL;
echo "Sale Items: " . \App\Models\SaleItem::count() . PHP_EOL;
ARTISAN;

    file_put_contents('__audit_db.php', $artisan);

    ob_start();
    passthru('php __audit_db.php');
    echo ob_get_clean();

    @unlink('__audit_db.php');

} catch (Throwable $e) {
    echo "Database audit error: " . $e->getMessage() . "\n";
}

echo "\n\n============================================\n";
echo " AUDIT COMPLETE\n";
echo "============================================\n";