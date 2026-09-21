<?php

$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $content = file_get_contents($file->getPathname());
        $originalContent = $content;

        // Pattern 1: @click="... = false" on div with fixed inset-0
        // We capture everything before and after the @click
        $content = preg_replace('/(@click(?!="away)[^=]*?Open\s*=\s*false")\s+(class="fixed\s+inset-0)/', '$2', $content);
        $content = preg_replace('/(@click\.away="[^"]+Open\s*=\s*false")\s+(class="fixed\s+inset-0)/', '$2', $content);
        $content = preg_replace('/(class="fixed\s+inset-0[^"]*")\s+@click(?!="away)[^=]*?Open\s*=\s*false"/', '$1', $content);

        // Pattern 2: Sometimes x-show="modalOpen" @click="modalOpen = false" class="fixed inset-0
        $content = preg_replace('/(@click="[a-zA-Z0-9_]+Open\s*=\s*false")(\s+class="fixed inset-0)/', '$2', $content);
        $content = preg_replace('/(@click\.away="[a-zA-Z0-9_]+Open\s*=\s*false")(\s+class="fixed inset-0)/', '$2', $content);

        // Also check dashboard blade files for @click.away="statModalOpen = false"
        // <div @click.away="statModalOpen = false" class="bg-white rounded-3xl max-w-2xl
        // We should just remove any @click.away=".*Open\s*=\s*false" from the main modal container.
        $content = preg_replace('/@click\.away="[a-zA-Z0-9_]+Open\s*=\s*false"\s*/', '', $content);

        if ($content !== $originalContent) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done.\n";
