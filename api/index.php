<?php

// Prepare /tmp directories for Vercel Serverless read-only environment
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/bootstrap/cache',
    '/tmp/database',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Determine database driver
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? null);

// If no cloud DB connection or explicitly sqlite, use /tmp/database/database.sqlite
if (!$dbConnection || $dbConnection === 'sqlite') {
    $targetDb = '/tmp/database/database.sqlite';
    $prodDb = __DIR__ . '/../database/production.sqlite';
    $sourceDb = __DIR__ . '/../database/database.sqlite';

    if (!file_exists($targetDb) || filesize($targetDb) === 0) {
        if (file_exists($prodDb) && filesize($prodDb) > 0) {
            @copy($prodDb, $targetDb);
        } elseif (file_exists($sourceDb) && filesize($sourceDb) > 0) {
            @copy($sourceDb, $targetDb);
        } else {
            @touch($targetDb);
        }
    }
    if (empty($_ENV['DB_DATABASE']) && empty(getenv('DB_DATABASE'))) {
        $_ENV['DB_DATABASE'] = $targetDb;
        putenv("DB_DATABASE={$targetDb}");
    }
}
// Set environment variables for Vercel Serverless
putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

if (empty($_ENV['APP_KEY'])) {
    $_ENV['APP_KEY'] = 'base64:nd/sNgRY/g4eQBVZL0iNa7GJPDz+iAEIna2N+UL8fys=';
    putenv('APP_KEY=base64:nd/sNgRY/g4eQBVZL0iNa7GJPDz+iAEIna2N+UL8fys=');
}

require __DIR__ . '/../public/index.php';
