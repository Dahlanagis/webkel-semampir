<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterRequest;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Admin SIMPEL KELURAHAN.
     */
    public function index()
    {
        // 1. Agregasi Data Konten & Informasi
        $totalBerita = \App\Models\Post::count();
        $totalPengumuman = \App\Models\Announcement::count();
        $totalGaleri = \App\Models\Gallery::count();

        // 2. Agregasi Data Kependudukan
        $totalPenduduk = \App\Models\Resident::count();
        
        // 3. Ambil Berita Terbaru
        $latestPosts = \App\Models\Post::with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBerita',
            'totalPengumuman',
            'totalGaleri',
            'totalPenduduk',
            'latestPosts'
        ));
    }
}
