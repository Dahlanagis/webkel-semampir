@extends('layouts.admin')

@section('title', 'Statistik & Demografi Warga')
@section('header-title', 'Statistik & Demografi Warga')
@section('header-subtitle', 'Mengatur data statistik penduduk, pekerjaan, dan pendidikan.')

@section('content')
<div class="space-y-6" x-data="{
    // Kartu Statistik modal & data
    cardModalOpen: false,
    editingCardIndex: null,
    cardForm: {
        id: null,
        icon: 'fas fa-users',
        title: '',
        value: '',
        color: 'EMERALD',
        is_active: true
    },
    cards: {{ json_encode($profile['stats_cards'] ?? \App\Http\Controllers\Admin\VillageProfileController::getDefaultStatsCards()) }},

    // Demografi
    male: '{{ old('demo_male', $profile['demographics']['male'] ?? '9000') }}',
    female: '{{ old('demo_female', $profile['demographics']['female'] ?? '4.245') }}',

    // Kelompok Usia
    balitaCount: '{{ old('demo_balita_count', $profile['demographics']['balita_count'] ?? '900') }}',
    anakCount: '{{ old('demo_anak_count', $profile['demographics']['anak_count'] ?? '999') }}',
    remajaCount: '{{ old('demo_remaja_count', $profile['demographics']['remaja_count'] ?? '900') }}',
    prodCount: '{{ old('demo_prod_count', $profile['demographics']['productive_count'] ?? '999') }}',
    elderlyCount: '{{ old('demo_elderly_count', $profile['demographics']['elderly_count'] ?? '2070') }}',

    // Tingkat Pendidikan
    eduBelum: '{{ old('edu_belum', collect($profile['demographics']['educations'] ?? [])->firstWhere('name', 'Belum / Tidak Sekolah')['count'] ?? '888') }}',
    eduSd: '{{ old('edu_sd', collect($profile['demographics']['educations'] ?? [])->firstWhere('name', 'SD / Sederajat')['count'] ?? '9999') }}',
    eduSmp: '{{ old('edu_smp', collect($profile['demographics']['educations'] ?? [])->firstWhere('name', 'SMP / Sederajat')['count'] ?? '888') }}',
    eduSma: '{{ old('edu_sma', collect($profile['demographics']['educations'] ?? [])->firstWhere('name', 'SMA / SMK / Sederajat')['count'] ?? '888') }}',
    eduSarjana: '{{ old('edu_sarjana', collect($profile['demographics']['educations'] ?? [])->firstWhere('name', 'Diploma / Sarjana (D3/S1/S2/S3)')['count'] ?? (collect($profile['demographics']['educations'] ?? [])->firstWhere('name', 'Diploma / Sarjana (D3, S1, S2, S3)')['count'] ?? '233')) }}',

    // Pekerjaan
    occupations: {{ json_encode(!empty($profile['demographics']['occupations']) ? $profile['demographics']['occupations'] : [
        ['name' => 'Wiraswasta / Pedagang', 'count' => '88'],
        ['name' => 'Karyawan Swasta', 'count' => '688'],
        ['name' => 'ASN / PNS / TNI / Polri', 'count' => '90'],
        ['name' => 'Buruh Harian', 'count' => '100'],
        ['name' => 'Petani / Peternak', 'count' => '3000'],
        ['name' => 'Pelajar / Mahasiswa', 'count' => '1000'],
        ['name' => 'Belum Bekerja', 'count' => '79'],
        ['name' => 'contoh', 'count' => '590'],
    ]) }},

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
        return this.parseNum(this.balitaCount) + this.parseNum(this.anakCount) + this.parseNum(this.remajaCount) + this.parseNum(this.prodCount) + this.parseNum(this.elderlyCount);
    },

    get sisaUsia() {
        let diff = this.totalPenduduk - this.totalKelompokUsia;
        return this.formatNum(diff >= 0 ? diff : 0);
    },

    get totalPendidikan() {
        return this.parseNum(this.eduBelum) + this.parseNum(this.eduSd) + this.parseNum(this.eduSmp) + this.parseNum(this.eduSma) + this.parseNum(this.eduSarjana);
    },

    get sisaPendidikan() {
        let diff = this.totalPenduduk - this.totalPendidikan;
        return this.formatNum(diff >= 0 ? diff : 0);
    },

    get totalPekerjaan() {
        return this.occupations.reduce((sum, item) => sum + this.parseNum(item.count), 0);
    },

    get sisaPekerjaan() {
        let diff = this.totalPenduduk - this.totalPekerjaan;
        return this.formatNum(diff >= 0 ? diff : 0);
    },

    addOccupation() {
        this.occupations.push({ name: '', count: '0' });
    },

    removeOccupation(idx) {
        this.occupations.splice(idx, 1);
    },

    openAddCard() {
        this.editingCardIndex = null;
        this.cardForm = {
            id: Date.now(),
            icon: 'fas fa-chart-line',
            title: '',
            value: '',
            color: 'EMERALD',
            is_active: true
        };
        this.cardModalOpen = true;
    },

    editCard(idx) {
        this.editingCardIndex = idx;
        this.cardForm = JSON.parse(JSON.stringify(this.cards[idx]));
        this.cardModalOpen = true;
    },

    saveCard() {
        if (!this.cardForm.title || !this.cardForm.value) {
            alert('Judul dan Nilai kartu wajib diisi');
            return;
        }
        if (this.editingCardIndex !== null) {
            this.cards[this.editingCardIndex] = JSON.parse(JSON.stringify(this.cardForm));
        } else {
            this.cards.push(JSON.parse(JSON.stringify(this.cardForm)));
        }
        this.cardModalOpen = false;
        this.$nextTick(() => {
            document.getElementById('form-kartu-statistik').submit();
        });
    },

    deleteCard(idx) {
        if (confirm('Hapus kartu statistik ini?')) {
            this.cards.splice(idx, 1);
            this.$nextTick(() => {
                document.getElementById('form-kartu-statistik').submit();
            });
        }
    },

    toggleCard(idx) {
        this.$nextTick(() => {
            document.getElementById('form-kartu-statistik').submit();
        });
    }
}">

    <!-- Alert Status -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-xs">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-xs">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-xs">
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
    <!-- 1. KARTU STATISTIK BERANDA                                                -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form id="form-kartu-statistik" action="{{ route('admin.beranda.update') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="kartu_statistik">
            <input type="hidden" name="stats_cards" :value="JSON.stringify(cards)">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Title & Info -->
                <div class="lg:col-span-3 flex flex-col justify-between h-full pr-0 lg:pr-4 border-b lg:border-b-0 lg:border-r border-slate-100 pb-5 lg:pb-0">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#00a664] flex items-center justify-center text-sm font-bold shadow-2xs">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <h3 class="font-black text-slate-800 text-base leading-tight">
                                Kartu Statistik Beranda
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed mt-2">
                            Mengelola daftar kartu statistik (seperti Jumlah Penduduk, KK) yang tampil di beranda.
                        </p>
                    </div>

                    <div class="mt-6">
                        <button type="button" @click="openAddCard()" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#009b5a] hover:bg-[#00874e] text-white text-xs font-bold transition shadow-sm">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah Kartu</span>
                        </button>
                    </div>
                </div>

                <!-- Right Column: Table List of Cards -->
                <div class="lg:col-span-9 overflow-x-auto min-w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pb-3 px-3">IKON & JUDUL</th>
                                <th class="pb-3 px-3">NILAI / ANGKA</th>
                                <th class="pb-3 px-3">WARNA TEMA</th>
                                <th class="pb-3 px-3">STATUS TAMPIL</th>
                                <th class="pb-3 px-3 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium">
                            <template x-for="(card, index) in cards" :key="index">
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs shrink-0">
                                                <i :class="card.icon || 'fas fa-chart-line'"></i>
                                            </span>
                                            <span class="font-bold text-slate-800 text-xs" x-text="card.title"></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 font-extrabold text-[#009b5a] text-xs whitespace-nowrap" x-text="card.value"></td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200/60 text-[10px] font-black uppercase" 
                                              x-text="card.color || 'EMERALD'"></span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="card.is_active" @change="toggleCard(index)" class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#009b5a]"></div>
                                        </label>
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap space-x-2">
                                        <button type="button" @click="editCard(index)" 
                                                class="px-2.5 py-1 rounded-md border border-slate-200 text-[11px] font-bold text-slate-700 hover:bg-slate-100 transition">
                                            Edit
                                        </button>
                                        <button type="button" @click="deleteCard(index)" 
                                                class="p-1 rounded-md text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition" title="Hapus Kartu">
                                            <i class="far fa-trash-can text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. DEMOGRAFI PENDUDUK WARGA                                               -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('admin.beranda.update') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="demografi_warga">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 leading-snug">
                        Demografi Penduduk Warga
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Total penduduk otomatis dihitung dari Laki-laki + Perempuan.
                    </p>
                </div>
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-[#009b5a] hover:bg-[#00874e] text-white text-xs font-bold transition shadow-sm shrink-0 self-start sm:self-auto">
                    Simpan Demografi
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5">
                <!-- Total Populasi Penduduk (Otomatis) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Total Populasi Penduduk 
                        <span class="ml-1 text-[9px] font-extrabold uppercase px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded border border-slate-200/60">OTOMATIS</span>
                    </label>
                    <input type="text" :value="totalPendudukFormatted" readonly 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-black text-slate-900 cursor-not-allowed select-none focus:outline-none">
                </div>

                <!-- Laki-laki -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Laki-laki</label>
                    <input type="text" name="demo_male" x-model="male" required placeholder="9000" 
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Perempuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Perempuan</label>
                    <input type="text" name="demo_female" x-model="female" required placeholder="4.245" 
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. KELOLA DATA KELOMPOK USIA                                              -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('admin.beranda.update') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="kelompok_usia">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 leading-snug">
                        Kelola Data Kelompok Usia
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klasifikasi usia warga untuk statistik.
                    </p>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <div class="text-right">
                        <div class="text-xs text-slate-600 font-medium">
                            Total Terisi: <strong class="text-emerald-600 font-bold" x-text="totalKelompokUsia"></strong> / <span class="font-bold text-slate-800" x-text="totalPendudukFormatted"></span> Jiwa
                        </div>
                        <div class="text-[10px] text-slate-400">
                            Sisa kuota: <span x-text="sisaUsia"></span> Jiwa
                        </div>
                    </div>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl bg-[#009b5a] hover:bg-[#00874e] text-white text-xs font-bold transition shadow-sm">
                        Simpan
                    </button>
                </div>
            </div>

            <div class="space-y-4 pt-5">
                <!-- Row 1: Balita, Anak, Remaja -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Balita (0-5 thn)</label>
                        <input type="text" name="demo_balita_count" x-model="balitaCount" placeholder="900" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Anak-anak (6-12 thn)</label>
                        <input type="text" name="demo_anak_count" x-model="anakCount" placeholder="999" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Remaja (13-17 thn)</label>
                        <input type="text" name="demo_remaja_count" x-model="remajaCount" placeholder="900" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Row 2: Produktif, Lansia -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Usia Produktif (18-59 thn)</label>
                        <input type="text" name="demo_prod_count" x-model="prodCount" placeholder="999" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Lansia (60+ thn)</label>
                        <input type="text" name="demo_elderly_count" x-model="elderlyCount" placeholder="2070" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. KELOLA DATA TINGKAT PENDIDIKAN                                         -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('admin.beranda.update') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="pendidikan">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 leading-snug">
                        Kelola Data Tingkat Pendidikan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Distribusi pendidikan warga.
                    </p>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <div class="text-right">
                        <div class="text-xs text-slate-600 font-medium">
                            Total Terisi: <strong class="text-emerald-600 font-bold" x-text="totalPendidikan"></strong> / <span class="font-bold text-slate-800" x-text="totalPendudukFormatted"></span> Jiwa
                        </div>
                        <div class="text-[10px] text-slate-400">
                            Sisa kuota: <span x-text="sisaPendidikan"></span> Jiwa
                        </div>
                    </div>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl bg-[#009b5a] hover:bg-[#00874e] text-white text-xs font-bold transition shadow-sm">
                        Simpan
                    </button>
                </div>
            </div>

            <div class="space-y-4 pt-5">
                <!-- Row 1: Belum Sekolah, SD, SMP -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Belum / Tidak Sekolah</label>
                        <input type="text" name="edu_belum" x-model="eduBelum" placeholder="888" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">SD / Sederajat</label>
                        <input type="text" name="edu_sd" x-model="eduSd" placeholder="9999" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">SMP / Sederajat</label>
                        <input type="text" name="edu_smp" x-model="eduSmp" placeholder="888" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Row 2: SMA, Diploma/Sarjana -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">SMA / SMK / Sederajat</label>
                        <input type="text" name="edu_sma" x-model="eduSma" placeholder="888" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Diploma / Sarjana (D3/S1/S2/S3)</label>
                        <input type="text" name="edu_sarjana" x-model="eduSarjana" placeholder="233" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. KELOLA DATA MATA PENCAHARIAN / PEKERJAAN                               -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('admin.beranda.update') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="pekerjaan">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 leading-snug">
                        Kelola Data Mata Pencaharian / Pekerjaan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Anda dapat menambah atau menghapus kategori profesi.
                    </p>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <div class="text-right">
                        <div class="text-xs text-slate-600 font-medium">
                            Total Terisi: <strong class="text-emerald-600 font-bold" x-text="totalPekerjaan"></strong> / <span class="font-bold text-slate-800" x-text="totalPendudukFormatted"></span> Jiwa
                        </div>
                        <div class="text-[10px] text-slate-400">
                            Sisa kuota: <span x-text="sisaPekerjaan"></span> Jiwa
                        </div>
                    </div>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl bg-[#009b5a] hover:bg-[#00874e] text-white text-xs font-bold transition shadow-sm">
                        Simpan
                    </button>
                </div>
            </div>

            <!-- List Pekerjaan -->
            <div class="space-y-3 pt-5">
                <template x-for="(item, index) in occupations" :key="index">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <input type="text" name="occ_name[]" x-model="item.name" placeholder="Nama Pekerjaan / Profesi" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="w-32 sm:w-44">
                            <input type="text" name="occ_count[]" x-model="item.count" placeholder="Jumlah" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <button type="button" @click="removeOccupation(index)" 
                                class="w-9 h-9 rounded-xl border border-rose-200 bg-rose-50/60 text-rose-500 hover:bg-rose-100 flex items-center justify-center text-xs transition shrink-0" 
                                title="Hapus Profesi">
                            <i class="far fa-trash-can"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Profesi -->
            <div class="pt-4">
                <button type="button" @click="addOccupation()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white text-slate-700 text-xs font-bold transition shadow-2xs">
                    <i class="fas fa-plus text-[10px] text-emerald-600"></i>
                    <span>Tambah Kategori Profesi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. KELOLA DISTRIBUSI WILAYAH (RT & RW)                                    -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('admin.beranda.update') }}" method="POST">
            @csrf
            <input type="hidden" name="section" value="wilayah">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 leading-snug">
                        Kelola Distribusi Wilayah (RT & RW)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Informasi batas, fasilitas, dan penduduk per RW.
                    </p>
                </div>
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-[#009b5a] hover:bg-[#00874e] text-white text-xs font-bold transition shadow-sm shrink-0 self-start sm:self-auto">
                    Simpan Wilayah
                </button>
            </div>

            <!-- Sub 1: Informasi Umum Wilayah -->
            <div class="pt-5 space-y-4">
                <h4 class="text-xs font-bold text-slate-800">Informasi Umum Wilayah</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Jumlah RW</label>
                        <input type="text" name="ter_rw" value="{{ old('ter_rw', $profile['territory']['rw'] ?? '08') }}" placeholder="08" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Jumlah RT</label>
                        <input type="text" name="ter_rt" value="{{ old('ter_rt', $profile['territory']['rt'] ?? '35') }}" placeholder="35" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Batas Utara</label>
                        <input type="text" name="ter_north" value="{{ old('ter_north', $profile['territory']['north'] ?? 'Desa Kalibuntu') }}" placeholder="Desa Kalibuntu" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Batas Timur</label>
                        <input type="text" name="ter_east" value="{{ old('ter_east', $profile['territory']['east'] ?? 'Kelurahan Kraksaan Wetan') }}" placeholder="Kelurahan Kraksaan Wetan" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Batas Selatan</label>
                        <input type="text" name="ter_south" value="{{ old('ter_south', $profile['territory']['south'] ?? 'Desa Alassumur Kulon') }}" placeholder="Desa Alassumur Kulon" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Batas Barat</label>
                        <input type="text" name="ter_west" value="{{ old('ter_west', $profile['territory']['west'] ?? 'Desa Sidomukti') }}" placeholder="Desa Sidomukti" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Sub 2: Fasilitas Umum -->
            <div class="pt-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-800">Fasilitas Umum</h4>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Sekolah</label>
                        <input type="text" name="ter_schools" value="{{ old('ter_schools', $profile['territory']['schools'] ?? '9') }}" placeholder="9" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Ibadah</label>
                        <input type="text" name="ter_mosques" value="{{ old('ter_mosques', $profile['territory']['mosques'] ?? '12') }}" placeholder="12" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Kesehatan</label>
                        <input type="text" name="ter_health" value="{{ old('ter_health', $profile['territory']['health'] ?? '8') }}" placeholder="8" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pasar</label>
                        <input type="text" name="ter_markets" value="{{ old('ter_markets', $profile['territory']['markets'] ?? '2') }}" placeholder="2" 
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- MODAL EDIT / TAMBAH KARTU STATISTIK BERANDA -->
    <div x-show="cardModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="cardModalOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm" x-text="editingCardIndex !== null ? 'Edit Kartu Statistik' : 'Tambah Kartu Statistik'"></h3>
                        <p class="text-[11px] text-slate-400">Atur judul, nilai, ikon, dan warna kartu beranda</p>
                    </div>
                    <button type="button" @click="cardModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold mb-1 text-slate-700">Judul Kartu <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="cardForm.title" required placeholder="Contoh: Jumlah Penduduk" 
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold mb-1 text-slate-700">Nilai / Angka <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="cardForm.value" required placeholder="Contoh: 13.245 atau 3,82 km²" 
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold mb-1 text-slate-700">Pilih Ikon FontAwesome <span class="text-rose-500">*</span></label>
                        <select x-model="cardForm.icon" 
                                class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-medium text-slate-800">
                            <option value="fas fa-users">Ikon Penduduk / Warga (fas fa-users)</option>
                            <option value="far fa-id-card">Ikon Kepala Keluarga / KTP (far fa-id-card)</option>
                            <option value="fas fa-signs-post">Ikon Wilayah RT/RW / Rambu (fas fa-signs-post)</option>
                            <option value="fas fa-industry">Ikon Luas Wilayah / Bangunan (fas fa-industry)</option>
                            <option value="fas fa-chart-area">Ikon Grafik Area (fas fa-chart-area)</option>
                            <option value="fas fa-building">Ikon Perkantoran / Gedung (fas fa-building)</option>
                            <option value="fas fa-coins">Ikon Keuangan / APBD (fas fa-coins)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold mb-1 text-slate-700">Warna Tema</label>
                        <select x-model="cardForm.color" 
                                class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-medium text-slate-800">
                            <option value="EMERALD">EMERALD (Hijau Mint)</option>
                            <option value="BLUE">BLUE (Biru)</option>
                            <option value="AMBER">AMBER (Kuning / Oranye)</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 mt-2">
                            <input type="checkbox" x-model="cardForm.is_active" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span>Tampilkan Kartu di Beranda</span>
                        </label>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="cardModalOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                    <button type="button" @click="saveCard()" class="px-5 py-2 bg-[#009b5a] hover:bg-[#00874e] text-white font-bold rounded-xl shadow-md transition">Simpan Kartu</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
