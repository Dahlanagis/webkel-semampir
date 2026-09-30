@extends('layouts.app')

@section('title', 'Statistik Kependudukan, Bansos & APBDes - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-6 sm:py-10 bg-slate-50 min-h-screen text-slate-800"
         x-data="{
            activeTab: 'bansos', // 'bansos', 'apbdes', 'kependudukan', 'wilayah'
            expandedYear: 2026,
            bansosCategory: 'all',
            previewModalOpen: false,
            previewDocTitle: '',
            previewDocYear: '',
            toggleYear(year) {
                this.expandedYear = this.expandedYear === year ? null : year;
            },
            openPdfPreview(title, year) {
                this.previewDocTitle = title;
                this.previewDocYear = year;
                this.previewModalOpen = true;
            },
            printPage() {
                window.print();
            }
         }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- ========================================================================= -->
        <!-- 1. HEADER HALAMAN & TOOLBAR DATA TERPADU (PREMIUM DARK EXECUTIVE HERO)     -->
        <!-- ========================================================================= -->
        <div class="relative bg-gradient-to-br from-slate-950 via-[#0a1324] to-slate-900 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-slate-800/80 overflow-hidden">
            <!-- Ambient Glowing Light Orbs -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none opacity-60"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3.5 max-w-3xl">
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-emerald-300 text-xs font-black tracking-wide shadow-inner">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>PORTAL DATA SATU PINTU & KETERBUKAAN INFORMASI PUBLIK</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Portal Transparansi & <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">Anggaran Kelurahan</span>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-medium">
                        Pusat data terpadu Pemerintah {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}, Kecamatan {{ $villageProfile['subdistrict'] ?? 'Kraksaan' }}. Menyajikan indikator demografi kependudukan, akuntabilitas realisasi Anggaran Kelurahan (APBD), serta keterbukaan penyaluran Bantuan Sosial (Bansos) tepat sasaran.
                    </p>

                    <!-- Trust & Compliance Micro Badges -->
                    <div class="flex flex-wrap items-center gap-3 pt-1 text-[11px] font-bold text-slate-300">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-emerald-300">
                            <i class="fas fa-shield-halved text-xs"></i>
                            <span>Laporan Terverifikasi Akuntabel</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-sky-300">
                            <i class="fas fa-database text-xs"></i>
                            <span>Data Bansos Terpadu</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-amber-300">
                            <i class="fas fa-check-double text-xs"></i>
                            <span>Tahun Anggaran {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap lg:flex-col sm:flex-row items-stretch gap-2.5 shrink-0">
                    <button type="button" @click="printPage()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-black text-xs transition backdrop-blur-md border border-white/15 shadow-sm active:scale-95">
                        <i class="fas fa-print text-emerald-400"></i>
                        <span>Cetak Laporan</span>
                    </button>
                    <a href="{{ asset('docs/transparansi-apbd.pdf') }}" download
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 rounded-2xl font-black text-xs transition shadow-lg shadow-amber-500/20 active:scale-95">
                        <i class="fas fa-file-pdf"></i>
                        <span>Unduh APBD (PDF)</span>
                    </a>
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-800/90 hover:bg-slate-700/90 text-slate-200 rounded-2xl font-bold text-xs transition border border-slate-700/80 active:scale-95">
                        <i class="fas fa-arrow-left text-slate-400"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. DASHBOARD METRICS CARDS (5 KARTU INDIKATOR UTAMA)                       -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4 items-stretch">
            
            <!-- 1. Total Penduduk -->
            <div @click="activeTab = 'kependudukan'"
                 :class="activeTab === 'kependudukan' ? 'ring-2 ring-emerald-500 border-emerald-500 shadow-xl -translate-y-1' : 'border-slate-200/90 hover:border-emerald-300'"
                 class="bg-white border rounded-3xl p-5 transition-all duration-300 cursor-pointer hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl pointer-events-none group-hover:bg-emerald-500/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-100 text-emerald-600 border border-emerald-200/80 flex items-center justify-center font-bold text-xl group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
                        <i class="fas fa-users"></i>
                    </span>
                    <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/80 shadow-2xs">+1.2% thn ini</span>
                </div>
                <div class="relative z-10">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $villageProfile['stats']['penduduk'] ?? '5.000' }}</div>
                    <div class="text-[11px] font-black text-slate-600 uppercase tracking-wider mt-1">Total Penduduk</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $villageProfile['demographics']['density'] ?? '1.308,9' }} Jiwa / km²</div>
                </div>
            </div>

            <!-- 2. Kepala Keluarga -->
            <div @click="activeTab = 'kependudukan'"
                 :class="activeTab === 'kependudukan' ? 'ring-2 ring-sky-500 border-sky-500 shadow-xl -translate-y-1' : 'border-slate-200/90 hover:border-sky-300'"
                 class="bg-white border rounded-3xl p-5 transition-all duration-300 cursor-pointer hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-sky-500/5 rounded-full blur-xl pointer-events-none group-hover:bg-sky-500/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-50 to-blue-100 text-sky-600 border border-sky-200/80 flex items-center justify-center font-bold text-xl group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all shadow-sm">
                        <i class="fas fa-address-card"></i>
                    </span>
                    <span class="text-[10px] font-black text-sky-700 bg-sky-50 px-2.5 py-1 rounded-full border border-sky-200/80 shadow-2xs">SIAK Terpadu</span>
                </div>
                <div class="relative z-10">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $villageProfile['stats']['kk'] ?? '2.640' }}</div>
                    <div class="text-[11px] font-black text-slate-600 uppercase tracking-wider mt-1">Kepala Keluarga</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Rata-rata {{ $villageProfile['demographics']['avg_family_size'] ?? '1,89' }} Jiwa/KK</div>
                </div>
            </div>

            <!-- 3. Rasio Gender (Laki / Perempuan) -->
            <div @click="activeTab = 'kependudukan'"
                 :class="activeTab === 'kependudukan' ? 'ring-2 ring-indigo-500 border-indigo-500 shadow-xl -translate-y-1' : 'border-slate-200/90 hover:border-indigo-300'"
                 class="bg-white border rounded-3xl p-5 transition-all duration-300 cursor-pointer hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl pointer-events-none group-hover:bg-indigo-500/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-50 to-purple-100 text-indigo-600 border border-indigo-200/80 flex items-center justify-center font-bold text-xl group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                        <i class="fas fa-venus-mars"></i>
                    </span>
                    <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-200/80 shadow-2xs">49.6% : 50.4%</span>
                </div>
                <div class="relative z-10">
                    <div class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-baseline gap-1.5">
                        <span class="text-blue-600">{{ $villageProfile['demographics']['male'] ?? '4.180' }}</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-pink-600">{{ $villageProfile['demographics']['female'] ?? '4.245' }}</span>
                    </div>
                    <div class="text-[11px] font-black text-slate-600 uppercase tracking-wider mt-1">Laki-laki / Perempuan</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Keseimbangan gender stabil</div>
                </div>
            </div>

            <!-- 4. Usia Kerja Produktif (15-64 Thn) -->
            <div @click="activeTab = 'kependudukan'"
                 :class="activeTab === 'kependudukan' ? 'ring-2 ring-teal-500 border-teal-500 shadow-xl -translate-y-1' : 'border-slate-200/90 hover:border-teal-300'"
                 class="bg-white border rounded-3xl p-5 transition-all duration-300 cursor-pointer hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-teal-500/5 rounded-full blur-xl pointer-events-none group-hover:bg-teal-500/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-50 to-emerald-100 text-teal-600 border border-teal-200/80 flex items-center justify-center font-bold text-xl group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all shadow-sm">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    <span class="text-[10px] font-black text-teal-800 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-200/80 shadow-2xs">{{ $villageProfile['demographics']['productive_pct'] ?? '66.6' }}% Porsi</span>
                </div>
                <div class="relative z-10">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $villageProfile['demographics']['productive_count'] ?? '5.610' }}</div>
                    <div class="text-[11px] font-black text-slate-600 uppercase tracking-wider mt-1">Usia Kerja (15-64 Thn)</div>
                    <div class="text-[10px] text-teal-600 font-bold mt-0.5">Bonus Demografi Tinggi</div>
                </div>
            </div>

            <!-- 5. WAJIB: Penerima Bantuan Sosial (Metrik Khusus) -->
            <div @click="activeTab = 'bansos'"
                 :class="activeTab === 'bansos' ? 'ring-2 ring-emerald-500 border-emerald-500 shadow-2xl -translate-y-1' : 'border-emerald-300/80 hover:border-emerald-500'"
                 class="bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-700 text-white border rounded-3xl p-5 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:-translate-y-1 flex flex-col justify-between group col-span-2 sm:col-span-1 shadow-lg shadow-emerald-700/20 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center font-bold text-xl backdrop-blur-md border border-white/20 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-hand-holding-heart"></i>
                    </span>
                    <span class="text-[10px] font-black text-emerald-950 bg-emerald-200 px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-700 animate-ping"></span>
                        <span>Bansos Aktif</span>
                    </span>
                </div>
                <div class="relative z-10">
                    <div class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-baseline gap-1">
                        <span>{{ $socialAssistance['summary']['total_kpm'] ?? '842' }}</span>
                        <span class="text-xs font-bold text-emerald-200">KPM</span>
                    </div>
                    <div class="text-[11px] font-black text-emerald-100 uppercase tracking-wider mt-1">Penerima Bantuan Sosial</div>
                    <div class="text-[10px] text-emerald-200 font-medium mt-0.5">
                        {{ $socialAssistance['summary']['persen_kk'] ?? '31.9%' }} dari Total KK Desa
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 3. NAVIGATION TABS (MODULAR LUXURY FLOATING BAR)                           -->
        <!-- ========================================================================= -->
        <div class="bg-white p-2 sm:p-2.5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto no-scrollbar text-xs font-extrabold">
            
            <!-- Tab: Bantuan Sosial (Prioritas Utama) -->
            <button type="button" @click="activeTab = 'bansos'"
                    :class="activeTab === 'bansos' ? 'bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-md shadow-emerald-600/30' : 'bg-transparent text-slate-700 hover:bg-slate-100/80'"
                    class="px-5 py-3 rounded-2xl transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap shrink-0 group">
                <i class="fas fa-hand-holding-heart text-sm transition-transform group-hover:scale-110" :class="activeTab === 'bansos' ? 'text-white' : 'text-emerald-600'"></i>
                <span>Penerima Bantuan Sosial (Bansos)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black"
                      :class="activeTab === 'bansos' ? 'bg-emerald-950/40 text-emerald-100' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/60'">
                    {{ $socialAssistance['summary']['total_kpm'] ?? '842' }} KPM
                </span>
            </button>

            <!-- Tab: APBDes Per Tahun (Accordion List) -->
            <button type="button" @click="activeTab = 'apbdes'"
                    :class="activeTab === 'apbdes' ? 'bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-md shadow-emerald-600/30' : 'bg-transparent text-slate-700 hover:bg-slate-100/80'"
                    class="px-5 py-3 rounded-2xl transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap shrink-0 group">
                <i class="fas fa-file-invoice-dollar text-sm transition-transform group-hover:scale-110" :class="activeTab === 'apbdes' ? 'text-white' : 'text-emerald-600'"></i>
                <span>Anggaran Kelurahan (APBD)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black"
                      :class="activeTab === 'apbdes' ? 'bg-emerald-950/40 text-emerald-100' : 'bg-slate-100 text-slate-700'">
                    2026 - 2024
                </span>
            </button>

            <!-- Tab: Demografi Kependudukan -->
            <button type="button" @click="activeTab = 'kependudukan'"
                    :class="activeTab === 'kependudukan' ? 'bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-md shadow-emerald-600/30' : 'bg-transparent text-slate-700 hover:bg-slate-100/80'"
                    class="px-5 py-3 rounded-2xl transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap shrink-0 group">
                <i class="fas fa-chart-pie text-sm transition-transform group-hover:scale-110" :class="activeTab === 'kependudukan' ? 'text-white' : 'text-emerald-600'"></i>
                <span>Demografi Kependudukan</span>
            </button>

            <!-- Tab: Wilayah & Sarpras -->
            <button type="button" @click="activeTab = 'wilayah'"
                    :class="activeTab === 'wilayah' ? 'bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-md shadow-emerald-600/30' : 'bg-transparent text-slate-700 hover:bg-slate-100/80'"
                    class="px-5 py-3 rounded-2xl transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap shrink-0 group">
                <i class="fas fa-map-marked-alt text-sm transition-transform group-hover:scale-110" :class="activeTab === 'wilayah' ? 'text-white' : 'text-emerald-600'"></i>
                <span>Profil Wilayah & Sarpras</span>
            </button>

        </div>

        <!-- ========================================================================= -->
        <!-- 4. TAB 1: METRIK KHUSUS PENERIMA BANTUAN SOSIAL (BANSOS)                  -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'bansos'" class="space-y-6" x-cloak>
            
            <!-- Banner Utama Bansos & 3 Ringkasan Utama (Ringkas & Bersih) -->
            <div class="bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-md relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold tracking-wide">
                            <i class="fas fa-shield-alt text-emerald-400"></i>
                            <span>BANTUAN SOSIAL KELURAHAN</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
                            Transparansi Bantuan Sosial
                        </h2>
                        <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed font-medium">
                            Penyaluran bantuan sosial tepat sasaran bagi warga Kelurahan Semampir tanpa potongan biaya apapun.
                        </p>
                    </div>

                    <!-- 3 Informasi Ringkasan Utama -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 shrink-0">
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                            <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider block">Total Penerima</span>
                            <span class="text-2xl sm:text-3xl font-black text-white mt-0.5 block">{{ $socialAssistance['summary']['total_kpm'] ?? '842' }}</span>
                            <span class="text-[11px] text-emerald-300 font-semibold block">Keluarga</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                            <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider block">Total Dana Tersalurkan</span>
                            <span class="text-xl sm:text-2xl font-black text-white mt-0.5 block">{{ $socialAssistance['summary']['total_anggaran_salur'] ?? 'Rp 1,84 Miliar' }}</span>
                            <span class="text-[11px] text-emerald-300 font-semibold block">Tahun Anggaran 2026</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                            <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider block">Sisa Kuota Tersedia</span>
                            <span class="text-2xl sm:text-3xl font-black text-white mt-0.5 block">{{ $socialAssistance['summary']['sisa_kuota'] ?? '48' }}</span>
                            <span class="text-[11px] text-emerald-300 font-semibold block">Dari total kuota {{ $socialAssistance['summary']['total_kuota'] ?? '890' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Kategori Bantuan Sosial -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Program:</span>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                    <button type="button" @click="bansosCategory = 'all'"
                            :class="bansosCategory === 'all' ? 'bg-emerald-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-xl font-bold text-xs transition whitespace-nowrap">
                        Semua Program ({{ count($socialAssistance['categories'] ?? []) }})
                    </button>
                    @foreach($socialAssistance['categories'] ?? [] as $bCategory)
                    <button type="button" @click="bansosCategory = '{{ $bCategory['code'] }}'"
                            :class="bansosCategory === '{{ $bCategory['code'] }}' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-xl font-bold text-xs transition whitespace-nowrap">
                        {{ $bCategory['code'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Grid Card Rincian Program Bantuan Sosial (BPNT, PKH, BLT, BST) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach($socialAssistance['categories'] ?? [] as $cat)
                <div x-show="bansosCategory === 'all' || bansosCategory === '{{ $cat['code'] }}'"
                     x-transition
                     x-data="{ showDetail: false }"
                     class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs hover:shadow-md transition-all space-y-4 flex flex-col justify-between">
                    
                    <div class="space-y-4">
                        <!-- Header Kartu Kategori -->
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3.5">
                            <div class="flex items-center gap-3">
                                <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-sm font-black shrink-0 bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                    {{ $cat['code'] }}
                                </span>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 leading-tight">
                                        {{ $cat['name'] }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $cat['tagline'] }}</p>
                                </div>
                            </div>

                            <!-- Badge Status Penyaluran -->
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider shrink-0 bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check-circle text-[9px] mr-1"></i>
                                {{ $cat['status'] }}
                            </span>
                        </div>

                        <!-- 4 Poin Sederhana Program Bansos -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                            <!-- 1. Nominal Bantuan -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nominal Bantuan</span>
                                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $cat['nominal'] }}</span>
                                </div>
                            </div>

                            <!-- 2. Jumlah Penerima -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jumlah Penerima</span>
                                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $cat['recipients'] }} dari {{ $cat['quota'] }} Keluarga</span>
                                </div>
                            </div>

                            <!-- 3. Jadwal & Lokasi Ambil -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2.5 sm:col-span-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                    <i class="fas fa-location-dot"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jadwal & Lokasi Ambil</span>
                                    <span class="font-bold text-slate-800 text-xs">{{ $cat['next_schedule'] }}</span>
                                </div>
                            </div>

                            <!-- 4. Syarat Utama (2 Poin Penting Bahasa Awam) -->
                            <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 sm:col-span-2 space-y-1">
                                <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider flex items-center gap-1">
                                    <i class="fas fa-clipboard-check text-emerald-600"></i>
                                    <span>Syarat Utama Penerima:</span>
                                </span>
                                <ul class="text-xs text-slate-700 space-y-1 pl-4 list-disc font-medium">
                                    @foreach($cat['main_criteria'] ?? array_slice($cat['criteria'], 0, 2) as $mCrit)
                                        <li>{{ $mCrit }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Visual Progress Bar Bersih (Satu Baris Sejajar) -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                                <span class="text-slate-500 font-medium">Realisasi Kuota Kelurahan</span>
                                <span class="text-emerald-700 font-extrabold">{{ $cat['recipients'] }} / {{ $cat['quota'] }} Kuota ({{ $cat['quota_pct'] }}%)</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-emerald-600 transition-all duration-500" style="width: {{ $cat['quota_pct'] }}%"></div>
                            </div>
                        </div>

                        <!-- Bagian Accordion / Detail Teknis Lengkap -->
                        <div x-show="showDetail" x-collapse class="pt-3 border-t border-slate-100 space-y-2.5 text-xs text-slate-600">
                            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-medium">Sumber Anggaran:</span>
                                <span class="font-bold text-slate-800">{{ $cat['source'] }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-medium">Tahap Terakhir Disalurkan:</span>
                                <span class="font-bold text-emerald-700">{{ $cat['disbursed_stage'] }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-500 font-medium">Kanal Pencairan:</span>
                                <span class="font-bold text-slate-800">{{ $cat['channel'] }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 bg-slate-50 p-2.5 rounded-lg border border-slate-100 leading-relaxed">
                                {{ $cat['description'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer Kartu dengan Tombol Accordion & Cek Mandiri -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 text-xs">
                        <button type="button" @click="showDetail = !showDetail"
                                class="inline-flex items-center gap-1.5 font-bold text-emerald-700 hover:text-emerald-800 transition py-1 cursor-pointer">
                            <span x-text="showDetail ? 'Tutup Detail Teknis' : 'Detail Lengkap & Sumber Anggaran'"></span>
                            <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''"></i>
                        </button>
                        <a href="https://cekbansos.kemensos.go.id" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 font-semibold text-slate-500 hover:text-slate-800">
                            <span>Cek Mandiri</span>
                            <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                        </a>
                    </div>

                </div>
                @endforeach
            </div>

            <!-- Posko Pengaduan & Layanan Verifikasi Bansos -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0 border border-emerald-200">
                        <i class="fas fa-bullhorn"></i>
                    </span>
                    <div>
                        <h4 class="text-base sm:text-lg font-black text-slate-900">Menemukan Ketidaksesuaian Data Bansos?</h4>
                        <p class="text-xs text-slate-500 mt-0.5 max-w-xl">
                            Warga dapat mengajukan sanggah atau usulan keluarga rentan baru melalui Posko Pengaduan di Kantor Kelurahan Semampir setiap hari kerja.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $villageProfile['whatsapp'] ?? '081234567890') }}?text={{ urlencode('Halo Admin Kelurahan Semampir, saya ingin menanyakan perihal data Bantuan Sosial (Bansos).') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-bold text-xs shadow-xs transition">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>Pengaduan Bansos (WhatsApp)</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 5. TAB 2: APBDes (FORMAT RIWAYAT PER TAHUN - ACCORDION LIST)             -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'apbdes'" class="space-y-6" x-cloak>
            
            <!-- Header Penjelasan APBDes (High-Impact Finance Header) -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-700/80 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[11px] font-black uppercase tracking-wider">
                            <i class="fas fa-scale-balanced text-emerald-400"></i>
                            <span>AKUNTABILITAS TATA KELOLA KEUANGAN KELURAHAN</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                            Riwayat Anggaran Kelurahan (APBD)
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Daftar transparansi Anggaran Kelurahan per tahun anggaran. Klik pada setiap tahun untuk membuka (*expand*) rincian Total Pendapatan, Belanja Bidang Wajib, dan mengunduh berkas laporan realisasi resmi format PDF.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span class="px-3.5 py-1.5 rounded-xl bg-white/10 text-emerald-300 text-xs font-black border border-white/10 shadow-sm backdrop-blur-md">
                            <i class="fas fa-calendar-check mr-1.5"></i>
                            <span>{{ count($apbdesHistory ?? []) }} Tahun Anggaran Tersedia</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- COLLAPSIBLE / ACCORDION LIST RIWAYAT PER TAHUN -->
            <div class="space-y-4">
                @foreach($apbdesHistory ?? [] as $history)
                <div class="bg-white rounded-3xl border transition-all duration-300 overflow-hidden shadow-sm hover:shadow-md
                    {{ $history['is_current'] ? 'border-emerald-300' : 'border-slate-200' }}">
                    
                    <!-- ACCORDION HEADER (BARIS TAHUN) -->
                    <div @click="toggleYear({{ $history['tahun'] }})"
                         class="p-5 sm:p-6 cursor-pointer select-none transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4
                         {{ $history['is_current'] ? 'bg-gradient-to-r from-emerald-50/50 via-teal-50/30 to-white hover:bg-emerald-50/80' : 'bg-slate-50/60 hover:bg-slate-100/70' }}">
                        
                        <!-- Info Tahun & Badge Status -->
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center font-black text-center shrink-0
                                {{ $history['is_current'] ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-white' }}">
                                <span class="text-[9px] uppercase tracking-wider opacity-80">TAHUN</span>
                                <span class="text-lg leading-none mt-0.5">{{ $history['tahun'] }}</span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg sm:text-xl font-black text-slate-900">
                                        Tahun Anggaran {{ $history['tahun'] }}
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                                        {{ $history['status_theme'] === 'emerald' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $history['status_theme'] === 'sky' ? 'bg-sky-100 text-sky-800' : '' }}
                                        {{ $history['status_theme'] === 'slate' ? 'bg-slate-200 text-slate-700' : '' }}">
                                        {{ $history['status_label'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">
                                    Audit Terakhir: <strong class="text-slate-700">{{ $history['last_audit'] ?? '-' }}</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Ringkasan Angka Cepat & Serapan Progress Bar -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:gap-6 shrink-0">
                            
                            <!-- Pendapatan & Belanja Singkat -->
                            <div class="grid grid-cols-2 gap-3 text-left sm:text-right border-y sm:border-y-0 sm:border-l border-slate-200/80 py-2 sm:py-0 sm:pl-4">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Pendapatan</span>
                                    <span class="text-xs sm:text-sm font-black text-emerald-800">
                                        Rp {{ number_format($history['total_pendapatan'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Belanja</span>
                                    <span class="text-xs sm:text-sm font-black text-slate-900">
                                        Rp {{ number_format($history['total_belanja'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Serapan Anggaran Meter -->
                            <div class="w-full sm:w-36 space-y-1">
                                <div class="flex justify-between text-[11px] font-bold">
                                    <span class="text-slate-500">Serapan:</span>
                                    <span class="text-emerald-700 font-black">{{ $history['serapan_pct'] }}%</span>
                                </div>
                                <div class="w-full h-2.5 rounded-full bg-slate-200/80 overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-600 transition-all duration-500"
                                         style="width: {{ $history['serapan_pct'] }}%"></div>
                                </div>
                            </div>

                            <!-- Tombol Toggle Chevron -->
                            <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 sm:pl-2">
                                <span class="hidden sm:inline" x-text="expandedYear === {{ $history['tahun'] }} ? 'Tutup' : 'Rincian'"></span>
                                <span class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 transition-transform duration-300 shadow-sm"
                                      :class="expandedYear === {{ $history['tahun'] }} ? 'rotate-180 bg-emerald-600 text-white border-emerald-600' : ''">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </span>
                            </div>

                        </div>

                    </div>

                    <!-- ACCORDION CONTENT (RINCIAN LENGKAP SAAT DI-EXPAND) -->
                    <div x-show="expandedYear === {{ $history['tahun'] }}"
                         x-collapse
                         class="p-6 sm:p-8 border-t border-slate-200/80 space-y-8 bg-white">
                        
                        <!-- 1. Ringkasan 4 Kartu KPI Keuangan -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <!-- 1. Pendapatan -->
                            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider">Total Pendapatan Desa</span>
                                    <i class="fas fa-arrow-down text-emerald-600 text-xs"></i>
                                </div>
                                <div class="text-lg sm:text-xl font-black text-emerald-950">
                                    Rp {{ number_format($history['total_pendapatan'], 0, ',', '.') }}
                                </div>
                                <p class="text-[10px] text-emerald-700 mt-1">PADes, Dana Desa, ADD & Bagi Hasil</p>
                            </div>

                            <!-- 2. Belanja (Pagu & Realisasi) -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Total Belanja Desa</span>
                                    <i class="fas fa-arrow-up text-slate-500 text-xs"></i>
                                </div>
                                <div class="text-lg sm:text-xl font-black text-slate-900">
                                    Rp {{ number_format($history['total_belanja'], 0, ',', '.') }}
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1">Realisasi: Rp {{ number_format($history['realisasi_belanja'], 0, ',', '.') }}</p>
                            </div>

                            <!-- 3. Surplus / Defisit -->
                            <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black text-sky-800 uppercase tracking-wider">Surplus / Defisit</span>
                                    <i class="fas fa-scale-balanced text-sky-600 text-xs"></i>
                                </div>
                                <div class="text-lg sm:text-xl font-black text-sky-950">
                                    Rp {{ number_format($history['surplus_defisit'], 0, ',', '.') }}
                                </div>
                                <p class="text-[10px] text-sky-700 mt-1">Selisih Pendapatan vs Belanja</p>
                            </div>

                            <!-- 4. SiLPA (Sisa Lebih Perhitungan Anggaran) -->
                            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black text-amber-800 uppercase tracking-wider">SiLPA Akhir Kas</span>
                                    <i class="fas fa-piggy-bank text-amber-600 text-xs"></i>
                                </div>
                                <div class="text-lg sm:text-xl font-black text-amber-950">
                                    Rp {{ number_format($history['silpa'], 0, ',', '.') }}
                                </div>
                                <p class="text-[10px] text-amber-700 mt-1">Tersimpan dalam Kas Kasir Resmi</p>
                            </div>
                        </div>

                        <!-- 2. Rincian Total Pendapatan Desa -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-wallet text-emerald-600"></i>
                                    <span>Rincian Sumber Pendapatan Desa (Kelompok 4)</span>
                                </h4>
                                <span class="text-xs font-bold text-emerald-800">
                                    Total: Rp {{ number_format($history['total_pendapatan'], 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                @foreach($history['pendapatan_items'] ?? [] as $pItem)
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                    <div class="flex items-start justify-between text-xs gap-2">
                                        <span class="font-bold text-slate-800">
                                            <span class="text-emerald-700 mr-1">[{{ $pItem['kode'] }}]</span>
                                            {{ $pItem['name'] }}
                                        </span>
                                        <span class="font-black text-slate-900 whitespace-nowrap">
                                            Rp {{ number_format($pItem['amount'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-[10px] text-slate-500 font-semibold">
                                            <span>Kontribusi dari Total Pendapatan</span>
                                            <span>{{ $pItem['pct'] }}%</span>
                                        </div>
                                        <div class="w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                            <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $pItem['pct'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3. Rincian Total Belanja Desa (4 Bidang Wajib Permendagri) -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-layer-group text-sky-600"></i>
                                    <span>Rincian Belanja Desa Berdasarkan 4 Bidang Wajib (Kelompok 5)</span>
                                </h4>
                                <span class="text-xs font-bold text-slate-700">
                                    Realisasi: Rp {{ number_format($history['realisasi_belanja'], 0, ',', '.') }} ({{ $history['serapan_pct'] }}%)
                                </span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                @foreach($history['belanja_bidang'] ?? [] as $bBidang)
                                <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-sm space-y-3.5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between gap-2 mb-1.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                                {{ $bBidang['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $bBidang['color'] === 'emerald' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $bBidang['color'] === 'amber' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $bBidang['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                                {{ $bBidang['kode'] }}
                                            </span>
                                            <span class="text-xs font-black text-emerald-700">
                                                Serapan {{ $bBidang['serapan'] }}%
                                            </span>
                                        </div>

                                        <h5 class="font-black text-slate-900 text-sm leading-snug">
                                            {{ $bBidang['bidang'] }}
                                        </h5>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                            {{ $bBidang['deskripsi'] }}
                                        </p>

                                        <!-- Nominal Pagu vs Realisasi -->
                                        <div class="grid grid-cols-2 gap-2 pt-3 text-xs">
                                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                                <span class="text-[10px] font-bold text-slate-400 block">PAGU ANGGARAN</span>
                                                <span class="font-black text-slate-800 block text-xs sm:text-sm">Rp {{ number_format($bBidang['pagu'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100">
                                                <span class="text-[10px] font-bold text-emerald-700 block">REALISASI</span>
                                                <span class="font-black text-emerald-900 block text-xs sm:text-sm">Rp {{ number_format($bBidang['realisasi'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <!-- Progress Bar Serapan Bidang -->
                                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden mt-3">
                                            <div class="h-full rounded-full transition-all duration-500
                                                {{ $bBidang['color'] === 'blue' ? 'bg-blue-600' : '' }}
                                                {{ $bBidang['color'] === 'emerald' ? 'bg-emerald-600' : '' }}
                                                {{ $bBidang['color'] === 'amber' ? 'bg-amber-600' : '' }}
                                                {{ $bBidang['color'] === 'indigo' ? 'bg-indigo-600' : '' }}"
                                                 style="width: {{ $bBidang['serapan'] }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Contoh Program Prioritas -->
                                    <div class="pt-3 border-t border-slate-100 text-xs text-slate-600 space-y-1">
                                        <span class="text-[10px] font-black uppercase text-slate-400 block">Realisasi Program Kerja Unggulan:</span>
                                        <ul class="space-y-1 pl-4 list-disc text-[11px]">
                                            @foreach($bBidang['programs'] as $prog)
                                            <li>{{ $prog }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 4. Rincian Pembiayaan & SiLPA (Kelompok 6) -->
                        <div class="bg-amber-50/40 rounded-2xl border border-amber-200/80 p-5 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-amber-200/60 pb-2">
                                <h4 class="font-black text-amber-900 flex items-center gap-2 text-sm">
                                    <i class="fas fa-coins text-amber-700"></i>
                                    <span>Pembiayaan Desa & Sisa Lebih Perhitungan Anggaran (SiLPA)</span>
                                </h4>
                                <span class="font-bold text-amber-800">Tahun Anggaran {{ $history['tahun'] }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="bg-white p-3 rounded-xl border border-amber-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Penerimaan Pembiayaan</span>
                                    <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($history['pembiayaan']['penerimaan'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-500 block">SiLPA Tahun Lalu</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-amber-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Pengeluaran Pembiayaan</span>
                                    <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($history['pembiayaan']['pengeluaran'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-500 block">Penyertaan Modal BUMDes</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-amber-200">
                                    <span class="text-[10px] font-black text-amber-800 uppercase block">SiLPA Kas Desa Berjalan</span>
                                    <span class="text-sm font-black text-amber-900 mt-0.5 block">Rp {{ number_format($history['pembiayaan']['silpa_tahun_berjalan'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-emerald-700 font-semibold block">Tercatat Kas Resmi</span>
                                </div>
                            </div>

                            <p class="text-[11px] text-amber-800/90 italic">
                                * {{ $history['pembiayaan']['silpa_desc'] }}
                            </p>
                        </div>

                        <!-- 5. TOMBOL CEPAT: Unduh Laporan Realisasi (PDF) & Serapan Bar -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg shrink-0 border border-red-100">
                                    <i class="fas fa-file-pdf"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">{{ $history['pdf_filename'] }}</span>
                                    <span class="text-[10px] text-slate-500 block">Dokumen Laporan Realisasi Pertanggungjawaban APBDes Resmi</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                                <button type="button"
                                        @click="openPdfPreview('Laporan Realisasi APBDes TA {{ $history['tahun'] }}', '{{ $history['tahun'] }}')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                    <i class="fas fa-download"></i>
                                    <span>Unduh Laporan Realisasi (PDF)</span>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
                @endforeach
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 6. TAB 3: DEMOGRAFI & KEPENDUDUKAN LENGKAP                                -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'kependudukan'" class="space-y-6" x-cloak>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Rasio Gender & Kepadatan -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                            <i class="fas fa-venus-mars text-indigo-600"></i>
                            <span>Komposisi Jenis Kelamin</span>
                        </h3>
                        <span class="text-xs font-bold text-slate-500">Total: {{ $villageProfile['demographics']['total'] ?? '8.425' }} Jiwa</span>
                    </div>

                    <!-- Progress Bar Perbandingan L/P -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-blue-600 flex items-center gap-1.5"><i class="fas fa-mars"></i> Laki-Laki ({{ $villageProfile['demographics']['male'] ?? '4.180' }})</span>
                            <span class="text-pink-600 flex items-center gap-1.5">Perempuan ({{ $villageProfile['demographics']['female'] ?? '4.245' }}) <i class="fas fa-venus"></i></span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-pink-100 overflow-hidden flex">
                            <div class="bg-blue-600 h-full" style="width: 49.6%"></div>
                            <div class="bg-pink-500 h-full" style="width: 50.4%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 font-semibold">
                            <span>49.6% Total Penduduk</span>
                            <span>50.4% Total Penduduk</span>
                        </div>
                    </div>

                    <!-- Ringkasan Kepadatan & Ukuran Keluarga -->
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs">
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Kepadatan Wilayah</span>
                            <span class="font-black text-slate-900 text-sm mt-0.5 block">{{ $villageProfile['demographics']['density'] ?? '3.438' }}</span>
                            <span class="text-[10px] text-slate-500">Jiwa / km²</span>
                        </div>
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Rata-rata/KK</span>
                            <span class="font-black text-slate-900 text-sm mt-0.5 block">{{ $villageProfile['demographics']['avg_family_size'] ?? '3.2' }}</span>
                            <span class="text-[10px] text-slate-500">Jiwa / Rumah Tangga</span>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Kelompok Usia (Produktif, Anak, Lansia) -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                            <i class="fas fa-layer-group text-teal-600"></i>
                            <span>Distribusi Kelompok Usia Penduduk</span>
                        </h3>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            Bonus Demografi Unggul
                        </span>
                    </div>

                    <div class="space-y-4 pt-1">
                        <!-- Usia Produktif / Kerja -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs font-bold text-slate-800">
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <span>Usia Kerja / Produktif (15 - 64 Tahun)</span>
                                </span>
                                <span>{{ $villageProfile['demographics']['productive_count'] ?? '5.610' }} Jiwa ({{ $villageProfile['demographics']['productive_pct'] ?? '66.6' }}%)</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $villageProfile['demographics']['productive_pct'] ?? '66.6' }}%"></div>
                            </div>
                        </div>

                        <!-- Anak-anak -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs font-bold text-slate-800">
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                                    <span>Anak-anak & Pelajar (0 - 14 Tahun)</span>
                                </span>
                                <span>{{ $villageProfile['demographics']['child_count'] ?? '1.825' }} Jiwa ({{ $villageProfile['demographics']['child_pct'] ?? '21.7' }}%)</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-sky-500 h-full rounded-full" style="width: {{ $villageProfile['demographics']['child_pct'] ?? '21.7' }}%"></div>
                            </div>
                        </div>

                        <!-- Lansia -->
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs font-bold text-slate-800">
                                <span class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    <span>Lanjut Usia (65+ Tahun)</span>
                                </span>
                                <span>{{ $villageProfile['demographics']['elderly_count'] ?? '990' }} Jiwa ({{ $villageProfile['demographics']['elderly_pct'] ?? '11.7' }}%)</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: {{ $villageProfile['demographics']['elderly_pct'] ?? '11.7' }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pekerjaan & Pendidikan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pekerjaan -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                <i class="fas fa-briefcase text-slate-700"></i>
                                <span>Mata Pencaharian & Sektor Profesi Warga</span>
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Distribusi penduduk usia kerja berdasarkan 7 kategori sektor utama</p>
                        </div>
                    </div>
                    <div class="space-y-2.5 text-xs">
                        @php
                            $pubSectorStyles = [
                                'Pertanian'     => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'bg-emerald-500'],
                                'Perikanan'     => ['badge' => 'bg-sky-50 text-sky-700 border-sky-200',         'bar' => 'bg-sky-500'],
                                'Perdagangan'   => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200',   'bar' => 'bg-amber-500'],
                                'Jasa'          => ['badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',  'bar' => 'bg-indigo-500'],
                                'PNS/TNI/Polri' => ['badge' => 'bg-purple-50 text-purple-700 border-purple-200',  'bar' => 'bg-purple-600'],
                                'Swasta'        => ['badge' => 'bg-blue-50 text-blue-700 border-blue-200',        'bar' => 'bg-blue-600'],
                                'Lainnya'       => ['badge' => 'bg-slate-100 text-slate-700 border-slate-200',   'bar' => 'bg-slate-600'],
                            ];
                        @endphp
                        @foreach($villageProfile['demographics']['occupations'] ?? [
                            ['name' => 'Karyawan Swasta & Pabrik Industri', 'sector' => 'Swasta', 'count' => '1.280', 'pct' => '25,6'],
                            ['name' => 'Pedagang Pasar & Pelaku UMKM Mikro', 'sector' => 'Perdagangan', 'count' => '1.150', 'pct' => '23,0'],
                            ['name' => 'Penyedia Jasa Logistik & Transportasi', 'sector' => 'Jasa', 'count' => '720', 'pct' => '14,4'],
                            ['name' => 'Petani Tanaman Pangan & Hortikultura', 'sector' => 'Pertanian', 'count' => '620', 'pct' => '12,4'],
                            ['name' => 'Aparatur Sipil Negara (ASN / TNI / POLRI)', 'sector' => 'PNS/TNI/Polri', 'count' => '450', 'pct' => '9,0'],
                            ['name' => 'Wiraswasta Mandiri & Freelance', 'sector' => 'Lainnya', 'count' => '400', 'pct' => '8,0'],
                            ['name' => 'Pembudidaya & Nelayan Tambak Pesisir', 'sector' => 'Perikanan', 'count' => '380', 'pct' => '7,6'],
                        ] as $occ)
                        @php
                            $secName = $occ['sector'] ?? 'Lainnya';
                            $secTheme = $pubSectorStyles[$secName] ?? $pubSectorStyles['Lainnya'];
                            $pctClean = (float) str_replace(',', '.', $occ['pct'] ?? '0');
                        @endphp
                        <div class="p-2.5 rounded-2xl bg-slate-50/70 border border-slate-100 hover:border-slate-200 transition space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $secTheme['badge'] }} shrink-0">
                                        {{ $secName }}
                                    </span>
                                    <span class="font-bold text-slate-800 truncate">{{ $occ['name'] }}</span>
                                </div>
                                <span class="font-extrabold text-slate-900 shrink-0">{{ $occ['count'] }} <span class="text-[10px] font-semibold text-slate-500">({{ $occ['pct'] }}%)</span></span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="{{ $secTheme['bar'] }} h-full rounded-full transition-all duration-500" style="width: {{ $pctClean }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pendidikan -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-3">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2.5 flex items-center justify-between">
                        <span>Tingkat Pendidikan Terakhir</span>
                        <i class="fas fa-graduation-cap text-slate-400"></i>
                    </h3>
                    <div class="space-y-2.5 text-xs">
                        @foreach($villageProfile['demographics']['educations'] ?? [
                            ['name' => 'Tamat SMA / SMK / Sederajat', 'count' => '3.240', 'pct' => '38.4'],
                            ['name' => 'Diploma / Sarjana (D3, S1, S2, S3)', 'count' => '1.890', 'pct' => '22.4'],
                            ['name' => 'Tamat SMP / Sederajat', 'count' => '1.620', 'pct' => '19.2'],
                            ['name' => 'Tamat SD / Sederajat', 'count' => '1.215', 'pct' => '14.4'],
                            ['name' => 'Belum / Tidak Sekolah', 'count' => '460', 'pct' => '5.6'],
                        ] as $edu)
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700">
                                <span class="font-medium">{{ $edu['name'] }}</span>
                                <span class="font-bold text-slate-900">{{ $edu['count'] }} ({{ $edu['pct'] }}%)</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $edu['pct'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 7. TAB 4: PROFIL WILAYAH, SARPRAS & RT/RW                                 -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'wilayah'" class="space-y-6" x-cloak>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Batas Wilayah Administrasi -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2.5 flex items-center gap-2">
                        <i class="fas fa-compass text-slate-500"></i>
                        <span>Batas Wilayah Administrasi</span>
                    </h3>
                    <div class="grid grid-cols-1 gap-2.5 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Utara</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['north'] ?? 'Desa Kalibuntu' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Timur</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['east'] ?? 'Kelurahan Kraksaan Wetan' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Selatan</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['south'] ?? 'Desa Alassumur Kulon' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Barat</span>
                            <span class="font-semibold text-slate-800">{{ $villageProfile['territory']['west'] ?? 'Desa Sidomukti' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sarana & Prasarana Fasilitas Publik -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 lg:col-span-2">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2.5 flex items-center gap-2">
                        <i class="fas fa-building text-slate-500"></i>
                        <span>Sarana & Prasarana Pelayanan Umum</span>
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <i class="fas fa-school text-slate-600 text-xl mb-1.5 block"></i>
                            <span class="text-2xl font-black text-slate-900 block">{{ $villageProfile['territory']['schools'] ?? '7' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Sekolah (SD/SMP/SMA)</span>
                        </div>
                        <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-100">
                            <i class="fas fa-mosque text-blue-600 text-xl mb-1.5 block"></i>
                            <span class="text-2xl font-black text-slate-900 block">{{ $villageProfile['territory']['mosques'] ?? '12' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Masjid & Musholla</span>
                        </div>
                        <div class="p-4 bg-pink-50/70 rounded-2xl border border-pink-100">
                            <i class="fas fa-hospital-alt text-pink-600 text-xl mb-1.5 block"></i>
                            <span class="text-2xl font-black text-slate-900 block">{{ $villageProfile['territory']['health'] ?? '8' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Poskesdes & Posyandu</span>
                        </div>
                        <div class="p-4 bg-amber-50/70 rounded-2xl border border-amber-100">
                            <i class="fas fa-store text-amber-600 text-xl mb-1.5 block"></i>
                            <span class="text-2xl font-black text-slate-900 block">{{ $villageProfile['territory']['markets'] ?? '2' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pasar & Sentra UMKM</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs text-slate-600 leading-relaxed">
                        <strong class="text-slate-800 block mb-1">Struktur Kelembagaan Teritorial:</strong>
                        Wilayah terbagi ke dalam <strong>{{ $villageProfile['territory']['rw'] ?? '08' }} Rukun Warga (RW)</strong> dan <strong>{{ $villageProfile['territory']['rt'] ?? '32' }} Rukun Tetangga (RT)</strong> yang aktif menyelenggarakan rembug warga bulanan dan gotong royong terpadu.
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL PRATINJAU DOKUMEN LAPORAN REALISASI (PDF)                         -->
    <!-- ========================================================================= -->
    <div x-show="previewModalOpen"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         x-cloak>
        <div @click.away="previewModalOpen = false"
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg font-black">
                        <i class="fas fa-file-pdf"></i>
                    </span>
                    <div>
                        <h4 class="font-black text-slate-900 text-sm sm:text-base leading-tight" x-text="previewDocTitle"></h4>
                        <span class="text-xs text-slate-500" x-text="'Tahun Anggaran ' + previewDocYear"></span>
                    </div>
                </div>
                <button type="button" @click="previewModalOpen = false"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3 text-xs text-slate-600 leading-relaxed">
                <div class="flex items-center gap-2 text-emerald-700 font-bold">
                    <i class="fas fa-check-circle"></i>
                    <span>Dokumen Publik Terverifikasi BPKP & Inspektorat</span>
                </div>
                <p>
                    Laporan Realisasi Pertanggungjawaban APBDes ini memuat rincian pos pendapatan asli desa, dana perimbangan, belanja penyelenggaraan, belanja pembangunan fisik, pembinaan kemasyarakatan, pemberdayaan ekonomi, serta saldo SiLPA kas kelurahan.
                </p>
                <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/60">
                    Format file: <strong>Adobe Acrobat (.PDF)</strong> | Ukuran: <strong>~2.4 MB</strong>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" @click="previewModalOpen = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <a href="{{ asset('docs/transparansi-apbd.pdf') }}" download
                   @click="previewModalOpen = false"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                    <i class="fas fa-download"></i>
                    <span>Konfirmasi Unduh PDF</span>
                </a>
            </div>

        </div>
    </div>

</section>

@endsection