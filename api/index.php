<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Tandai environment Vercel
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Siapkan direktori writable di /tmp untuk Laravel
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

// Siapkan database SQLite di /tmp agar writable
$sqliteDestination = '/tmp/database.sqlite';
$sqliteSource = __DIR__ . '/../database/database.sqlite';
if (!file_exists($sqliteSource) && file_exists(__DIR__ . '/../webkel_patokan')) {
    $sqliteSource = __DIR__ . '/../webkel_patokan';
}

if (file_exists($sqliteSource)) {
    if (!file_exists($sqliteDestination) || (filemtime($sqliteSource) > filemtime($sqliteDestination))) {
        @copy($sqliteSource, $sqliteDestination);
        @touch($sqliteDestination, filemtime($sqliteSource));
    }
}

// Fallback cerdas: Jika DB_HOST masih berisi placeholder atau DB_CONNECTION=sqlite, gunakan SQLite lokal
$currentDbHost = getenv('DB_HOST');
$currentDbConn = getenv('DB_CONNECTION');
if ($currentDbHost === 'isi_dengan_host_database_cloud' || $currentDbConn === 'sqlite' || empty($currentDbHost)) {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    putenv('DB_DATABASE=' . $sqliteDestination);
    $_ENV['DB_DATABASE'] = $sqliteDestination;
    $_SERVER['DB_DATABASE'] = $sqliteDestination;
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
