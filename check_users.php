<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \Illuminate\Support\Facades\DB::select("SELECT email FROM users");
foreach ($users as $u) {
    echo $u->email . "\n";
}
