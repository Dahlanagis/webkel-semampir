<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'icon',
        'badge_label',
        'description',
        'required_documents',
        'order',
        'show_on_homepage',
        'is_online_request',
        'action_url',
        'is_active',
        'pdf_document',
        'sop_description',
        'operational_hours',
        'estimated_time',
        'cost',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'show_on_homepage' => 'boolean',
        'is_online_request' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($serviceType) {
            $url = '/standar-pelayanan?id=' . $serviceType->id;
            $exists = \App\Models\NavigationMenu::where('section', 'layanan')->where('url', $url)->exists();
            if (!$exists) {
                $maxOrder = \App\Models\NavigationMenu::where('section', 'layanan')->max('order') ?? 0;
                \App\Models\NavigationMenu::create([
                    'section' => 'layanan',
                    'title' => $serviceType->name,
                    'url' => $url,
                    'order' => $maxOrder + 1,
                    'is_active' => (bool)$serviceType->is_active,
                ]);
            }
        });

        static::updated(function ($serviceType) {
            $url = '/standar-pelayanan?id=' . $serviceType->id;
            $navMenu = \App\Models\NavigationMenu::where('section', 'layanan')->where('url', $url)->first();
            if ($navMenu) {
                $navMenu->update([
                    'title' => $serviceType->name,
                    'is_active' => (bool)$serviceType->is_active,
                ]);
            } else {
                $maxOrder = \App\Models\NavigationMenu::where('section', 'layanan')->max('order') ?? 0;
                \App\Models\NavigationMenu::create([
                    'section' => 'layanan',
                    'title' => $serviceType->name,
                    'url' => $url,
                    'order' => $maxOrder + 1,
                    'is_active' => (bool)$serviceType->is_active,
                ]);
            }
        });

        static::deleting(function ($serviceType) {
            if ($serviceType->pdf_document && \Illuminate\Support\Facades\Storage::disk('public')->exists($serviceType->pdf_document)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($serviceType->pdf_document);
            }
            \App\Models\NavigationMenu::where('section', 'layanan')
                ->where('url', '/standar-pelayanan?id=' . $serviceType->id)
                ->delete();
        });
    }
}
