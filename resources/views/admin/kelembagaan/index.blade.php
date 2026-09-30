@extends('layouts.admin')

@section('title', 'Kelembagaan Desa & BUMDes')
@section('header-title', 'Kelembagaan Desa (LKD & BUMDes)')
@section('header-subtitle', 'Kelola data Lembaga Kemasyarakatan Desa (LKD), BUMDes, dan lembaga desa lainnya secara terpadu')

@section('content')
<div class="space-y-6" x-data="{ 
    createModalOpen: false, 
    editModalOpen: false, 
    formData: {
        id: null,
        nama_lembaga: '',
        singkatan: '',
        jenis_lembaga: 'LKD',
        nomor_sk_pendirian: '',
        tanggal_sk: '',
        dasar_hukum: '',
        nama_ketua: '',
        kontak: '',
        alamat_kantor: '',
        deskripsi_profil: '',
        status_aktif: true,
        // BUMDes Detail
        nomor_badan_hukum_kemenkumham: '',
        tahun_pendirian: '',
        npwp_bumdes: '',
        kategori_status: 'Berkembang',
        permodalan_awal: 0,
        total_aset: 0,
        omzet_terakhir: 0,
        daftar_unit_usaha: '',
        nama_penasihat: '',
        nama_pelaksana_operasional: '',
        nama_pengawas: ''
    },
    resetForm() {
        this.formData = {
            id: null,
            nama_lembaga: '',
            singkatan: '',
            jenis_lembaga: 'LKD',
            nomor_sk_pendirian: '',
            tanggal_sk: '',
            dasar_hukum: '',
            nama_ketua: '',
            kontak: '',
            alamat_kantor: '',
            deskripsi_profil: '',
            status_aktif: true,
            nomor_badan_hukum_kemenkumham: '',
            tahun_pendirian: '',
            npwp_bumdes: '',
            kategori_status: 'Berkembang',
            permodalan_awal: 0,
            total_aset: 0,
            omzet_terakhir: 0,
            daftar_unit_usaha: '',
            nama_penasihat: '',
            nama_pelaksana_operasional: '',
            nama_pengawas: ''
        };
    },
    openCreate() {
        this.resetForm();
        this.createModalOpen = true;
    },
    openEdit(item) {
        this.resetForm();
        this.formData.id = item.id;
        this.formData.nama_lembaga = item.nama_lembaga || '';
        this.formData.singkatan = item.singkatan || '';
        this.formData.jenis_lembaga = item.jenis_lembaga || 'LKD';
        this.formData.nomor_sk_pendirian = item.nomor_sk_pendirian || '';
        this.formData.tanggal_sk = item.tanggal_sk ? item.tanggal_sk.substring(0, 10) : '';
        this.formData.dasar_hukum = item.dasar_hukum || '';
        this.formData.nama_ketua = item.nama_ketua || '';
        this.formData.kontak = item.kontak || '';
        this.formData.alamat_kantor = item.alamat_kantor || '';
        this.formData.deskripsi_profil = item.deskripsi_profil || '';
        this.formData.status_aktif = item.status_aktif ? true : false;

        if (item.bumdes_detail) {
            this.formData.nomor_badan_hukum_kemenkumham = item.bumdes_detail.nomor_badan_hukum_kemenkumham || '';
            this.formData.tahun_pendirian = item.bumdes_detail.tahun_pendirian || '';
            this.formData.npwp_bumdes = item.bumdes_detail.npwp_bumdes || '';
            this.formData.kategori_status = item.bumdes_detail.kategori_status || 'Berkembang';
            this.formData.permodalan_awal = item.bumdes_detail.permodalan_awal || 0;
            this.formData.total_aset = item.bumdes_detail.total_aset || 0;
            this.formData.omzet_terakhir = item.bumdes_detail.omzet_terakhir || 0;
            this.formData.daftar_unit_usaha = item.bumdes_detail.daftar_unit_usaha || '';
            this.formData.nama_penasihat = item.bumdes_detail.nama_penasihat || '';
            this.formData.nama_pelaksana_operasional = item.bumdes_detail.nama_pelaksana_operasional || '';
            this.formData.nama_pengawas = item.bumdes_detail.nama_pengawas || '';
        }

        this.editModalOpen = true;
    }
}">

    <!-- Alert Flash -->
    @if(session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-xs">
            <div class="flex items-center gap-2 mb-1.5 font-bold">
                <i class="fas fa-circle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. STATS OVERVIEW CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Lembaga -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Lembaga</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $totalLembaga }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Semua entitas terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-people-roof"></i>
            </div>
        </div>

        <!-- LKD -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Lembaga LKD</p>
                <h3 class="text-2xl font-black text-blue-700 mt-1">{{ $totalLkd }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">RT/RW, PKK, Posyandu, dll</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-users-rectangle"></i>
            </div>
        </div>

        <!-- BUMDes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">BUMDes Terdata</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ $totalBumdes }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Badan Usaha Milik Desa</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-store"></i>
            </div>
        </div>

        <!-- Unit Usaha Berjalan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Unit Usaha BUMDes</p>
                <h3 class="text-2xl font-black text-indigo-700 mt-1">{{ $unitUsahaCount }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Sektor ekonomi aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- 2. FILTER & ACTION BAR -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Filter Tabs / Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
            <a href="{{ route('admin.kelembagaan.index') }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ !request('jenis') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $totalLembaga }})
            </a>
            <a href="{{ route('admin.kelembagaan.index', ['jenis' => 'LKD']) }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('jenis') === 'LKD' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                LKD
            </a>
            <a href="{{ route('admin.kelembagaan.index', ['jenis' => 'BUMDes']) }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('jenis') === 'BUMDes' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                BUMDes
            </a>
            <a href="{{ route('admin.kelembagaan.index', ['jenis' => 'Lembaga Pemerintahan']) }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('jenis') === 'Lembaga Pemerintahan' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pemerintahan
            </a>
            <a href="{{ route('admin.kelembagaan.index', ['jenis' => 'Lembaga Adat']) }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('jenis') === 'Lembaga Adat' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Lembaga Adat
            </a>
        </div>

        <!-- Search & Action -->
        <div class="flex items-center gap-2.5">
            <form method="GET" action="{{ route('admin.kelembagaan.index') }}" class="flex items-center gap-2">
                @if(request('jenis'))
                    <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                @endif
                <div class="relative min-w-[200px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / ketua..." 
                           class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-400 transition">
                </div>
            </form>

            <button type="button" @click="openCreate()" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition transform hover:-translate-y-0.5 shrink-0">
                <i class="fas fa-plus"></i>
                <span>Tambah Lembaga</span>
            </button>
        </div>
    </div>

    <!-- 3. DATA TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4">Nama Lembaga & Legalitas</th>
                        <th class="py-3.5 px-4">Jenis Lembaga</th>
                        <th class="py-3.5 px-4">Pimpinan / Narahubung</th>
                        <th class="py-3.5 px-4">Detail Khusus (BUMDes)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($lembagas as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-bold">
                                {{ $lembagas->firstItem() + $index }}
                            </td>

                            <!-- Nama Lembaga -->
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 text-[13px] flex items-center gap-2">
                                    <span>{{ $item->nama_lembaga }}</span>
                                    @if($item->singkatan)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200">
                                            {{ $item->singkatan }}
                                        </span>
                                    @endif
                                </div>
                                @if($item->nomor_sk_pendirian)
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                        <i class="fas fa-file-contract text-slate-400 text-[10px]"></i>
                                        <span>SK: <strong>{{ $item->nomor_sk_pendirian }}</strong></span>
                                        @if($item->tanggal_sk)
                                            <span class="text-slate-400">({{ \Carbon\Carbon::parse($item->tanggal_sk)->translatedFormat('d M Y') }})</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Jenis Lembaga Badge -->
                            <td class="py-3.5 px-4">
                                @php $badge = $item->jenis_badge; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badge['class'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>

                            <!-- Pimpinan & Kontak -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">
                                    {{ $item->nama_ketua ?: '-' }}
                                </div>
                                @if($item->kontak)
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <i class="fas fa-phone text-slate-400 text-[10px]"></i> {{ $item->kontak }}
                                    </div>
                                @endif
                            </td>

                            <!-- Detail Khusus BUMDes -->
                            <td class="py-3.5 px-4">
                                @if($item->jenis_lembaga === 'BUMDes' && $item->bumdesDetail)
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] border {{ $item->bumdesDetail->kategori_badge['class'] }}">
                                                Status: {{ $item->bumdesDetail->kategori_badge['label'] }}
                                            </span>
                                            @if($item->bumdesDetail->tahun_pendirian)
                                                <span class="text-[10px] text-slate-400">Est. {{ $item->bumdesDetail->tahun_pendirian }}</span>
                                            @endif
                                        </div>
                                        @if($item->bumdesDetail->nomor_badan_hukum_kemenkumham)
                                            <div class="text-[10px] text-slate-600 font-mono truncate max-w-[200px]" title="{{ $item->bumdesDetail->nomor_badan_hukum_kemenkumham }}">
                                                <i class="fas fa-scale-balanced text-emerald-600"></i> {{ $item->bumdesDetail->nomor_badan_hukum_kemenkumham }}
                                            </div>
                                        @endif
                                        @if($item->bumdesDetail->omzet_terakhir > 0)
                                            <div class="text-[10px] text-emerald-700 font-bold">
                                                Omzet: Rp {{ number_format($item->bumdesDetail->omzet_terakhir, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">- Data LKD Umum -</span>
                                @endif
                            </td>

                            <!-- Status Aktif -->
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('admin.kelembagaan.toggle', $item->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold transition {{ $item->status_aktif ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->status_aktif ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1.5">
                                <button type="button" 
                                        @click="openEdit({{ json_encode($item) }})"
                                        class="p-2 text-slate-600 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition inline-flex items-center justify-center">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>
                                
                                <form action="{{ route('admin.kelembagaan.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data lembaga desa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition inline-flex items-center justify-center">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fas fa-people-roof"></i>
                                </div>
                                <p class="font-bold text-slate-600 text-sm">Belum Ada Data Kelembagaan Desa</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Tambah Lembaga" untuk menambahkan lembaga baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lembagas->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $lembagas->links() }}
            </div>
        @endif
    </div>

    <!-- 4. MODAL CREATE & EDIT KELEMBAGAAN DESA (DENGAN FORM DINAMIS BUMDES) -->
    <!-- CREATE MODAL -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="createModalOpen" @click="createModalOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <div x-show="createModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-2xl w-full border border-slate-200 my-8">
                <form action="{{ route('admin.kelembagaan.store') }}" method="POST">
                    @csrf
                    
                    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 px-6 py-4 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white/10 text-indigo-300 flex items-center justify-center text-sm">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h3 class="text-base font-bold">Tambah Kelembagaan Desa Baru</h3>
                        </div>
                        <button type="button" @click="createModalOpen = false" class="text-slate-300 hover:text-white transition">
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        <!-- Jenis Lembaga (Pemicu Form Dinamis) -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jenis Kelembagaan <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_lembaga" x-model="formData.jenis_lembaga" required 
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold transition">
                                <option value="LKD">Lembaga Kemasyarakatan Desa (LKD) - RT/RW, PKK, Posyandu, Karang Taruna, LPMD</option>
                                <option value="BUMDes">Badan Usaha Milik Desa (BUMDes) / Lembaga Usaha Ekonomi</option>
                                <option value="Lembaga Pemerintahan">Lembaga Pemerintahan Desa / Kelurahan</option>
                                <option value="Lembaga Adat">Lembaga Adat / Komunitas Budaya</option>
                            </select>
                        </div>

                        <!-- Grid: Nama Lembaga & Singkatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Lembaga <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama_lembaga" x-model="formData.nama_lembaga" required placeholder="Contoh: TP-PKK Kelurahan Semampir" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-semibold transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Singkatan / Alias
                                </label>
                                <input type="text" name="singkatan" x-model="formData.singkatan" placeholder="Contoh: PKK" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- FORM DINAMIS KHUSUS BUMDES (CONDITIONAL FIELDS) -->
                        <!-- ============================================== -->
                        <div x-show="formData.jenis_lembaga === 'BUMDes'" x-collapse class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-4">
                            <div class="flex items-center gap-2 text-emerald-800 font-black text-xs uppercase tracking-wider border-b border-emerald-200/80 pb-2">
                                <i class="fas fa-store text-emerald-600"></i>
                                <span>Informasi Spesifik BUMDes (Legalitas & Usaha)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Legalitas AHU Kemenkumham
                                    </label>
                                    <input type="text" name="nomor_badan_hukum_kemenkumham" x-model="formData.nomor_badan_hukum_kemenkumham" placeholder="AHU-00000.AH.01.33..." 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Tahun Pendirian
                                    </label>
                                    <input type="number" name="tahun_pendirian" x-model="formData.tahun_pendirian" placeholder="2023" min="1900" max="2100" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Kategori / Status
                                    </label>
                                    <select name="kategori_status" x-model="formData.kategori_status" 
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                                        <option value="Perintis">Perintis</option>
                                        <option value="Berkembang">Berkembang</option>
                                        <option value="Maju">Maju</option>
                                        <option value="Mandiri">Mandiri</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NPWP & Keuangan -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        NPWP BUMDes
                                    </label>
                                    <input type="text" name="npwp_bumdes" x-model="formData.npwp_bumdes" placeholder="00.000.000.0-000.000" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Total Aset (Rp)
                                    </label>
                                    <input type="number" name="total_aset" x-model="formData.total_aset" min="0" step="1000" placeholder="0" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Omzet Terakhir (Rp)
                                    </label>
                                    <input type="number" name="omzet_terakhir" x-model="formData.omzet_terakhir" min="0" step="1000" placeholder="0" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>
                            </div>

                            <!-- Daftar Unit Usaha -->
                            <div>
                                <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                    Daftar Unit Usaha Berjalan
                                </label>
                                <textarea name="daftar_unit_usaha" x-model="formData.daftar_unit_usaha" rows="2" placeholder="Contoh: Pengelolaan Sampah, Simpan Pinjam, Wisata Kuliner, Penjualan Saprotan" 
                                          class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                                <p class="text-[10px] text-emerald-700 mt-0.5">Pisahkan dengan tanda koma untuk setiap unit usaha</p>
                            </div>

                            <!-- Susunan Pengurus BUMDes -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Penasihat (Kades / Lurah)
                                    </label>
                                    <input type="text" name="nama_penasihat" x-model="formData.nama_penasihat" placeholder="Nama Penasihat" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Pelaksana Operasional / Direktur
                                    </label>
                                    <input type="text" name="nama_pelaksana_operasional" x-model="formData.nama_pelaksana_operasional" placeholder="Nama Direktur" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Pengawas BUMDes
                                    </label>
                                    <input type="text" name="nama_pengawas" x-model="formData.nama_pengawas" placeholder="Nama Pengawas" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>

                        <!-- Data Legalitas Umum -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nomor SK Pendirian
                                </label>
                                <input type="text" name="nomor_sk_pendirian" x-model="formData.nomor_sk_pendirian" placeholder="140/01/SK/..." 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal SK
                                </label>
                                <input type="date" name="tanggal_sk" x-model="formData.tanggal_sk" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Dasar Hukum / Payung Hukum
                            </label>
                            <input type="text" name="dasar_hukum" x-model="formData.dasar_hukum" placeholder="Contoh: Permendagri No. 18 Tahun 2018" 
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>

                        <!-- Nama Ketua & Kontak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Ketua / Koordinator
                                </label>
                                <input type="text" name="nama_ketua" x-model="formData.nama_ketua" placeholder="Nama lengkap ketua" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kontak Telepon / WhatsApp
                                </label>
                                <input type="text" name="kontak" x-model="formData.kontak" placeholder="08xxxxxxxxxx" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <!-- Alamat Kantor -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Kantor / Sekretariat
                            </label>
                            <textarea name="alamat_kantor" x-model="formData.alamat_kantor" rows="2" placeholder="Alamat lengkap sekretariat..." 
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></textarea>
                        </div>

                        <!-- Deskripsi Profil -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Deskripsi Profil & Tugas Pokok
                            </label>
                            <textarea name="deskripsi_profil" x-model="formData.deskripsi_profil" rows="3" placeholder="Penjelasan peran, fungsi, dan kegiatan lembaga..." 
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></textarea>
                        </div>

                        <!-- Status Aktif -->
                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="status_aktif" value="1" x-model="formData.status_aktif" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="font-bold text-slate-700 text-xs">Aktifkan data kelembagaan desa ini</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-xs transition transform hover:-translate-y-0.5">
                            Simpan Lembaga
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="editModalOpen" @click="editModalOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <div x-show="editModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-2xl w-full border border-slate-200 my-8">
                <form :action="'{{ url('admin/kelembagaan') }}/' + formData.id" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 px-6 py-4 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white/10 text-indigo-300 flex items-center justify-center text-sm">
                                <i class="fas fa-pen-to-square"></i>
                            </div>
                            <h3 class="text-base font-bold">Edit Data Kelembagaan Desa</h3>
                        </div>
                        <button type="button" @click="editModalOpen = false" class="text-slate-300 hover:text-white transition">
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        <!-- Jenis Lembaga -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jenis Kelembagaan <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_lembaga" x-model="formData.jenis_lembaga" required 
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold transition">
                                <option value="LKD">Lembaga Kemasyarakatan Desa (LKD) - RT/RW, PKK, Posyandu, Karang Taruna, LPMD</option>
                                <option value="BUMDes">Badan Usaha Milik Desa (BUMDes) / Lembaga Usaha Ekonomi</option>
                                <option value="Lembaga Pemerintahan">Lembaga Pemerintahan Desa / Kelurahan</option>
                                <option value="Lembaga Adat">Lembaga Adat / Komunitas Budaya</option>
                            </select>
                        </div>

                        <!-- Grid: Nama & Singkatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Lembaga <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama_lembaga" x-model="formData.nama_lembaga" required 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-semibold transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Singkatan / Alias
                                </label>
                                <input type="text" name="singkatan" x-model="formData.singkatan" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <!-- CONDITIONAL BUMDES DETAIL -->
                        <div x-show="formData.jenis_lembaga === 'BUMDes'" x-collapse class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-4">
                            <div class="flex items-center gap-2 text-emerald-800 font-black text-xs uppercase tracking-wider border-b border-emerald-200/80 pb-2">
                                <i class="fas fa-store text-emerald-600"></i>
                                <span>Informasi Spesifik BUMDes (Legalitas & Usaha)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Legalitas AHU Kemenkumham
                                    </label>
                                    <input type="text" name="nomor_badan_hukum_kemenkumham" x-model="formData.nomor_badan_hukum_kemenkumham" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Tahun Pendirian
                                    </label>
                                    <input type="number" name="tahun_pendirian" x-model="formData.tahun_pendirian" min="1900" max="2100" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Kategori / Status
                                    </label>
                                    <select name="kategori_status" x-model="formData.kategori_status" 
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                                        <option value="Perintis">Perintis</option>
                                        <option value="Berkembang">Berkembang</option>
                                        <option value="Maju">Maju</option>
                                        <option value="Mandiri">Mandiri</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NPWP & Keuangan -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        NPWP BUMDes
                                    </label>
                                    <input type="text" name="npwp_bumdes" x-model="formData.npwp_bumdes" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Total Aset (Rp)
                                    </label>
                                    <input type="number" name="total_aset" x-model="formData.total_aset" min="0" step="1000" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Omzet Terakhir (Rp)
                                    </label>
                                    <input type="number" name="omzet_terakhir" x-model="formData.omzet_terakhir" min="0" step="1000" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>
                            </div>

                            <!-- Unit Usaha -->
                            <div>
                                <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                    Daftar Unit Usaha Berjalan
                                </label>
                                <textarea name="daftar_unit_usaha" x-model="formData.daftar_unit_usaha" rows="2" 
                                          class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                            </div>

                            <!-- Susunan Pengurus -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Penasihat (Kades / Lurah)
                                    </label>
                                    <input type="text" name="nama_penasihat" x-model="formData.nama_penasihat" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Pelaksana Operasional / Direktur
                                    </label>
                                    <input type="text" name="nama_pelaksana_operasional" x-model="formData.nama_pelaksana_operasional" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-emerald-950 uppercase tracking-wider mb-1">
                                        Pengawas BUMDes
                                    </label>
                                    <input type="text" name="nama_pengawas" x-model="formData.nama_pengawas" 
                                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>

                        <!-- Data Legalitas Umum -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nomor SK Pendirian
                                </label>
                                <input type="text" name="nomor_sk_pendirian" x-model="formData.nomor_sk_pendirian" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal SK
                                </label>
                                <input type="date" name="tanggal_sk" x-model="formData.tanggal_sk" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Dasar Hukum / Payung Hukum
                            </label>
                            <input type="text" name="dasar_hukum" x-model="formData.dasar_hukum" 
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>

                        <!-- Nama Ketua & Kontak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Ketua / Koordinator
                                </label>
                                <input type="text" name="nama_ketua" x-model="formData.nama_ketua" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kontak Telepon / WhatsApp
                                </label>
                                <input type="text" name="kontak" x-model="formData.kontak" 
                                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <!-- Alamat Kantor -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Kantor / Sekretariat
                            </label>
                            <textarea name="alamat_kantor" x-model="formData.alamat_kantor" rows="2" 
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></textarea>
                        </div>

                        <!-- Deskripsi Profil -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Deskripsi Profil & Tugas Pokok
                            </label>
                            <textarea name="deskripsi_profil" x-model="formData.deskripsi_profil" rows="3" 
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></textarea>
                        </div>

                        <!-- Status Aktif -->
                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="status_aktif" value="1" x-model="formData.status_aktif" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="font-bold text-slate-700 text-xs">Aktifkan data kelembagaan desa ini</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-xs transition transform hover:-translate-y-0.5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
