<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Tandai environment Vercel
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Pastikan direktori writable di /tmp siap untuk Laravel
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
    $storagePath . '/app/public',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Salin file konfigurasi JSON default dari project ke /tmp/storage/app
$localAppStorage = __DIR__ . '/../storage/app';
if (is_dir($localAppStorage)) {
    @mkdir($storagePath . '/app', 0755, true);
    foreach (['village_profile.json', 'system_settings.json'] as $file) {
        if (file_exists($localAppStorage . '/' . $file) && !file_exists($storagePath . '/app/' . $file)) {
            @copy($localAppStorage . '/' . $file, $storagePath . '/app/' . $file);
        }
    }
}

try {
    // Teruskan ke entrypoint publik Laravel
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h2>Laravel Error on Vercel:</h2>";
    echo "<p style='color:red;'><strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
    echo "<p>Di file: <code>" . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</code></p>";
    echo "<pre style='background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
