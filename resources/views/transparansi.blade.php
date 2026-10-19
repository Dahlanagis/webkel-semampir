@extends('layouts.app')

@section('title', 'Transparansi Anggaran (APBDes) - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('meta_description', 'Transparansi Realisasi Anggaran Pendapatan dan Belanja (APBDes / APBD) Pemerintah ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir') . ', Kecamatan ' . ($villageProfile['subdistrict'] ?? 'Kraksaan') . '.')

@section('content')

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="w-full bg-slate-50 min-h-screen text-slate-800 pb-16"
     x-data="{
        showBansosSection: false,
        printPage() {
            window.print();
        }
     }">

    <!-- ========================================================================= -->
    <!-- 1. HEADER BANNER: TRANSPARANSI ANGGARAN (APBDES)                          -->
    <!-- Menggunakan tema warna Executive Deep Slate-Navy (Elegan & Berbeda dari Hijau) -->
    <!-- ========================================================================= -->
    <div class="w-full bg-gradient-to-r from-slate-950 via-[#0f1d36] to-slate-900 text-white py-10 sm:py-14 px-4 shadow-md relative overflow-hidden border-b border-slate-800">
        <!-- Ambient Glowing Backdrop -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.05)_1px,transparent_1px)] [background-size:18px_18px] pointer-events-none opacity-40"></div>

        <div class="relative z-10 max-w-5xl mx-auto text-center space-y-2">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight">
                Transparansi Anggaran (APBDes)
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 font-medium">
                Pemerintah {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }} &bull; Kecamatan {{ $villageProfile['subdistrict'] ?? 'Kraksaan' }} &bull; Tahun Anggaran {{ $selectedYear }}
            </p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TOOLBAR: TOMBOL KEMBALI KE DAFTAR & FILTER TAHUN                       -->
    <!-- ========================================================================= -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <!-- Tombol Kembali ke Daftar (Persis Screenshot) -->
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100/80 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition active:scale-95 w-fit">
                <i class="fas fa-arrow-left text-xs text-slate-500"></i>
                <span>Kembali ke Daftar</span>
            </a>

            <!-- Filter Tahun Anggaran & Aksi Cetak/Unduh -->
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="flex items-center gap-2 bg-white border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs">
                    <label for="tahun_select" class="text-xs font-bold text-slate-500 flex items-center gap-1.5 shrink-0">
                        <i class="fas fa-calendar-days text-slate-400"></i>
                        <span>Tahun:</span>
                    </label>
                    <select id="tahun_select" 
                            onchange="window.location.href='{{ route('transparansi') }}?tahun=' + this.value"
                            class="bg-transparent text-xs sm:text-sm font-black text-slate-800 focus:outline-none cursor-pointer">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ (int)$selectedYear === (int)$year ? 'selected' : '' }}>
                                TA {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <a href="{{ asset('docs/transparansi-apbd.pdf') }}" download
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-xs transition">
                    <i class="fas fa-file-pdf text-rose-500"></i>
                    <span class="hidden sm:inline">Unduh PDF</span>
                </a>

                <button type="button" @click="printPage()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-xs transition">
                    <i class="fas fa-print text-slate-500"></i>
                    <span class="hidden sm:inline">Cetak</span>
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. KONTEN UTAMA: 3 KARTU TABEL DENGAN WARNA DIBEDAKAN                     -->
    <!-- 1. Pendapatan Desa  -> Emerald Green (#059669)                             -->
    <!-- 2. Belanja Desa     -> Amber / Terracotta (#d97706)                        -->
    <!-- 3. Pembiayaan Desa  -> Royal Blue / Indigo (#2563eb)                       -->
    <!-- ========================================================================= -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

        <!-- ===================================================================== -->
        <!-- KARTU 1: PENDAPATAN DESA (TEMA WARNA: EMERALD GREEN)                  -->
        <!-- ===================================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
            <!-- Header Kartu -->
            <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>1. Pendapatan Desa</span>
                </h2>
                <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/60">
                    Penerimaan Kas
                </span>
            </div>

            <!-- Tabel Pendapatan Desa -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-emerald-600 text-white font-bold text-xs sm:text-sm">
                            <th class="py-3 px-5 sm:px-6 text-left font-bold tracking-wide w-2/5"></th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Rencana / Anggaran</th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Realisasi</th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Lebih/Kurang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendapatanSections as $sec)
                            <!-- Subkategori Title Row -->
                            <tr class="bg-slate-50/60">
                                <td colspan="4" class="pt-3.5 pb-2 px-5 sm:px-6 font-bold text-xs text-emerald-700 uppercase tracking-wider">
                                    {{ $sec['name'] }}
                                </td>
                            </tr>
                            <!-- Baris Data Item -->
                            @foreach($sec['items'] as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-5 sm:px-6 font-medium text-slate-700 pl-7 sm:pl-9">
                                    {{ $item['nama'] }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format($item['anggaran'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format($item['realisasi'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format(abs($item['selisih']), 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach

                        <!-- Baris Total Pendapatan -->
                        <tr class="bg-emerald-50/70 font-bold border-t-2 border-emerald-200 text-slate-900">
                            <td class="py-3 px-5 sm:px-6 font-black text-xs sm:text-sm text-emerald-950 uppercase">
                                Total Pendapatan
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-emerald-950 whitespace-nowrap">
                                Rp. {{ number_format($totalPendapatanAnggaran, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-emerald-950 whitespace-nowrap">
                                Rp. {{ number_format($totalPendapatanRealisasi, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-emerald-950 whitespace-nowrap">
                                Rp. {{ number_format(abs($totalPendapatanSelisih), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 2: BELANJA DESA (TEMA WARNA: AMBER / ORANGE)                    -->
        <!-- ===================================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
            <!-- Header Kartu -->
            <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>2. Belanja Desa</span>
                </h2>
                <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/60">
                    Pengeluaran Kas
                </span>
            </div>

            <!-- Tabel Belanja Desa -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-amber-600 text-white font-bold text-xs sm:text-sm">
                            <th class="py-3 px-5 sm:px-6 text-left font-bold tracking-wide w-2/5"></th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Rencana / Anggaran</th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Realisasi</th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Lebih/Kurang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($belanjaSections as $sec)
                            <!-- Subkategori Title Row -->
                            <tr class="bg-slate-50/60">
                                <td colspan="4" class="pt-3.5 pb-2 px-5 sm:px-6 font-bold text-xs text-amber-800 uppercase tracking-wider">
                                    {{ $sec['name'] }}
                                </td>
                            </tr>
                            <!-- Baris Data Item -->
                            @foreach($sec['items'] as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-5 sm:px-6 font-medium text-slate-700 pl-7 sm:pl-9">
                                    {{ $item['nama'] }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format($item['anggaran'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format($item['realisasi'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format(abs($item['selisih']), 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach

                        <!-- Baris Total Belanja -->
                        <tr class="bg-amber-50/70 font-bold border-t-2 border-amber-200 text-slate-900">
                            <td class="py-3 px-5 sm:px-6 font-black text-xs sm:text-sm text-amber-950 uppercase">
                                Total Belanja
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-amber-950 whitespace-nowrap">
                                Rp. {{ number_format($totalBelanjaAnggaran, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-amber-950 whitespace-nowrap">
                                Rp. {{ number_format($totalBelanjaRealisasi, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-amber-950 whitespace-nowrap">
                                Rp. {{ number_format(abs($totalBelanjaSelisih), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 3: PEMBIAYAAN DESA (TEMA WARNA: ROYAL BLUE / INDIGO)            -->
        <!-- ===================================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
            <!-- Header Kartu -->
            <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>3. Pembiayaan Desa</span>
                </h2>
                <span class="text-[11px] font-bold text-blue-800 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200/60">
                    Netto Pembiayaan
                </span>
            </div>

            <!-- Tabel Pembiayaan Desa -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-blue-600 text-white font-bold text-xs sm:text-sm">
                            <th class="py-3 px-5 sm:px-6 text-left font-bold tracking-wide w-2/5"></th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Rencana / Anggaran</th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Realisasi</th>
                            <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Lebih/Kurang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pembiayaanSections as $sec)
                            <!-- Subkategori Title Row -->
                            <tr class="bg-slate-50/60">
                                <td colspan="4" class="pt-3.5 pb-2 px-5 sm:px-6 font-bold text-xs text-blue-800 uppercase tracking-wider">
                                    {{ $sec['name'] }}
                                </td>
                            </tr>
                            <!-- Baris Data Item -->
                            @foreach($sec['items'] as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-5 sm:px-6 font-medium text-slate-700 pl-7 sm:pl-9">
                                    {{ $item['nama'] }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format($item['anggaran'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format($item['realisasi'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                    Rp. {{ number_format(abs($item['selisih']), 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach

                        <!-- Baris Pembiayaan Netto -->
                        <tr class="bg-blue-50/70 font-bold border-t-2 border-blue-200 text-slate-900">
                            <td class="py-3 px-5 sm:px-6 font-black text-xs sm:text-sm text-blue-950 uppercase">
                                Pembiayaan Netto
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-blue-950 whitespace-nowrap">
                                Rp. {{ number_format($totalPembiayaanAnggaran, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-blue-950 whitespace-nowrap">
                                Rp. {{ number_format($totalPembiayaanRealisasi, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right font-black text-blue-950 whitespace-nowrap">
                                Rp. {{ number_format(abs($totalPembiayaanSelisih), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- 4. RINGKASAN SURPLUS / DEFISIT & SILPA (AKUNTABILITAS STANDAR)         -->
        <!-- ===================================================================== -->
        @php
            $surplusDefisitAnggaran = $totalPendapatanAnggaran - $totalBelanjaAnggaran;
            $surplusDefisitRealisasi = $totalPendapatanRealisasi - $totalBelanjaRealisasi;
            $silpaAkhir = $surplusDefisitRealisasi + $totalPembiayaanRealisasi;
        @endphp

        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-700/80">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-700 pb-4 mb-4">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-scale-balanced text-emerald-400"></i>
                        <span>Ringkasan Akhir Perhitungan Anggaran TA {{ $selectedYear }}</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Saldo surplus/defisit dan Sisa Lebih Pembiayaan Anggaran (SiLPA) tahun berjalan.
                    </p>
                </div>
                <div class="text-left sm:text-right shrink-0">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">SiLPA Tahun Berjalan</span>
                    <span class="text-lg sm:text-xl font-black text-emerald-400">
                        Rp. {{ number_format($silpaAkhir, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                    <span class="text-slate-400 font-semibold block text-[11px]">Total Pendapatan:</span>
                    <span class="font-bold text-emerald-300 text-sm mt-0.5 block">
                        Rp. {{ number_format($totalPendapatanRealisasi, 0, ',', '.') }}
                    </span>
                </div>
                <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                    <span class="text-slate-400 font-semibold block text-[11px]">Total Belanja:</span>
                    <span class="font-bold text-amber-300 text-sm mt-0.5 block">
                        Rp. {{ number_format($totalBelanjaRealisasi, 0, ',', '.') }}
                    </span>
                </div>
                <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                    <span class="text-slate-400 font-semibold block text-[11px]">Surplus / (Defisit):</span>
                    <span class="font-bold {{ $surplusDefisitRealisasi >= 0 ? 'text-emerald-300' : 'text-rose-300' }} text-sm mt-0.5 block">
                        Rp. {{ number_format($surplusDefisitRealisasi, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- 5. TOMBOL OPSIONAL: INFORMASI BANTUAN SOSIAL (BANSOS)                 -->
        <!-- ===================================================================== -->
        <div class="pt-2 text-center">
            <button type="button" @click="showBansosSection = !showBansosSection"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 font-bold text-xs transition shadow-xs">
                <i class="fas fa-hand-holding-heart text-emerald-600"></i>
                <span x-text="showBansosSection ? 'Sembunyikan Data Bantuan Sosial (Bansos)' : 'Lihat Ringkasan Bantuan Sosial (Bansos)'"></span>
                <i class="fas fa-chevron-down text-[10px] transition-transform" :class="{ 'rotate-180': showBansosSection }"></i>
            </button>
        </div>

        <!-- Collapsible Bansos Content -->
        <div x-show="showBansosSection" x-collapse x-cloak class="space-y-4 pt-2">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-users-viewfinder text-emerald-600"></i>
                        <span>Ringkasan Data Penyaluran Bantuan Sosial</span>
                    </h3>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                        {{ $socialAssistance['summary']['total_kpm'] ?? '842' }} KPM Terdaftar
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs mb-4">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 text-center">
                        <span class="text-slate-500 font-semibold text-[11px] block">Total Anggaran Bansos</span>
                        <span class="text-base font-extrabold text-slate-900 mt-0.5 block">{{ $socialAssistance['summary']['total_anggaran_salur'] ?? 'Rp 1,84 Miliar' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 text-center">
                        <span class="text-slate-500 font-semibold text-[11px] block">Persentase KK Penerima</span>
                        <span class="text-base font-extrabold text-slate-900 mt-0.5 block">{{ $socialAssistance['summary']['persen_kk'] ?? '31,9%' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 text-center">
                        <span class="text-slate-500 font-semibold text-[11px] block">Basis Basis Data</span>
                        <span class="text-xs font-extrabold text-slate-800 mt-1 block">DTKS Kemensos RI</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    @foreach($socialAssistance['categories'] ?? [] as $cat)
                    <div class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 transition bg-slate-50/50">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-extrabold text-slate-900 text-xs">{{ $cat['name'] }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                {{ $cat['recipients'] ?? '0' }} KPM
                            </span>
                        </div>
                        <p class="text-slate-500 text-[11px] leading-relaxed line-clamp-2">{{ $cat['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>

@endsection