@extends('layouts.admin')

@section('title', 'Statistik Dasar & Demografi')
@section('header-title', 'Statistik & Demografi Warga')
@section('header-subtitle', 'Mengatur data statistik penduduk, pekerjaan, dan pendidikan.')

@section('content')
<div class="space-y-6" x-data="{
    createOccOpen: false,
    editOccOpen: false,
    selectedOcc: null,
    selectedOccIndex: null,

    createEduOpen: false,
    editEduOpen: false,
    selectedEdu: null,
    selectedEduIndex: null,

    male: '{{ old('demo_male', $profile['demographics']['male'] ?? '4.180') }}',
    female: '{{ old('demo_female', $profile['demographics']['female'] ?? '4.245') }}',
    prodCount: '{{ old('demo_prod_count', $profile['demographics']['productive_count'] ?? '5.610') }}',
    childCount: '{{ old('demo_child_count', $profile['demographics']['child_count'] ?? '1.825') }}',
    elderlyCount: '{{ old('demo_elderly_count', $profile['demographics']['elderly_count'] ?? '990') }}',

    parseNum(val) {
        if (!val) return 0;
        let cleaned = String(val).replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.-]/g, '');
        let n = parseFloat(cleaned);
        return isNaN(n) ? 0 : n;
    },

    formatNum(n) {
        return new Intl.NumberFormat('id-ID').format(Math.round(n));
    },

    get totalPenduduk() {
        return this.parseNum(this.male) + this.parseNum(this.female);
    },

    get totalPendudukFormatted() {
        return this.formatNum(this.totalPenduduk);
    },

    get totalKelompokUsia() {
        return this.parseNum(this.prodCount) + this.parseNum(this.childCount) + this.parseNum(this.elderlyCount);
    },

    get totalKelompokUsiaFormatted() {
        return this.formatNum(this.totalKelompokUsia);
    },

    get isKelompokUsiaExceeded() {
        return this.totalPenduduk > 0 && this.totalKelompokUsia > this.totalPenduduk;
    },

    get prodPct() {
        let t = this.totalPenduduk;
        return t > 0 ? ((this.parseNum(this.prodCount) / t) * 100).toFixed(1).replace('.', ',') : '0';
    },

    get childPct() {
        let t = this.totalPenduduk;
        return t > 0 ? ((this.parseNum(this.childCount) / t) * 100).toFixed(1).replace('.', ',') : '0';
    },

    get elderlyPct() {
        let t = this.totalPenduduk;
        return t > 0 ? ((this.parseNum(this.elderlyCount) / t) * 100).toFixed(1).replace('.', ',') : '0';
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

    <!-- Banner Navigasi Cepat ke Halaman Statistik Wilayah -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border border-emerald-200/90 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                <i class="fas fa-chart-line"></i>
            </span>
            <div>
                <h4 class="text-xs font-bold text-emerald-950">Statistik Wilayah & Indikator Teritorial (TA 2026)</h4>
                <p class="text-[11px] text-emerald-800 mt-0.5">Data Luas Wilayah (3.82 km²), 32 RT & 8 RW, 2.640 KK, dan Kepadatan Penduduk dikelola di menu khusus tersendiri.</p>
            </div>
        </div>
        <a href="{{ route('admin.beranda.statistik_wilayah') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-xs shrink-0 self-start sm:self-auto">
            <span>Kelola Statistik Wilayah</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <!-- Form 1: Demografi Utama -->
    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="demografi">
        <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-users text-slate-700"></i>
                    <span>Demografi Penduduk Warga</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Total penduduk terhitung otomatis dari jumlah pria dan wanita.</p>
            </div>
            <button type="submit" 
                :disabled="isKelompokUsiaExceeded"
                :class="isKelompokUsiaExceeded ? 'bg-slate-400 cursor-not-allowed opacity-60' : 'bg-slate-700 hover:bg-slate-800'"
                class="px-5 py-2 text-white font-bold text-xs rounded-xl transition shadow-sm">
                Simpan Demografi
            </button>
        </div>

        <!-- Banner Peringatan Realtime jika Kelompok Usia Melebihi Total Penduduk -->
        <div x-show="isKelompokUsiaExceeded" x-cloak class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl flex items-center gap-3 text-xs font-semibold">
            <i class="fas fa-exclamation-triangle text-rose-600 text-base shrink-0"></i>
            <div>
                <span class="font-bold">Peringatan Validasi:</span> Jumlah Kelompok Usia (<span class="font-bold" x-text="totalKelompokUsiaFormatted"></span> jiwa) tidak boleh melebihi total penduduk (<span class="font-bold" x-text="totalPendudukFormatted"></span> jiwa).
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <!-- Field Total Penduduk: Read-Only / Dihitung Otomatis -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>Total Penduduk (Jiwa)</span>
                    <span class="text-[10px] bg-sky-100 text-sky-800 px-2 py-0.5 rounded-full font-bold">
                        <i class="fas fa-calculator mr-1"></i>Otomatis
                    </span>
                </label>
                <div class="relative">
                    <input type="text" name="demo_total" :value="totalPendudukFormatted" readonly class="w-full p-2.5 bg-slate-100/90 text-slate-900 font-black border border-slate-300 rounded-lg cursor-not-allowed select-none focus:outline-none">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                </div>
                <span class="text-[10px] text-slate-500 mt-1 block">Formula: Laki-laki + Perempuan</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jumlah Laki-laki (Jiwa) *</label>
                <input type="text" name="demo_male" x-model="male" required placeholder="Contoh: 4.180" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium">
                <span class="text-[10px] text-slate-400 mt-1 block">Ubah angka untuk memperbarui total realtime</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jumlah Perempuan (Jiwa) *</label>
                <input type="text" name="demo_female" x-model="female" required placeholder="Contoh: 4.245" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium">
                <span class="text-[10px] text-slate-400 mt-1 block">Ubah angka untuk memperbarui total realtime</span>
            </div>
            
            <!-- Usia Produktif -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Usia Produktif (Jumlah)</label>
                <input type="text" name="demo_prod_count" x-model="prodCount" placeholder="Contoh: 5.610" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Persentase Usia Produktif (%)</label>
                <input type="text" :value="prodPct + '%'" class="w-full p-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold" readonly title="Dihitung otomatis">
            </div>

            <!-- Anak/Balita -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Anak & Balita (Jumlah)</label>
                <input type="text" name="demo_child_count" x-model="childCount" placeholder="Contoh: 1.825" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Persentase Anak & Balita (%)</label>
                <input type="text" :value="childPct + '%'" class="w-full p-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold" readonly title="Dihitung otomatis">
            </div>

            <!-- Lansia -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Lansia (Jumlah)</label>
                <input type="text" name="demo_elderly_count" x-model="elderlyCount" placeholder="Contoh: 990" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Persentase Lansia (%)</label>
                <input type="text" :value="elderlyPct + '%'" class="w-full p-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold" readonly title="Dihitung otomatis">
            </div>

            <!-- Ringkasan Akumulasi Usia -->
            <div class="sm:col-span-3 bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                <span class="text-slate-600">
                    <i class="fas fa-info-circle text-slate-400 mr-1"></i>
                    Total Kelompok Usia: <strong x-text="totalKelompokUsiaFormatted"></strong> jiwa 
                    (Batas maksimal: <strong x-text="totalPendudukFormatted"></strong> jiwa)
                </span>
                <span :class="isKelompokUsiaExceeded ? 'text-rose-600 font-bold' : 'text-emerald-600 font-semibold'">
                    <i :class="isKelompokUsiaExceeded ? 'fas fa-times-circle' : 'fas fa-check-circle'"></i>
                    <span x-text="isKelompokUsiaExceeded ? 'Melebihi Total Penduduk' : 'Valid (Dalam Batas)'"></span>
                </span>
            </div>

            <!-- Info Tambahan -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Kepadatan (Jiwa/km²)</label>
                <input type="text" name="demo_density" value="{{ old('demo_density', $profile['demographics']['density'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Rata-rata Anggota Keluarga</label>
                <input type="text" name="demo_avg_family" value="{{ old('demo_avg_family', $profile['demographics']['avg_family_size'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            
            <!-- Hidden inputs so they don't get erased during generic Demografi update -->
            @foreach($profile['demographics']['occupations'] ?? [] as $occ)
                <input type="hidden" name="occ_name[]" value="{{ $occ['name'] }}">
                <input type="hidden" name="occ_sector[]" value="{{ $occ['sector'] ?? 'Lainnya' }}">
                <input type="hidden" name="occ_count[]" value="{{ $occ['count'] }}">
                <input type="hidden" name="occ_pct[]" value="{{ $occ['pct'] }}">
            @endforeach
            @foreach($profile['demographics']['educations'] ?? [] as $edu)
                <input type="hidden" name="edu_name[]" value="{{ $edu['name'] }}">
                <input type="hidden" name="edu_count[]" value="{{ $edu['count'] }}">
                <input type="hidden" name="edu_pct[]" value="{{ $edu['pct'] }}">
            @endforeach
        </div>
    </form>

    @php
        $sectorStyles = [
            'Pertanian'     => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'bar' => 'bg-emerald-500', 'icon' => 'fa-seedling'],
            'Perikanan'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-700',     'border' => 'border-sky-200',     'bar' => 'bg-sky-500',     'icon' => 'fa-fish'],
            'Perdagangan'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'border-amber-200',   'bar' => 'bg-amber-500',   'icon' => 'fa-store'],
            'Jasa'          => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'border' => 'border-indigo-200',  'bar' => 'bg-indigo-500',  'icon' => 'fa-handshake'],
            'PNS/TNI/Polri' => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700',  'border' => 'border-purple-200',  'bar' => 'bg-purple-600',  'icon' => 'fa-shield-halved'],
            'Swasta'        => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'border' => 'border-blue-200',    'bar' => 'bg-blue-500',    'icon' => 'fa-building'],
            'Lainnya'       => ['bg' => 'bg-slate-100',  'text' => 'text-slate-700',   'border' => 'border-slate-200',   'bar' => 'bg-slate-500',   'icon' => 'fa-shapes'],
        ];

        $sectorTotals = [
            'Pertanian' => 0, 'Perikanan' => 0, 'Perdagangan' => 0, 'Jasa' => 0,
            'PNS/TNI/Polri' => 0, 'Swasta' => 0, 'Lainnya' => 0
        ];
        $totalOccPopulation = 0;
        foreach($profile['demographics']['occupations'] ?? [] as $itemOcc) {
            $sec = $itemOcc['sector'] ?? 'Lainnya';
            $cntVal = (float) str_replace(['.', ','], ['', '.'], $itemOcc['count'] ?? 0);
            $sectorTotals[$sec] = ($sectorTotals[$sec] ?? 0) + $cntVal;
            $totalOccPopulation += $cntVal;
        }
    @endphp

    <!-- TABEL 1: PEKERJAAN (MATA PENCAHARIAN) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-briefcase text-slate-700 shrink-0"></i>
                    <span>Tabel Mata Pencaharian</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola data profesi per sektor. Akumulasi tidak boleh melebihi total penduduk.</p>
            </div>
            <button type="button" @click="createOccOpen = true" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>

        <!-- Rekap Distribusi Sektor Interaktif -->
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-chart-pie text-slate-500"></i> Rekap Distribusi 7 Kategori Sektor
                </span>
                <span class="text-xs font-semibold text-slate-500">
                    Total Tercatat: <strong class="text-slate-900">{{ number_format($totalOccPopulation, 0, ',', '.') }}</strong> jiwa
                </span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                @foreach($sectorStyles as $sName => $sStyle)
                    @php
                        $sCount = $sectorTotals[$sName] ?? 0;
                        $sPct = $totalOccPopulation > 0 ? round(($sCount / $totalOccPopulation) * 100, 1) : 0;
                    @endphp
                    <div class="p-2.5 rounded-xl border {{ $sStyle['bg'] }} {{ $sStyle['border'] }} flex flex-col justify-between">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[11px] font-bold {{ $sStyle['text'] }} truncate">{{ $sName }}</span>
                            <i class="fas {{ $sStyle['icon'] }} {{ $sStyle['text'] }} text-[11px] shrink-0"></i>
                        </div>
                        <div class="mt-1 flex items-baseline justify-between">
                            <span class="text-xs font-extrabold text-slate-900">{{ number_format($sCount, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-semibold text-slate-500">{{ $sPct }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[550px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 sm:px-5">Bidang Pekerjaan</th>
                        <th class="py-3 px-4 sm:px-5">Kategori Sektor</th>
                        <th class="py-3 px-4 sm:px-5">Jumlah Orang</th>
                        <th class="py-3 px-4 sm:px-5">Persentase (%)</th>
                        <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($profile['demographics']['occupations'] ?? [] as $index => $occ)
                        @php
                            $occSec = $occ['sector'] ?? 'Lainnya';
                            $occStyle = $sectorStyles[$occSec] ?? $sectorStyles['Lainnya'];
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 sm:px-5 font-bold text-slate-900">{{ $occ['name'] }}</td>
                            <td class="py-3 px-4 sm:px-5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $occStyle['bg'] }} {{ $occStyle['text'] }} {{ $occStyle['border'] }}">
                                    <i class="fas {{ $occStyle['icon'] }} text-[10px]"></i>
                                    {{ $occSec }}
                                </span>
                            </td>
                            <td class="py-3 px-4 sm:px-5 font-semibold text-slate-700">{{ $occ['count'] }} jiwa</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-bold">{{ $occ['pct'] }}%</span>
                            </td>
                            <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button type="button" @click="selectedOcc = {{ json_encode($occ) }}; selectedOccIndex = {{ $index }}; editOccOpen = true;" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>
                                <form action="{{ route('admin.beranda.statistik.destroy', ['type' => 'occupation', 'index' => $index]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pekerjaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada data pekerjaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- TABEL 2: TINGKAT PENDIDIKAN -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-slate-700 shrink-0"></i>
                    <span>Tabel Tingkat Pendidikan</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Total akumulasi tingkat pendidikan tidak boleh melebihi total penduduk.</p>
            </div>
            <button type="button" @click="createEduOpen = true" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>

        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 sm:px-5">Tingkat Pendidikan</th>
                        <th class="py-3 px-4 sm:px-5">Jumlah Orang</th>
                        <th class="py-3 px-4 sm:px-5">Persentase (%)</th>
                        <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($profile['demographics']['educations'] ?? [] as $index => $edu)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 sm:px-5 font-bold text-slate-900">{{ $edu['name'] }}</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">{{ $edu['count'] }} jiwa</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-bold">{{ $edu['pct'] }}%</span>
                            </td>
                            <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button type="button" @click="selectedEdu = {{ json_encode($edu) }}; selectedEduIndex = {{ $index }}; editEduOpen = true;" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>
                                <form action="{{ route('admin.beranda.statistik.destroy', ['type' => 'education', 'index' => $index]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jenjang pendidikan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-400">Belum ada data pendidikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL OCCUPATION (PEKERJAAN) -->
    <!-- Create -->
    <div x-show="createOccOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="createOccOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200">
                <form action="{{ route('admin.beranda.statistik.store', 'occupation') }}" method="POST">@csrf
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-800">Tambah Data Mata Pencaharian</h3>
                            <p class="text-[11px] text-slate-400">Formulir penambahan data profesi warga desa</p>
                        </div>
                        <button type="button" @click="createOccOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Nama Jenis Pekerjaan / Mata Pencaharian <span class="text-rose-500">*</span></label>
                            <input type="text" name="stat_name" required placeholder="Contoh: Wiraswasta / Pedagang Kelontong" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Kategori Sektor <span class="text-rose-500">*</span></label>
                            <select name="stat_sector" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                                <option value="Pertanian">Pertanian</option>
                                <option value="Perikanan">Perikanan</option>
                                <option value="Perdagangan">Perdagangan</option>
                                <option value="Jasa">Jasa</option>
                                <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                <option value="Swasta" selected>Swasta</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Jumlah Penduduk yang Bekerja <span class="text-rose-500">*</span></label>
                            <input type="text" name="stat_count" required placeholder="Contoh: 1.250" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                            <span class="text-[10px] text-slate-400 mt-1 block">Akumulasi tidak boleh melebihi total penduduk (<span x-text="totalPendudukFormatted"></span> jiwa)</span>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createOccOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-md transition">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit -->
    <div x-show="editOccOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="editOccOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200">
                <template x-if="selectedOcc !== null">
                    <form :action="'{{ url('admin/kelola-beranda/statistik/occupation') }}/' + selectedOccIndex" method="POST">@csrf @method('PUT')
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-800">Edit Mata Pencaharian</h3>
                                <p class="text-[11px] text-slate-400">Perbarui rincian profesi dan sektor terkait</p>
                            </div>
                            <button type="button" @click="editOccOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold mb-1 text-slate-700">Nama Jenis Pekerjaan / Mata Pencaharian <span class="text-rose-500">*</span></label>
                                <input type="text" name="stat_name" x-model="selectedOcc.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                            </div>
                            <div>
                                <label class="block font-bold mb-1 text-slate-700">Kategori Sektor <span class="text-rose-500">*</span></label>
                                <select name="stat_sector" x-model="selectedOcc.sector" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                                    <option value="Pertanian">Pertanian</option>
                                    <option value="Perikanan">Perikanan</option>
                                    <option value="Perdagangan">Perdagangan</option>
                                    <option value="Jasa">Jasa</option>
                                    <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                    <option value="Swasta">Swasta</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold mb-1 text-slate-700">Jumlah Penduduk yang Bekerja <span class="text-rose-500">*</span></label>
                                <input type="text" name="stat_count" x-model="selectedOcc.count" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                                <span class="text-[10px] text-slate-400 mt-1 block">Akumulasi tidak boleh melebihi total penduduk (<span x-text="totalPendudukFormatted"></span> jiwa)</span>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="editOccOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-md transition">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>


    <!-- MODAL EDUCATION (PENDIDIKAN) -->
    <!-- Create -->
    <div x-show="createEduOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="createEduOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200">
                <form action="{{ route('admin.beranda.statistik.store', 'education') }}" method="POST">@csrf
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">Tambah Tingkat Pendidikan</h3>
                        <button type="button" @click="createEduOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold mb-1">Tingkat Pendidikan <span class="text-rose-500">*</span></label>
                            <input type="text" name="stat_name" required placeholder="Contoh: Tamat SMA / SMK" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                        </div>
                        <div>
                            <label class="block font-bold mb-1">Jumlah Orang <span class="text-rose-500">*</span></label>
                            <input type="text" name="stat_count" required placeholder="Contoh: 2.100" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                            <span class="text-[10px] text-slate-400 mt-1 block">Akumulasi tidak boleh melebihi total penduduk (<span x-text="totalPendudukFormatted"></span> jiwa)</span>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createEduOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-slate-700 hover:bg-slate-800 text-white font-bold rounded-xl shadow-md transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit -->
    <div x-show="editEduOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="editEduOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200">
                <template x-if="selectedEdu !== null">
                    <form :action="'{{ url('admin/kelola-beranda/statistik/education') }}/' + selectedEduIndex" method="POST">@csrf @method('PUT')
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800">Edit Tingkat Pendidikan</h3>
                            <button type="button" @click="editEduOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold mb-1">Tingkat Pendidikan <span class="text-rose-500">*</span></label>
                                <input type="text" name="stat_name" x-model="selectedEdu.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                            </div>
                            <div>
                                <label class="block font-bold mb-1">Jumlah Orang <span class="text-rose-500">*</span></label>
                                <input type="text" name="stat_count" x-model="selectedEdu.count" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600">
                                <span class="text-[10px] text-slate-400 mt-1 block">Akumulasi tidak boleh melebihi total penduduk (<span x-text="totalPendudukFormatted"></span> jiwa)</span>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="editEduOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-slate-700 hover:bg-slate-800 text-white font-bold rounded-xl shadow-md transition">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

