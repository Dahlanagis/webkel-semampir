<?php

// Konfigurasi direktori storage sementara di Vercel (/tmp)
// Karena Vercel Serverless memiliki sistem berkas read-only kecuali /tmp
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
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
            mkdir($dir, 0755, true);
        }
    }
}

// Teruskan request ke entrypoint Laravel
require __DIR__ . '/../public/index.php';
