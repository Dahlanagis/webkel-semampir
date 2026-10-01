<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL') || app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Pastikan akun admin Kelurahan Semampir selalu terdaftar dan aktif
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $semampirAdmin = \App\Models\User::where('email', 'admin@kelurahan-semampir.go.id')->first();
                if (!$semampirAdmin) {
                    \App\Models\User::create([
                        'name' => 'Administrator Kelurahan Semampir',
                        'email' => 'admin@kelurahan-semampir.go.id',
                        'username' => 'admin',
                        'password' => \Illuminate\Support\Facades\Hash::make('password'),
                        'role' => 'admin',
                        'is_active' => true,
                        'whatsapp' => '081234567890',
                        'referral_code' => 'SMP123',
                    ]);
                }
            }
        } catch (\Throwable $e) {}

        // Removed pending count global variable since letter request is deprecated

        // Share village profile and system settings globally for headers, footers, and admin sidebars
        View::composer('*', function ($view) {
            // We use * because it's needed in both public app layout and admin layout
            $profilePath = storage_path('app/village_profile.json');
            if (!file_exists($profilePath)) {
                $profilePath = base_path('storage/app/village_profile.json');
            }
            $villageProfile = [];
            if (file_exists($profilePath)) {
                $villageProfile = json_decode(file_get_contents($profilePath), true) ?? [];
            }
            $view->with('villageProfile', $villageProfile);

            // Load System Settings
            $systemSettings = \App\Http\Controllers\Admin\SettingController::getSettings();
            $view->with('systemSettings', $systemSettings);
        });

        // Share service types globally untuk navbar layouts.app
        View::composer('layouts.app', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('navigation_menus')) {
                    if (\App\Models\NavigationMenu::count() === 0) {
                        \App\Http\Controllers\Admin\NavigationMenuController::seedDefaultMenus();
                    }
                    $menus = \App\Models\NavigationMenu::where('is_active', true)
                        ->orderBy('order')
                        ->get()
                        ->groupBy('section');
                    
                    $view->with('navProfil', $menus->get('profil', collect()));
                    $view->with('navLayanan', $menus->get('layanan', collect()));
                    $view->with('navDokumen', $menus->get('dokumen', collect()));
                    $view->with('navInformasi', $menus->get('informasi', collect()));
                } else {
                    $view->with('navProfil', collect());
                    $view->with('navLayanan', collect());
                    $view->with('navDokumen', collect());
                    $view->with('navInformasi', collect());
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('announcements')) {
                    $view->with('globalAnnouncements', \App\Models\Announcement::where('is_active', true)->latest()->get());
                } else {
                    $view->with('globalAnnouncements', collect());
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
                    $view->with('profilePages', \App\Models\Page::where('category', 'profile')->where('is_active', true)->orderBy('order')->get());
                } else {
                    $view->with('profilePages', collect());
                }
            } catch (\Throwable $e) {
                // Fallback jika database belum terkoneksi / migrate
                $view->with('navProfil', collect());
                $view->with('navLayanan', collect());
                $view->with('navDokumen', collect());
                $view->with('navInformasi', collect());
                $view->with('globalAnnouncements', collect());
                $view->with('profilePages', collect());
            }
        });
    }
}
