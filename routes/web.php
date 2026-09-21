<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;

use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\PostCategoryController as AdminPostCategoryController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ResidentController as AdminResidentController;
use App\Http\Controllers\Admin\RtRwController as AdminRtRwController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceTypeController as AdminServiceTypeController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VillageProfileController as AdminVillageProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use Illuminate\Support\Facades\Route;

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/visi-misi', [HomeController::class, 'visiMisi'])->name('visi-misi');
Route::get('/struktur-organisasi', [HomeController::class, 'strukturOrganisasi'])->name('struktur-organisasi');
Route::get('/sejarah', [HomeController::class, 'sejarah'])->name('sejarah');
Route::get('/berita', [HomeController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [HomeController::class, 'beritaDetail'])->name('berita.detail');
Route::get('/layanan/{slug}', [HomeController::class, 'layananDetail'])->name('layanan.detail');
Route::get('/lokasi', [HomeController::class, 'lokasi'])->name('lokasi');
Route::get('/layanan-whatsapp', [HomeController::class, 'layananWhatsapp'])->name('layanan-whatsapp');
Route::get('/galeri', [HomeController::class, 'galeri'])->name('galeri');
Route::get('/transparansi', [HomeController::class, 'transparansi'])->name('transparansi');
Route::get('/dokumen', [HomeController::class, 'dokumen'])->name('dokumen');
Route::get('/standar-pelayanan', [HomeController::class, 'standarPelayanan'])->name('standar-pelayanan');
Route::get('/pengumuman', [HomeController::class, 'pengumuman'])->name('pengumuman');
Route::get('/halaman/{slug}', [HomeController::class, 'page'])->name('page');
Route::get('/profil/{slug}', [\App\Http\Controllers\ProfilePageController::class, 'show'])->name('profile.page');

// ==========================================
// GUEST ONLY AUTHENTICATION ROUTES
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/reload-captcha', [AuthController::class, 'reloadCaptcha'])->name('captcha.reload');
    Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
});

