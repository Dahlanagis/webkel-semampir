<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }

    protected static function booted()
    {
        static::created(function ($document) {
            $url = '/dokumen?id=' . $document->id;
            $exists = \App\Models\NavigationMenu::where('section', 'dokumen')->where('url', $url)->exists();
            if (!$exists) {
                $maxOrder = \App\Models\NavigationMenu::where('section', 'dokumen')->max('order') ?? 0;
                \App\Models\NavigationMenu::create([
                    'section' => 'dokumen',
                    'title' => $document->name,
                    'url' => $url,
                    'order' => $maxOrder + 1,
                    'is_active' => (bool)$document->is_active,
                ]);
            }
        });

        static::updated(function ($document) {
            $url = '/dokumen?id=' . $document->id;
            $navMenu = \App\Models\NavigationMenu::where('section', 'dokumen')->where('url', $url)->first();
            if ($navMenu) {
                $navMenu->update([
                    'title' => $document->name,
                    'is_active' => (bool)$document->is_active,
                ]);
            } else {
                $maxOrder = \App\Models\NavigationMenu::where('section', 'dokumen')->max('order') ?? 0;
                \App\Models\NavigationMenu::create([
                    'section' => 'dokumen',
                    'title' => $document->name,
                    'url' => $url,
                    'order' => $maxOrder + 1,
                    'is_active' => (bool)$document->is_active,
                ]);
            }
        });

        static::deleting(function ($document) {
            foreach ($document->files as $file) {
                if ($file->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                }
            }
            \App\Models\NavigationMenu::where('section', 'dokumen')
                ->where('url', '/dokumen?id=' . $document->id)
                ->delete();
        });
    }
}
