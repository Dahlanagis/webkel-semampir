@extends('layouts.app')

@section('title', 'Struktur Organisasi (SOTK) - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Struktur Organisasi & SOTK</h1>
                <p class="text-sm text-slate-500">Bagan alur kelembagaan, hierarki aparatur resmi, dan tata kerja Kelurahan Patokan.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm">
                    <i class="fas fa-arrow-left text-slate-400"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

        {{-- Main Outer Container: SaaS / GovTech Card (rounded-3xl, bg-slate-50, white card, subtle borders, gentle shadows) --}}
        <div class="w-full space-y-6">

            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 space-y-8">
                
                {{-- Header Section inside Card --}}
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        BAGAN STRUKTUR RESMI
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Struktur Organisasi Kelurahan Patokan
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                        Bagan tata kerja kelembagaan dan pembagian kelompok jabatan fungsional Pemerintah Kelurahan Patokan, Kecamatan Kraksaan.
                    </p>
                </div>

                {{-- Org Chart Canvas (overflow-x-auto for responsiveness) --}}
                <div class="overflow-x-auto pb-8 custom-scrollbar">
                    <div class="min-w-[920px] flex flex-col items-center py-6">

                        {{-- 1. TOP NODE (Chief / Lurah) --}}
                        <div class="flex flex-col items-center z-10 relative">
                            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-56 hover:border-slate-300 transition">
                                {{-- Foto Lingkaran Lurah (Ukuran Dikunci w-24 h-24 / 96px) --}}
                                <div class="w-24 h-24 rounded-full border-2 border-slate-800 p-0.5 mb-2.5 bg-slate-50 shrink-0 shadow-sm">
                                    <img src="{{ !empty($villageProfile['head_photo']) ? asset('storage/' . $villageProfile['head_photo']) : asset('images/sotk/lurah.png') }}" 
                                        alt="Lurah Patokan" 
                                        class="w-full h-full object-cover rounded-full">
                                </div>
                                
                                <h3 class="font-bold text-xs text-slate-900 leading-snug">
                                    {{ $villageProfile['head_name'] ?? 'Drs. H. Ahmad Sudirman, M.Si' }}
                                </h3>
                                
                                <div class="mt-1.5">
                                    <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-3 py-1 rounded-md tracking-wider inline-block">
                                        LURAH PATOKAN
                                    </span>
                                </div>
                            </div>

                            {{-- Garis Penghubung Turun --}}
                            <div class="w-px h-10 bg-slate-300"></div>
                        </div>


                        {{-- 3. BOTTOM NODES (4 Columns Grid) --}}
                        <div class="w-full max-w-4xl relative">
                            {{-- Horizontal Branch Line --}}
                            <div class="w-full h-px bg-slate-300 absolute top-0 left-0 right-0"></div>

                            {{-- 4 Evenly Spaced Columns --}}
                            <div class="grid grid-cols-4 gap-4 sm:gap-6 pt-8">

                                {{-- Node 1: Secretary --}}
                                <div class="flex flex-col items-center relative">
                                    {{-- Vertical connector line from top horizontal branch --}}
                                    <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>

                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-300 transition group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                            <img src="{{ !empty($villageProfile['sekel_photo']) ? asset('storage/' . $villageProfile['sekel_photo']) : asset('images/sotk/sekel.png') }}" 
                                                 alt="Sekretaris Kelurahan" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight">
                                            {{ $villageProfile['sekel_name'] ?? 'Budi Santoso, S.STP' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-sm">
                                                SEKRETARIS KELURAHAN
                                            </span>
                                        </div>
                                        <span class="border border-slate-300 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full mt-2.5 inline-block">
                                            Kelompok Jabatan Fungsional
                                        </span>
                                    </div>
                                </div>

                                {{-- Node 2: Section Head 1 (Pemerintahan) --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>

                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-300 transition group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                            <img src="{{ !empty($villageProfile['kasi_pem_photo']) ? asset('storage/' . $villageProfile['kasi_pem_photo']) : asset('images/sotk/kasi_pem.png') }}" 
                                                 alt="Kasi Pemerintahan" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight">
                                            {{ $villageProfile['kasi_kesra_name'] ?? 'Arief Rachman, S.IP' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-sm">
                                                KASI PEMERINTAHAN & TRANTIB
                                            </span>
                                        </div>
                                        <span class="border border-slate-300 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full mt-2.5 inline-block">
                                            Kelompok Jabatan Fungsional
                                        </span>
                                    </div>
                                </div>

                                {{-- Node 3: Section Head 2 (Kesra) --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>

                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-300 transition group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                            <img src="{{ !empty($villageProfile['kasi_kesra_photo']) ? asset('storage/' . $villageProfile['kasi_kesra_photo']) : asset('images/sotk/kasi_kesra.png') }}" 
                                                 alt="Kasi Kesra" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight">
                                            {{ $villageProfile['kasi_pem_name'] ?? 'Siti Aminah, S.Sos' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-sm">
                                                KASI PELAYANAN PUBLIK & KESRA
                                            </span>
                                        </div>
                                        <span class="border border-slate-300 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full mt-2.5 inline-block">
                                            Kelompok Jabatan Fungsional
                                        </span>
                                    </div>
                                </div>

                                {{-- Node 4: Section Head 3 (Ekbang) --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>

                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-300 transition group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                            <img src="{{ !empty($villageProfile['kasi_ekbang_photo']) ? asset('storage/' . $villageProfile['kasi_ekbang_photo']) : asset('images/sotk/kasi_ekbang.png') }}" 
                                                 alt="Kasi Ekbang" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight">
                                            {{ $villageProfile['kasi_ekbang_name'] ?? 'Bambang Wijaya, S.T' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-sm">
                                                KASI PEMBERDAYAAN & EKBANG
                                            </span>
                                        </div>
                                        <span class="border border-slate-300 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full mt-2.5 inline-block">
                                            Kelompok Jabatan Fungsional
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- TUPOKSI Summary Cards below --}}
                <div class="pt-6 border-t border-slate-100 space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tasks text-emerald-600"></i>
                        <h3 class="font-extrabold text-sm text-slate-900">Rincian Tugas Pokok & Fungsi (TUPOKSI)</h3>
                    </div>

                    <div class="flex flex-wrap justify-center -m-2 text-left">
                        <div class="w-full sm:w-1/2 lg:w-1/3 p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5 h-full">
                                <h4 class="font-bold text-xs text-slate-900">Lurah Kelurahan</h4>
                                <div class="text-[11px] text-slate-500 leading-relaxed prose-p:m-0">{!! $villageProfile['lurah_tupoksi'] ?? 'Penyelenggara utama urusan pemerintahan, ketertiban umum, pelayanan publik, dan pembinaan wilayah Patokan.' !!}</div>
                            </div>
                        </div>
                        <div class="w-full sm:w-1/2 lg:w-1/3 p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5 h-full">
                                <h4 class="font-bold text-xs text-slate-900">Sekretaris Kelurahan</h4>
                                <div class="text-[11px] text-slate-500 leading-relaxed prose-p:m-0">{!! $villageProfile['sekel_tupoksi'] ?? 'Pengelola administrasi umum, perencanaan operasional, keuangan, dan pelayanan surat-menyurat kelurahan.' !!}</div>
                            </div>
                        </div>
                        <div class="w-full sm:w-1/2 lg:w-1/3 p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5 h-full">
                                <h4 class="font-bold text-xs text-slate-900">Kasi Pemerintahan & Trantib</h4>
                                <div class="text-[11px] text-slate-500 leading-relaxed prose-p:m-0">{!! $villageProfile['kasi_pem_tupoksi'] ?? 'Pelayanan KTP/KK, pengawasan ketertiban lingkungan, dan pengelolaan data pertanahan & PBB.' !!}</div>
                            </div>
                        </div>
                        <div class="w-full sm:w-1/2 lg:w-1/3 p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5 h-full">
                                <h4 class="font-bold text-xs text-slate-900">Kasi Pelayanan & Kesra</h4>
                                <div class="text-[11px] text-slate-500 leading-relaxed prose-p:m-0">{!! $villageProfile['kasi_kesra_tupoksi'] ?? 'Penerbitan SKTM, koordinasi bantuan sosial kementerian, kesehatan posyandu, dan keagamaan.' !!}</div>
                            </div>
                        </div>
                        <div class="w-full sm:w-1/2 lg:w-1/3 p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5 h-full">
                                <h4 class="font-bold text-xs text-slate-900">Kasi Pemberdayaan & Ekbang</h4>
                                <div class="text-[11px] text-slate-500 leading-relaxed prose-p:m-0">{!! $villageProfile['kasi_ekbang_tupoksi'] ?? 'Pemberdayaan masyarakat, pembinaan UMKM, fasilitasi pembangunan infrastruktur kelurahan, dan kebersihan lingkungan.' !!}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
