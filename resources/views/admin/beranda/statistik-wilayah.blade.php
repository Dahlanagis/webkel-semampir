@extends('layouts.admin')

@section('title', 'Statistik Wilayah & Indikator')
@section('header-title', 'Statistik Wilayah & Indikator Kependudukan')
@section('header-subtitle', 'Mengatur data wilayah teritorial, RT/RW, Kepala Keluarga (KK), kepadatan penduduk, dan integrasi API.')

@section('content')
<div class="space-y-6" x-data="{
    luasWilayah: '{{ old('luas_wilayah', $regionalStatistic->luas_wilayah ?? 3.82) }}',
    jumlahKk: '{{ old('jumlah_kk', $regionalStatistic->jumlah_kk ?? 2640) }}',
    jumlahRt: '{{ old('jumlah_rt', $regionalStatistic->jumlah_rt ?? 32) }}',
    jumlahRw: '{{ old('jumlah_rw', $regionalStatistic->jumlah_rw ?? 8) }}',
    totalPenduduk: {{ $regionalStatistic->total_penduduk ?? 5000 }},

    parseNum(val) {
        if (!val) return 0;
        let cleaned = String(val).replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.-]/g, '');
        let n = parseFloat(cleaned);
        return isNaN(n) ? 0 : n;
    },

    formatNum(n) {
        return new Intl.NumberFormat('id-ID').format(Math.round(n));
    },

    get rataRataJiwaKk() {
        let kk = this.parseNum(this.jumlahKk);
        return kk > 0 ? (this.totalPenduduk / kk).toFixed(2).replace('.', ',') : '0,00';
    },

    get kepadatanJiwaKm2() {
        let luas = parseFloat(String(this.luasWilayah).replace(',', '.'));
        return (luas > 0) ? (this.totalPenduduk / luas).toFixed(1).replace('.', ',') : '0,0';
    }
}">

    <!-- Alert Status -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('status'))
        <div class="bg-slate-50 border border-slate-200 text-slate-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-slate-600 text-sm"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-times-circle text-rose-600 mt-0.5 text-sm"></i>
            <div>
                <span class="font-bold">Gagal memproses data:</span>
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. HERO KPI CARDS STATISTIK WILAYAH TA 2026 (MODERN EXECUTIVE LIGHT DESIGN)-->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-br from-white via-slate-50/70 to-emerald-50/30 rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/90 relative overflow-hidden">
        <!-- Ambient Decorative Lighting Effect -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-emerald-100/50 to-teal-50/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-gradient-to-tr from-slate-100/70 to-emerald-50/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-slate-200/80">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-[11px] font-extrabold tracking-wide mb-2.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>SINKRONISASI DATABASE REGIONAL TA {{ $regionalStatistic->tahun ?? '2026' }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-sm shadow-sm shadow-emerald-500/20 shrink-0">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span>Dashboard Statistik Wilayah & Indikator</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1.5 max-w-2xl leading-relaxed">
                    {{ $regionalStatistic->nama_kelurahan ?? 'Kelurahan Semampir' }}, {{ $regionalStatistic->nama_kecamatan ?? 'Kecamatan Kraksaan' }}, {{ $regionalStatistic->nama_kabupaten ?? 'Kabupaten Probolinggo' }}. Terhubung langsung dengan Endpoint API Publik dan Beranda Web Portal.
                </p>
            </div>

            <!-- Tombol Action API & Beranda (Clean Executive Style) -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ url('/api/v1/statistik-wilayah') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 border border-slate-200/90 text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-95">
                    <i class="fas fa-code text-slate-500"></i>
                    <span>Test API JSON (v1)</span>
                </a>
                <a href="{{ route('home') }}#statistik" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black transition-all shadow-sm shadow-emerald-600/20 active:scale-95 border border-emerald-500/40">
                    <i class="fas fa-external-link-alt text-[11px]"></i>
                    <span>Lihat di Beranda</span>
                </a>
            </div>
        </div>

        <!-- 6 KPI Grid Cards (Clean, Crisp, Modern Elevated Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 pt-6">
            <!-- 1. Total Penduduk -->
            <div class="p-4 bg-white hover:bg-slate-50/50 border border-slate-200/90 hover:border-emerald-300 rounded-2xl transition-all duration-200 shadow-2xs hover:shadow-md group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 group-hover:text-emerald-700 uppercase tracking-wider block transition-colors">Total Penduduk</span>
                        <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] border border-emerald-100">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 mt-2 block tracking-tight">
                        {{ number_format($regionalStatistic->total_penduduk ?? 5000, 0, ',', '.') }} <span class="text-xs font-bold text-slate-400">Jiwa</span>
                    </span>
                </div>
                <div class="mt-2.5 inline-flex items-center gap-1 text-[11px] text-emerald-700 font-bold bg-emerald-50/80 px-2 py-0.5 rounded-md border border-emerald-100 w-fit">
                    <i class="fas fa-arrow-up text-[8px]"></i> +{{ number_format($regionalStatistic->pertumbuhan_penduduk ?? 1.2, 1, ',', '.') }}% thn ini
                </div>
            </div>

            <!-- 2. Kepala Keluarga -->
            <div class="p-4 bg-white hover:bg-slate-50/50 border border-slate-200/90 hover:border-slate-300 rounded-2xl transition-all duration-200 shadow-2xs hover:shadow-md group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Total KK</span>
                        <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] border border-slate-200">
                            <i class="fas fa-house-user"></i>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 mt-2 block tracking-tight">
                        {{ number_format($regionalStatistic->jumlah_kk ?? 2640, 0, ',', '.') }} <span class="text-xs font-bold text-slate-400">KK</span>
                    </span>
                </div>
                <span class="text-[11px] text-slate-500 font-medium block mt-2.5">KK Terdaftar Aktif</span>
            </div>

            <!-- 3. Rata-rata Jiwa/KK (Formula) -->
            <div class="p-4 bg-white hover:bg-slate-50/50 border border-slate-200/90 hover:border-emerald-300 rounded-2xl transition-all duration-200 shadow-2xs hover:shadow-md group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 group-hover:text-emerald-700 uppercase tracking-wider block transition-colors">Rata-rata / KK</span>
                        <div class="w-6 h-6 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-[10px] border border-teal-100">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-emerald-950 mt-2 block tracking-tight" x-text="rataRataJiwaKk + ' Jiwa'">
                        {{ number_format($regionalStatistic->rata_rata_jiwa_per_kk ?? 1.89, 2, ',', '.') }} Jiwa
                    </span>
                </div>
                <div class="mt-2.5 inline-block text-[10px] font-mono text-emerald-800 bg-emerald-50/80 px-2 py-0.5 rounded-md border border-emerald-100 w-fit">
                    Penduduk ÷ KK
                </div>
            </div>

            <!-- 4. Kepadatan (Formula) -->
            <div class="p-4 bg-white hover:bg-slate-50/50 border border-slate-200/90 hover:border-sky-300 rounded-2xl transition-all duration-200 shadow-2xs hover:shadow-md group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Kepadatan Wilayah</span>
                        <div class="w-6 h-6 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-[10px] border border-sky-100">
                            <i class="fas fa-chart-area"></i>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 mt-2 block tracking-tight" x-text="kepadatanJiwaKm2 + ' /km²'">
                        {{ number_format($regionalStatistic->kepadatan_penduduk ?? 1308.9, 1, ',', '.') }} /km²
                    </span>
                </div>
                <div class="mt-2.5 inline-block text-[10px] font-mono text-sky-800 bg-sky-50/80 px-2 py-0.5 rounded-md border border-sky-100 w-fit">
                    Penduduk ÷ Luas
                </div>
            </div>

            <!-- 5. Luas & RT/RW -->
            <div class="p-4 bg-white hover:bg-slate-50/50 border border-slate-200/90 hover:border-slate-300 rounded-2xl transition-all duration-200 shadow-2xs hover:shadow-md group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Luas & RT / RW</span>
                        <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] border border-slate-200">
                            <i class="fas fa-map"></i>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 mt-2 block tracking-tight">
                        {{ number_format($regionalStatistic->luas_wilayah ?? 3.82, 2, ',', '.') }} <span class="text-xs font-bold text-slate-400">km²</span>
                    </span>
                </div>
                <span class="text-[11px] text-slate-700 font-semibold block mt-2.5">
                    {{ $regionalStatistic->jumlah_rt ?? 32 }} RT &bull; {{ $regionalStatistic->jumlah_rw ?? 8 }} RW
                </span>
            </div>

            <!-- 6. Usia Produktif -->
            <div class="p-4 bg-white hover:bg-slate-50/50 border border-slate-200/90 hover:border-amber-300 rounded-2xl transition-all duration-200 shadow-2xs hover:shadow-md group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Usia Produktif</span>
                        <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-[10px] border border-amber-100">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 mt-2 block tracking-tight">
                        {{ number_format($regionalStatistic->usia_produktif ?? 5610, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-2.5 inline-flex items-center gap-1 text-[11px] text-amber-800 font-bold bg-amber-50/80 px-2 py-0.5 rounded-md border border-amber-100 w-fit">
                    {{ $regionalStatistic->persentase_usia_produktif ?? 66.6 }}% Produktif
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. FORM KELOLA STATISTIK WILAYAH (TABEL REGIONAL_STATISTICS)               -->
    <!-- ========================================================================= -->
    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="statistik_wilayah">
        
        <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-emerald-600"></i>
                    <span>Form Data Indikator Wilayah & Kepala Keluarga (KK)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Data ini menentukan nilai Luas Wilayah, RT/RW, KK, Rata-rata Jiwa/KK, dan Kepadatan Penduduk di seluruh web portal.
                </p>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2 shrink-0">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan Statistik Wilayah</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <!-- Tahun Anggaran -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>Tahun Anggaran (TA) *</span>
                    <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-bold">Aktif</span>
                </label>
                <input type="number" name="tahun" value="{{ old('tahun', $regionalStatistic->tahun ?? 2026) }}" required min="2020" max="2050" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-bold">
                <span class="text-[10px] text-slate-400 mt-1 block">Tahun basis data statistik</span>
            </div>

            <!-- Luas Wilayah -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>Luas Wilayah (km²) *</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Kilometer Persegi</span>
                </label>
                <input type="number" step="0.01" name="luas_wilayah" x-model="luasWilayah" required placeholder="Contoh: 3.82" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-bold">
                <span class="text-[10px] text-slate-400 mt-1 block">Digunakan menghitung kepadatan penduduk</span>
            </div>

            <!-- Jumlah RT -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jumlah RT Aktif *</label>
                <input type="number" name="jumlah_rt" x-model="jumlahRt" required min="1" placeholder="Contoh: 32" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-bold">
                <span class="text-[10px] text-slate-400 mt-1 block">Rukun Tetangga terdaftar</span>
            </div>

            <!-- Jumlah RW -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jumlah RW Aktif *</label>
                <input type="number" name="jumlah_rw" x-model="jumlahRw" required min="1" placeholder="Contoh: 8" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-bold">
                <span class="text-[10px] text-slate-400 mt-1 block">Rukun Warga terdaftar</span>
            </div>

            <!-- Jumlah Kepala Keluarga (KK) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>Total Kepala Keluarga (KK) *</span>
                    <span class="text-[10px] text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full font-bold">SIAK</span>
                </label>
                <input type="number" name="jumlah_kk" x-model="jumlahKk" required min="1" placeholder="Contoh: 2640" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-bold">
                <span class="text-[10px] text-slate-400 mt-1 block">Digunakan menghitung rata-rata jiwa/KK</span>
            </div>

            <!-- Pertumbuhan Penduduk -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Pertumbuhan Tahunan (%) *</label>
                <input type="number" step="0.1" name="pertumbuhan_penduduk" value="{{ old('pertumbuhan_penduduk', $regionalStatistic->pertumbuhan_penduduk ?? 1.2) }}" required placeholder="Contoh: 1.2" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-bold">
                <span class="text-[10px] text-slate-400 mt-1 block">Persentase pertumbuhan per tahun</span>
            </div>

            <!-- Sumber Data Resmi -->
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Sumber Data Kependudukan *</label>
                <input type="text" name="sumber_data" value="{{ old('sumber_data', $regionalStatistic->sumber_data ?? 'SIAK Dispendukcapil') }}" required placeholder="Contoh: SIAK Dispendukcapil" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                <span class="text-[10px] text-slate-400 mt-1 block">Nama instansi penyedia data resmi</span>
            </div>

            <!-- Catatan Keterangan -->
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block font-bold text-slate-700 mb-1.5">Catatan / Keterangan Analitik</label>
                <input type="text" name="catatan" value="{{ old('catatan', $regionalStatistic->catatan ?? 'Data indikator resmi terpadu SIAK Dispendukcapil') }}" placeholder="Contoh: Data indikator resmi terpadu SIAK Dispendukcapil" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                <span class="text-[10px] text-slate-400 mt-1 block">Catatan tambahan yang akan disertakan di metadata API</span>
            </div>

            <!-- Live Kalkulasi Preview Banner -->
            <div class="sm:col-span-2 lg:col-span-4 bg-emerald-50/60 border border-emerald-200/80 rounded-xl p-3.5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fas fa-calculator"></i>
                    </span>
                    <div>
                        <span class="text-xs font-bold text-emerald-950 block">Formula Analitik Otomatis (Realtime Preview):</span>
                        <span class="text-[11px] text-emerald-800">
                            Rata-rata Jiwa / KK: <strong class="text-emerald-900" x-text="rataRataJiwaKk">1,89</strong> Jiwa &bull;
                            Kepadatan Penduduk: <strong class="text-emerald-900" x-text="kepadatanJiwaKm2">1.308,9</strong> Jiwa/km²
                        </span>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-emerald-700 bg-white px-3 py-1 rounded-lg border border-emerald-300 shadow-2xs">
                    Tersinkronisasi Otomatis
                </span>
            </div>
        </div>
    </form>


</div>
@endsection