// ==========================================
// AUTHENTICATED ROUTES
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================
    // 1. ADMIN PANEL ROUTES (Role: Admin)
    // ==========================================
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // CMS: Berita & Artikel
        Route::prefix('berita')->name('berita.')->group(function () {
            Route::get('/', [AdminPostController::class, 'index'])->name('index');
            Route::post('/', [AdminPostController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminPostController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminPostController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-featured', [AdminPostController::class, 'toggleFeatured'])->name('toggle_featured');
        });

        // CMS: Kategori Informasi (Berita, Pengumuman, Galeri)
        Route::prefix('kategori')->name('kategori.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('destroy');
        });

        // CMS: Kelola Header Navigasi Dinamis
        Route::resource('navigation', \App\Http\Controllers\Admin\NavigationMenuController::class)->except(['create', 'show', 'edit']);

        // CMS: Media Library & Pengelola Berkas Publik
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [AdminMediaController::class, 'index'])->name('index');
            Route::post('/', [AdminMediaController::class, 'store'])->name('store');
            Route::delete('/', [AdminMediaController::class, 'destroy'])->name('destroy');
        });

        // CMS: Pengumuman & Running Text Marquee
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
            Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminAnnouncementController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle', [AdminAnnouncementController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
        });

        // CMS: Galeri Kegiatan Foto
        Route::prefix('galeri')->name('galeri.')->group(function () {
            Route::get('/', [AdminGalleryController::class, 'index'])->name('index');
            Route::post('/', [AdminGalleryController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminGalleryController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminGalleryController::class, 'destroy'])->name('destroy');
            Route::delete('/{galleryId}/image/{imageId}', [AdminGalleryController::class, 'destroyImage'])->name('destroyImage');
            Route::patch('/{id}/toggle-homepage', [AdminGalleryController::class, 'toggleHomepage'])->name('toggle_homepage');
        });

        // CMS: Dokumen Publik
        Route::resource('documents', App\Http\Controllers\Admin\DocumentController::class);
        Route::delete('documents/file/{id}', [App\Http\Controllers\Admin\DocumentController::class, 'destroyFile'])->name('documents.destroyFile');

        // CMS: Halaman Dinamis Profil
        Route::resource('pages', App\Http\Controllers\Admin\PageController::class)->only(['index', 'edit', 'update']);

        // CMS: Kelola Beranda (Section-based)
        Route::prefix('kelola-beranda')->name('beranda.')->group(function () {
            Route::get('identitas-sambutan', [\App\Http\Controllers\Admin\VillageProfileController::class, 'identitasSambutan'])->name('identitas_sambutan');
            Route::get('sotk', [\App\Http\Controllers\Admin\VillageProfileController::class, 'sotk'])->name('sotk');
            Route::get('visi-misi-sejarah', [\App\Http\Controllers\Admin\VillageProfileController::class, 'visiMisiSejarah'])->name('visi_misi_sejarah');
            Route::get('banner', [\App\Http\Controllers\Admin\VillageProfileController::class, 'banner'])->name('banner');
            Route::get('statistik', [\App\Http\Controllers\Admin\VillageProfileController::class, 'statistik'])->name('statistik');
            Route::get('transparansi', [\App\Http\Controllers\Admin\VillageProfileController::class, 'transparansi'])->name('transparansi');
            Route::get('kontak', [\App\Http\Controllers\Admin\VillageProfileController::class, 'kontak'])->name('kontak');
            Route::get('lokasi', [\App\Http\Controllers\Admin\VillageProfileController::class, 'lokasi'])->name('lokasi');
            Route::get('footer', [\App\Http\Controllers\Admin\VillageProfileController::class, 'footer'])->name('footer');
            Route::post('update', [\App\Http\Controllers\Admin\VillageProfileController::class, 'update'])->name('update');
            
            // Single-item APBD endpoints for Modal UI
            Route::post('apbd/store', [\App\Http\Controllers\Admin\VillageProfileController::class, 'storeApbd'])->name('apbd.store');
            Route::put('apbd/{index}', [\App\Http\Controllers\Admin\VillageProfileController::class, 'updateApbd'])->name('apbd.update');
            Route::delete('apbd/{index}', [\App\Http\Controllers\Admin\VillageProfileController::class, 'destroyApbd'])->name('apbd.destroy');
            
            // Single-item Statistik endpoints for Modal UI
            Route::post('statistik/{type}/store', [\App\Http\Controllers\Admin\VillageProfileController::class, 'storeStatistik'])->name('statistik.store');
            Route::put('statistik/{type}/{index}', [\App\Http\Controllers\Admin\VillageProfileController::class, 'updateStatistik'])->name('statistik.update');
            Route::delete('statistik/{type}/{index}', [\App\Http\Controllers\Admin\VillageProfileController::class, 'destroyStatistik'])->name('statistik.destroy');
            
            Route::get('maklumat', [\App\Http\Controllers\Admin\KemitraanMaklumatController::class, 'maklumat'])->name('maklumat');
            Route::get('kemitraan', [\App\Http\Controllers\Admin\KemitraanMaklumatController::class, 'kemitraan'])->name('kemitraan');
            Route::post('update-kemitraan', [\App\Http\Controllers\Admin\KemitraanMaklumatController::class, 'update'])->name('update-kemitraan');
            
            // Single-item Kemitraan endpoints for Modal UI
            Route::post('kemitraan/store', [\App\Http\Controllers\Admin\KemitraanMaklumatController::class, 'storeMitra'])->name('kemitraan.store');
            Route::put('kemitraan/{index}', [\App\Http\Controllers\Admin\KemitraanMaklumatController::class, 'updateMitra'])->name('kemitraan.update');
            Route::delete('kemitraan/{index}', [\App\Http\Controllers\Admin\KemitraanMaklumatController::class, 'destroyMitra'])->name('kemitraan.destroy');
        });



        // Pelayanan: Master Layanan & Jenis Surat
        Route::prefix('jenis-layanan')->name('jenis-layanan.')->group(function () {
            Route::get('/', [AdminServiceTypeController::class, 'index'])->name('index');
            Route::post('/', [AdminServiceTypeController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminServiceTypeController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle', [AdminServiceTypeController::class, 'toggleStatus'])->name('toggle');
            Route::patch('/{id}/toggle-homepage', [AdminServiceTypeController::class, 'toggleHomepage'])->name('toggle-homepage');
            Route::delete('/{id}', [AdminServiceTypeController::class, 'destroy'])->name('destroy');
        });

        // Redirect legacy layanan-publik route
        Route::get('/layanan-publik', fn() => redirect()->route('admin.jenis-layanan.index'))->name('layanan-publik.index');



        // Pengguna: Operator & Hak Akses
        Route::prefix('operator')->name('operator.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminUserController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('toggle');
            Route::post('/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('reset-password');
            Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
        });

        // Pengaturan Identitas & Konfigurasi Sistem
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            Route::get('/', [AdminSettingController::class, 'index'])->name('index');
            Route::post('/', [AdminSettingController::class, 'update'])->name('update');
        });

        // Log Aktivitas Sistem
        Route::prefix('log-aktivitas')->name('activity-log.')->group(function () {
            Route::get('/', [AdminActivityLogController::class, 'index'])->name('index');
            Route::post('/clear', [AdminActivityLogController::class, 'clear'])->name('clear');
        });
    });

    // ==========================================
    // 2. STAFF PANEL ROUTES (Role: Staff)
    // ==========================================
    Route::middleware('staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [StaffDashboardController::class, 'index']);

    });
});