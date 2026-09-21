@extends('layouts.app')

@section('title', 'Statistik Wilayah & Transparansi APBD - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-6 sm:py-10 bg-slate-50 min-h-screen text-slate-800"
         x-data="{
            activeTab: 'kependudukan', // 'kependudukan', 'anggaran', 'wilayah', 'pelayanan'
            year: '2026',
            printPage() {
                window.print();
            }
         }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- ========================================================================= -->
        <!-- HEADER HALAMAN & TOOLBAR AKSI                                             -->
        <!-- ========================================================================= -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>DATA SATU PINTU TERPADU</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Statistik Wilayah & Transparansi Publik</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Portal data terbuka demografi kependudukan, tata kelola anggaran APBD, dan capaian kinerja Kelurahan Patokan.</p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="printPage()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl font-bold text-xs transition shadow-sm">
                    <i class="fas fa-print"></i>
                    <span>Cetak Data</span>
                </button>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Beranda</span>
                </a>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- KARTU INDIKATOR UTAMA (5 KARTU SEJAJAR RAPI)                              -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5 sm:gap-4 items-stretch">
            <!-- 1. Penduduk -->
            <div @click="activeTab = 'kependudukan'"
                 :class="activeTab === 'kependudukan' ? 'ring-2 ring-emerald-500 border-emerald-500 shadow-md' : 'border-slate-200/80'"
                 class="bg-white border rounded-2xl p-4 transition-all cursor-pointer hover:shadow-md flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base group-hover:bg-emerald-600 group-hover:text-white transition">
                        <i class="fas fa-users"></i>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+1.2% thn ini</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $villageProfile['stats']['penduduk'] ?? '0' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-1">Total Penduduk</div>
                </div>
            </div>

            <!-- 2. KK -->
            <div @click="activeTab = 'kependudukan'"
                 :class="activeTab === 'kependudukan' ? 'ring-2 ring-emerald-500 border-emerald-500 shadow-md' : 'border-slate-200/80'"
                 class="bg-white border rounded-2xl p-4 transition-all cursor-pointer hover:shadow-md flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-base group-hover:bg-sky-600 group-hover:text-white transition">
                        <i class="fas fa-address-card"></i>
                    </span>
                    <span class="text-[10px] font-bold text-slate-400">SIAK Terpadu</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $villageProfile['stats']['kk'] ?? '0' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-1">Kepala Keluarga</div>
                </div>
            </div>

            <!-- 3. RT/RW -->
            <div @click="activeTab = 'wilayah'"
                 :class="activeTab === 'wilayah' ? 'ring-2 ring-emerald-500 border-emerald-500 shadow-md' : 'border-slate-200/80'"
                 class="bg-white border rounded-2xl p-4 transition-all cursor-pointer hover:shadow-md flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base group-hover:bg-indigo-600 group-hover:text-white transition">
                        <i class="fas fa-map-signs"></i>
                    </span>
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">08 RW</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $villageProfile['territory']['rt'] ?? '0' }} RT</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-1">Struktur Wilayah</div>
                </div>
            </div>

            <!-- 4. Luas Wilayah -->
            <div @click="activeTab = 'wilayah'"
                 :class="activeTab === 'wilayah' ? 'ring-2 ring-emerald-500 border-emerald-500 shadow-md' : 'border-slate-200/80'"
                 class="bg-white border rounded-2xl p-4 transition-all cursor-pointer hover:shadow-md flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-base group-hover:bg-teal-600 group-hover:text-white transition">
                        <i class="fas fa-chart-area"></i>
                    </span>
                    <span class="text-[10px] font-bold text-slate-400">Kraksaan</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $villageProfile['stats']['luas'] ?? '0' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-1">Luas Wilayah</div>
                </div>
            </div>

            <!-- 5. Anggaran (Hapus col-span agar ukurannya seragam dengan 4 kartu lainnya) -->
            <div @click="activeTab = 'anggaran'"
                 :class="activeTab === 'anggaran' ? 'ring-2 ring-amber-500 border-amber-500 shadow-md' : 'border-amber-200'"
                 class="bg-amber-50/50 border rounded-2xl p-4 transition-all cursor-pointer hover:shadow-md flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-base group-hover:bg-amber-500 group-hover:text-white transition border border-amber-300/40">
                        <i class="fas fa-coins"></i>
                    </span>
                    <span class="text-[10px] font-extrabold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-full">TA {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                </div>
                <div>
                    <div class="text-base sm:text-lg lg:text-xl font-black text-amber-700 tracking-tight truncate">Rp {{ $villageProfile['apbd']['total_budget'] ?? '0' }}</div>
                    <div class="text-[11px] font-extrabold text-amber-800/80 uppercase mt-1">Pagu Anggaran</div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB SELECTOR NAVIGATION                                                   -->
        <!-- ========================================================================= -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto custom-scrollbar text-xs font-bold">
            <button type="button" @click="activeTab = 'kependudukan'"
                    :class="activeTab === 'kependudukan' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-users"></i>
                <span>Demografi Penduduk</span>
            </button>
            <button type="button" @click="activeTab = 'anggaran'"
                    :class="activeTab === 'anggaran' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Transparansi Anggaran (APBD)</span>
            </button>
            <button type="button" @click="activeTab = 'wilayah'"
                    :class="activeTab === 'wilayah' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-map-marked-alt"></i>
                <span>Profil Wilayah & Sarpras</span>
            </button>
            <button type="button" @click="activeTab = 'pelayanan'"
                    :class="activeTab === 'pelayanan' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-chart-line"></i>
                <span>Capaian Layanan Publik</span>
            </button>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: DEMOGRAFI & KEPENDUDUKAN                                           -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'kependudukan'" class="space-y-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Rasio Jenis Kelamin -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                            <i class="fas fa-venus-mars text-emerald-500"></i>
                            <span>Komposisi Gender</span>
                        </h3>
                        <span class="text-[11px] text-slate-400">Total: {{ $villageProfile['demographics']['total'] ?? '0' }} Jiwa</span>
                    </div>

                    <!-- Progress Bar Perbandingan -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-blue-600 flex items-center gap-1.5"><i class="fas fa-mars"></i> Laki-Laki ({{ $villageProfile['demographics']['male'] ?? '0' }})</span>
                            <span class="font-bold text-pink-600 flex items-center gap-1.5">Perempuan ({{ $villageProfile['demographics']['female'] ?? '0' }}) <i class="fas fa-venus"></i></span>
                        </div>
                    </div>

                    <!-- Kartu Ringkasan Kependudukan -->
                    <div class="grid grid-cols-2 gap-2.5 pt-2 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Kepadatan</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $villageProfile['demographics']['density'] ?? '0' }} jiwa/km²</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Rata-rata/KK</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $villageProfile['demographics']['avg_family_size'] ?? '0' }} Jiwa/Rumah</span>
                        </div>
                    </div>
                </div>

                <!-- Kelompok Usia Penduduk -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4 lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                            <i class="fas fa-layer-group text-emerald-500"></i>
                            <span>Distribusi Kelompok Usia Penduduk</span>
                        </h3>
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Produktif Tinggi</span>
                    </div>

                    <div class="space-y-3 pt-1">
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-slate-700 mb-1">
                                <span>Usia Produktif (15 - 64 Tahun)</span>
                                <span>{{ $villageProfile['demographics']['productive_count'] ?? '0' }} ({{ $villageProfile['demographics']['productive_pct'] ?? '0' }}%)</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $villageProfile['demographics']['productive_pct'] ?? '0' }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold text-slate-700 mb-1">
                                <span>Anak-anak & Remaja (0 - 14 Tahun)</span>
                                <span>{{ $villageProfile['demographics']['child_count'] ?? '0' }} ({{ $villageProfile['demographics']['child_pct'] ?? '0' }}%)</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-sky-500 h-full rounded-full" style="width: {{ $villageProfile['demographics']['child_pct'] ?? '0' }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold text-slate-700 mb-1">
                                <span>Lanjut Usia (65+ Tahun)</span>
                                <span>{{ $villageProfile['demographics']['elderly_count'] ?? '0' }} ({{ $villageProfile['demographics']['elderly_pct'] ?? '0' }}%)</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: {{ $villageProfile['demographics']['elderly_pct'] ?? '0' }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Mata Pencaharian & Agama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pekerjaan Utama -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2">Mata Pencaharian Utama Warga</h3>
                    <div class="space-y-2 text-xs">
                        @foreach($villageProfile['demographics']['occupations'] ?? [] as $occ)
                        <div class="flex justify-between py-1.5 border-b border-slate-50 last:border-0">
                            <span class="text-slate-600 truncate pr-2">{{ $occ['name'] }}</span>
                            <span class="font-bold text-slate-900">{{ $occ['count'] }} ({{ $occ['pct'] }}%)</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tingkat Pendidikan -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2">Tingkat Pendidikan Terakhir</h3>
                    <div class="space-y-2 text-xs">
                        @foreach($villageProfile['demographics']['educations'] ?? [] as $edu)
                        <div class="flex justify-between py-1.5 border-b border-slate-50 last:border-0">
                            <span class="text-slate-600 truncate pr-2">{{ $edu['name'] }}</span>
                            <span class="font-bold text-slate-900">{{ $edu['count'] }} ({{ $edu['pct'] }}%)</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: TRANSPARANSI ANGGARAN & APBD (2026)                                -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'anggaran'" class="space-y-6" x-cloak>
            
            <!-- Banner Ringkasan Realisasi APBD -->
            <div class="bg-gradient-to-br from-amber-500/10 via-amber-50 to-white border border-amber-200 rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left">
                    <span class="px-2.5 py-0.5 rounded bg-amber-500 text-white font-black text-[10px] uppercase tracking-wider">APBD-KEL TA {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Total Pagu: Rp {{ $villageProfile['apbd']['total_budget'] ?? '0' }}</h2>
                    <p class="text-xs text-slate-600 max-w-xl">Alokasi anggaran kelurahan tahun {{ $villageProfile['apbd']['year'] ?? '2026' }} berfokus pada perbaikan sarana, pemberdayaan, dan layanan publik.</p>
                </div>
                
                <!-- Realisasi Widget -->
                <div class="bg-white p-4 rounded-2xl border border-amber-200 shadow-sm text-center shrink-0 w-full sm:w-auto">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Realisasi Hingga Saat Ini</span>
                    <span class="text-2xl font-black text-emerald-600 mt-0.5 block">Rp {{ $villageProfile['apbd']['realized_budget'] ?? '0' }}</span>
                    <span class="text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full inline-block mt-1">Tercapai {{ $villageProfile['apbd']['realized_pct'] ?? '0' }}%</span>
                </div>
            </div>

            <!-- Pos Alokasi Pagu Anggaran -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">Rincian Pos Belanja & Pembangunan</h3>
                    <span class="text-xs text-slate-400">Tahun Anggaran {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                </div>

                <div class="space-y-5">
                    @foreach($villageProfile['apbd']['allocations'] ?? [] as $index => $alloc)
                    <div class="space-y-1.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-1">
                            <span class="font-bold text-slate-800">{{ $index + 1 }}. {{ $alloc['name'] }}</span>
                            <span class="font-black text-slate-900">Rp {{ $alloc['amount'] }} <span class="text-slate-400 font-normal">({{ $alloc['pct'] }}%)</span></span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                            <div class="bg-{{ ['emerald','sky','amber','indigo','pink'][$index % 5] }}-500 h-full rounded-full" style="width: {{ $alloc['pct'] }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-500">{{ $alloc['desc'] }}</p>
                    </div>
                    @endforeach
                </div>

                <!-- Tombol Unduh Dokumen Pendukung -->
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-slate-500 italic">Dokumen LAKIP dan RKA lengkap tersedia dalam format publik resmi.</p>
                    <a href="{{ asset('docs/transparansi-apbd.pdf') }}" target="_blank" download class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span>Unduh Dokumen LAKIP APBD (PDF)</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- TAB 3: PROFIL WILAYAH, RT/RW, & SARPRAS                                   -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'wilayah'" class="space-y-6" x-cloak>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Batas Wilayah Administrasi -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fas fa-compass text-emerald-500"></i>
                        <span>Batas Wilayah Administrasi</span>
                    </h3>
                    <div class="grid grid-cols-1 gap-2 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500">UTARA</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['north'] ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500">TIMUR</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['east'] ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500">SELATAN</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['south'] ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500">BARAT</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['west'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas & Sarana Prasarana -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4 lg:col-span-2">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fas fa-building text-emerald-500"></i>
                        <span>Inventaris Sarana & Prasarana Umum</span>
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="p-3.5 bg-emerald-50/60 rounded-xl border border-emerald-100">
                            <i class="fas fa-school text-emerald-600 text-lg mb-1 block"></i>
                            <span class="text-xl font-black text-slate-900 block">{{ $villageProfile['territory']['schools'] ?? '0' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Sekolah (SD/SMP/SMA)</span>
                        </div>
                        <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-100">
                            <i class="fas fa-mosque text-blue-600 text-lg mb-1 block"></i>
                            <span class="text-xl font-black text-slate-900 block">{{ $villageProfile['territory']['mosques'] ?? '0' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Masjid & Musholla</span>
                        </div>
                        <div class="p-3.5 bg-pink-50/60 rounded-xl border border-pink-100">
                            <i class="fas fa-hospital-alt text-pink-600 text-lg mb-1 block"></i>
                            <span class="text-xl font-black text-slate-900 block">{{ $villageProfile['territory']['health'] ?? '0' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Posyandu & Polindes</span>
                        </div>
                        <div class="p-3.5 bg-amber-50/60 rounded-xl border border-amber-100">
                            <i class="fas fa-store text-amber-600 text-lg mb-1 block"></i>
                            <span class="text-xl font-black text-slate-900 block">{{ $villageProfile['territory']['markets'] ?? '0' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Pasar Tradisional</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs text-slate-600 leading-relaxed">
                        <span class="font-bold text-slate-800 block mb-1">Informasi Pemekaran Lingkungan:</span>
                        Kelurahan terbagi ke dalam <strong>{{ $villageProfile['territory']['rw'] ?? '0' }} Rukun Warga (RW)</strong> dan <strong>{{ $villageProfile['territory']['rt'] ?? '0' }} Rukun Tetangga (RT)</strong> yang aktif menyelenggarakan rembug warga dan kegiatan poskamling swakarsa secara berkesinambungan.
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- TAB 4: CAPAIAN LAYANAN PUBLIK                                             -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'pelayanan'" class="space-y-6" x-cloak>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm text-center">
                    <span class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center mx-auto mb-2 font-bold">
                        <i class="fas fa-clock"></i>
                    </span>
                    <span class="text-2xl font-black text-slate-900 block">{{ $villageProfile['service_metrics']['avg_time'] ?? '< 15 Menit' }}</span>
                    <span class="text-xs text-slate-500 font-medium">Rata-rata Waktu Pelayanan</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm text-center">
                    <span class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-2 font-bold">
                        <i class="fas fa-star"></i>
                    </span>
                    <span class="text-2xl font-black text-slate-900 block">{{ $villageProfile['service_metrics']['ikm_score'] ?? '98.4%' }}</span>
                    <span class="text-xs text-slate-500 font-medium">Indeks Kepuasan Masyarakat (IKM)</span>
                </div>
            </div>

            <!-- Ajakan Menuju Standar Pelayanan Publik -->
            <div class="bg-emerald-600 text-white rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold text-base mb-1">Perlu Mengurus Surat Keterangan atau Dokumen Administrasi?</h4>
                    <p class="text-xs text-emerald-100">Lihat standar persyaratan, alur pelayanan, dan dokumen SOP resmi Kelurahan Patokan.</p>
                </div>
                <a href="{{ route('standar-pelayanan') }}" class="px-5 py-2.5 bg-white hover:bg-slate-100 text-emerald-800 font-bold text-xs rounded-xl transition shrink-0">
                    Buka Standar Pelayanan & SOP &rarr;
                </a>
            </div>

        </div>

    </div>
</section>

<style>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    height: 4px;
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
</style>

@endsection