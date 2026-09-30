@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header-title', 'Panel Utama Kelurahan Semampir')
@section('header-subtitle', 'Pusat kendali informasi publik, pelayanan warga, dan berita wilayah')

@section('content')
<div class="space-y-5 sm:space-y-6">

    <!-- ========================================== -->
    <!-- 1. WELCOME HERO BANNER                     -->
    <!-- ========================================== -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-white p-5 sm:p-7 md:p-8 shadow-sm border border-slate-200">
        <div class="absolute -right-10 -bottom-10 opacity-40 pointer-events-none hidden sm:block">
            <svg class="w-80 h-80 sm:w-96 sm:h-96 text-slate-50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] sm:text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Portal Administrator Kelurahan Semampir</span>
                </div>
                <h2 class="text-lg sm:text-xl md:text-2xl font-black text-slate-900 tracking-tight leading-snug">
                    Selamat Datang Kembali, {{ Auth::user()->name ?? 'Administrator' }}!
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed">
                    Kelola berita kegiatan, galeri foto, pelayanan warga, dan informasi publik Kelurahan Semampir dengan mudah dan cepat.
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('admin.berita.index') }}" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Kelola Berita</span>
                </a>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- 2. SUMMARY STATISTICAL CARDS (3 GRID)      -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

        <!-- CARD 2: Total Berita -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider">Artikel & Berita</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                        {{ number_format($totalBerita ?? 0) }}
                    </h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-slate-50 text-slate-600 border border-slate-200 flex items-center justify-center font-bold shadow-inner shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
            </div>
            <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-800 font-medium">Informasi Web</span>
                <a href="{{ route('admin.berita.index') }}" class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold border border-slate-200 hover:bg-slate-200 transition">Lihat Data</a>
            </div>
        </div>

        <!-- CARD 3: Pengumuman -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-amber-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider">Pengumuman</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-amber-900 mt-1">
                        {{ number_format($totalPengumuman ?? 0) }}
                    </h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold shadow-inner shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
            </div>
            <div class="mt-3.5 pt-3 border-t border-amber-100 flex items-center justify-between text-[11px]">
                <span class="text-amber-800 font-medium">Running Text</span>
                <a href="{{ route('admin.pengumuman.index') }}" class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold border border-amber-200 hover:bg-amber-200 transition">Kelola</a>
            </div>
        </div>

        <!-- CARD 4: Galeri -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-sky-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] sm:text-xs font-bold text-sky-700 uppercase tracking-wider">Galeri Dokumentasi</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-sky-950 mt-1">
                        {{ number_format($totalGaleri ?? 0) }}
                    </h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center font-bold shadow-inner shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="mt-3.5 pt-3 border-t border-sky-100 flex items-center justify-between text-[11px]">
                <span class="text-sky-800 font-medium">Foto Terpublikasi</span>
                <a href="{{ route('admin.galeri.index') }}" class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-bold border border-sky-200 hover:bg-sky-200 transition">Lihat Galeri</a>
            </div>
        </div>

    </div>


    <!-- ========================================== -->
    <!-- 3. TABLE: RECENT POSTS                     -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden" id="berita-terbaru">
        
        <!-- Table Header & Filter Title -->
        <div class="p-4 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-slate-50/60">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Artikel & Berita Terbaru</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar publikasi berita dan artikel yang baru saja ditambahkan</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.berita.index') }}" class="px-3 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold shadow-sm transition whitespace-nowrap">
                    Lihat Semua Berita
                </a>
            </div>
        </div>

        <!-- Table Responsive Wrapper -->
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5">Judul Berita</th>
                        <th class="py-3.5 px-4 sm:px-5">Kategori</th>
                        <th class="py-3.5 px-4 sm:px-5">Tgl Publikasi</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($latestPosts as $post)
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            <!-- Judul Berita -->
                            <td class="py-3.5 px-4 sm:px-5">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm">
                                    {{ Str::limit($post->title, 50) }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $post->views ?? 0 }} kali dilihat
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap">
                                <span class="font-semibold text-slate-800">
                                    {{ $post->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>

                            <!-- Tanggal Publikasi -->
                            <td class="py-3.5 px-4 sm:px-5 text-slate-600 whitespace-nowrap">
                                {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 sm:px-5 text-center whitespace-nowrap">
                                @if($post->published_at)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                        <span>Terbit</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Draft</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap">
                                <a href="{{ route('admin.berita.index') }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-[11px] rounded-lg shadow-sm transition border border-slate-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    <span>Lihat/Edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                <p class="font-semibold text-slate-600">Belum ada berita yang diterbitkan.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Mulai publikasikan berita atau artikel baru untuk warga.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>Menampilkan <strong>{{ count($latestPosts) }}</strong> berita terbaru</span>
            <span class="text-[11px] text-slate-400 italic">Data diperbarui secara otomatis secara real-time</span>
        </div>

    </div>

</div>
@endsection
