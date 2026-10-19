@extends('layouts.admin')

@section('title', 'Transparansi & APBD')
@section('header-title', 'Transparansi APBD & Dana Desa')
@section('header-subtitle', 'Mengatur data riwayat realisasi APBD/Dana Desa dari tahun ke tahun sesuai standar laporan.')

@section('content')
<div class="space-y-6" x-data="{
    // Modal states
    createItemModalOpen: false,
    editItemModalOpen: false,
    addYearModalOpen: false,
    editYearModalOpen: false,
    deleteYearModalOpen: false,

    // Data buffers
    activeJenis: 'pendapatan', // 'pendapatan', 'belanja', 'pembiayaan'
    activeYear: '{{ $selectedYear ?? date('Y') }}',
    selectedItem: {
        id: null,
        tahun: '{{ $selectedYear ?? date('Y') }}',
        jenis: 'pendapatan',
        kategori: '',
        nama_bidang: '',
        anggaran: '',
        realisasi: '',
    },
    yearToEdit: null,
    yearToDelete: null,

    openCreateModal(jenis) {
        this.activeJenis = jenis;
        let defaultKategori = 'PENDAPATAN TRANSFER';
        if (jenis === 'belanja') defaultKategori = 'PELAKSANAAN PEMBANGUNAN DESA';
        if (jenis === 'pembiayaan') defaultKategori = 'PENERIMAAN PEMBIAYAAN';

        this.selectedItem = {
            id: null,
            tahun: this.activeYear,
            jenis: jenis,
            kategori: defaultKategori,
            nama_bidang: '',
            anggaran: '',
            realisasi: '',
        };
        this.createItemModalOpen = true;
    },

    openEditModal(item) {
        this.selectedItem = {
            id: item.id,
            tahun: item.tahun,
            jenis: item.jenis || 'belanja',
            kategori: item.kategori || '',
            nama_bidang: item.nama_bidang || '',
            anggaran: item.anggaran ? new Intl.NumberFormat('id-ID').format(item.anggaran) : '',
            realisasi: item.realisasi ? new Intl.NumberFormat('id-ID').format(item.realisasi) : '',
        };
        this.editItemModalOpen = true;
    },

    openEditYear(year) {
        this.yearToEdit = year;
        this.editYearModalOpen = true;
    },

    openDeleteYear(year) {
        this.yearToDelete = year;
        this.deleteYearModalOpen = true;
    }
}">

    <!-- Alert Status & Feedback -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-xs">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('status'))
        <div class="bg-slate-50 border border-slate-200 text-slate-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-xs">
            <i class="fas fa-check-circle text-slate-600 text-sm"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
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

    {{-- ========================================================================= --}}
    {{-- TAMPILAN 1: DAFTAR RIWAYAT TAHUN ANGGARAN (JIKA BELUM MEMILIH TAHUN)       --}}
    {{-- ========================================================================= --}}
    @if(empty($selectedYear))
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            
            <!-- Header Kartu Tahun -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                        Riwayat Tahun Anggaran (APBDes)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">
                        Pilih tahun anggaran yang ingin dikelola atau tambahkan tahun baru.
                    </p>
                </div>
                <button type="button" @click="addYearModalOpen = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl shadow-sm transition active:scale-95 shrink-0">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Tahun APBD</span>
                </button>
            </div>

            <!-- Grid Kartu Tahun Anggaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @forelse($availableYears as $year)
                    <div class="bg-white rounded-2xl border border-slate-200 hover:border-emerald-400/80 p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                        
                        <!-- Top Icon & Actions -->
                        <div class="flex items-start justify-between">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <i class="fas fa-calendar-alt"></i>
                            </div>

                            <!-- Edit & Delete Buttons -->
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="openEditYear({{ $year }})" title="Edit Tahun"
                                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
                                    <i class="fas fa-pen text-[10px]"></i>
                                </button>
                                <button type="button" @click="openDeleteYear({{ $year }})" title="Hapus Tahun"
                                        class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center text-xs transition">
                                    <i class="fas fa-trash text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Year Number Display -->
                        <div class="my-5">
                            <span class="text-3xl font-black text-slate-900 tracking-tight block">
                                {{ $year }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-semibold block mt-0.5">
                                Tahun Anggaran {{ $year }}
                            </span>
                        </div>

                        <!-- Button Kelola Data Anggaran -->
                        <a href="{{ route('admin.beranda.transparansi', ['tahun' => $year]) }}"
                           class="w-full py-2.5 px-4 rounded-xl border border-slate-200 hover:border-emerald-600 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-bold text-xs flex items-center justify-center gap-2 transition active:scale-95 shadow-xs">
                            <i class="far fa-edit text-xs"></i>
                            <span>Kelola Data Anggaran</span>
                        </a>

                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400">
                        <i class="fas fa-calendar-xmark text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-semibold">Belum ada riwayat tahun anggaran yang terdaftar.</p>
                    </div>
                @endforelse
            </div>

        </div>

    {{-- ========================================================================= --}}
    {{-- TAMPILAN 2: RINCIAN KELOLA POS ANGGARAN (TAHUN AKTIF TERPILIH)             --}}
    {{-- ========================================================================= --}}
    @else
        <!-- Top Action Bar -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('admin.beranda.transparansi') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl shadow-xs transition active:scale-95">
                <i class="fas fa-arrow-left text-xs text-slate-500"></i>
                <span>Kembali ke Daftar Tahun</span>
            </a>

            <div class="flex items-center gap-2 text-right">
                <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                    Tahun Anggaran {{ $selectedYear }}
                </h3>
            </div>
        </div>

        <!-- 3 KARTU TABEL DENGAN WARNA BERBEDA -->
        <div class="space-y-6">

            <!-- ================================================================= -->
            <!-- ================================================================= -->
            <!-- KARTU 1: PENDAPATAN DESA (TEMA WARNA: EMERALD GREEN)             -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
                <!-- Header Kartu -->
                <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>1. Pendapatan Desa</span>
                    </h2>
                    <div class="flex items-center gap-2.5">
                        <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/60">
                            Penerimaan Kas
                        </span>
                        <button type="button" @click="openCreateModal('pendapatan')"
                                class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl flex items-center gap-1.5 transition shadow-xs active:scale-95">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Tabel Pendapatan Desa -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-emerald-600 text-white font-bold text-xs sm:text-sm">
                                <th class="py-3 px-5 sm:px-6 text-left font-bold tracking-wide w-2/5">Item / Kegiatan</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Rencana / Anggaran</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Realisasi</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Lebih/Kurang</th>
                                <th class="py-3 px-4 text-center font-bold tracking-wide w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $pendapatanGrouped = $pendapatanList->groupBy(fn($i) => trim($i->kategori) ?: 'PENDAPATAN TRANSFER');
                                $totPendAnggaran = (float) $pendapatanList->sum('anggaran');
                                $totPendRealisasi = (float) $pendapatanList->sum('realisasi');
                                $totPendSelisih = $totPendRealisasi - $totPendAnggaran;
                            @endphp

                            @forelse($pendapatanGrouped as $catName => $items)
                                <!-- Subkategori Header -->
                                <tr class="bg-slate-50/70 border-y border-slate-100">
                                    <td colspan="5" class="pt-3 pb-2 px-5 sm:px-6 font-bold text-xs text-emerald-700 uppercase tracking-wider">
                                        {{ $catName }}
                                    </td>
                                </tr>
                                <!-- Baris Data Item -->
                                @foreach($items as $item)
                                    @php
                                        $itemAnggaran = (float) $item->anggaran;
                                        $itemRealisasi = (float) $item->realisasi;
                                        $itemSelisih = $itemRealisasi - $itemAnggaran;
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-5 sm:px-6 font-medium text-slate-700 pl-7 sm:pl-9">
                                            {{ $item->nama_bidang }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format($itemAnggaran, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format($itemRealisasi, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format(abs($itemSelisih), 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" @click="openEditModal({{ $item }})"
                                                        class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.beranda.apbd.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-2.5 py-1 text-[11px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs font-semibold">
                                        Belum ada data Pendapatan Desa untuk tahun {{ $selectedYear }}. Klik <strong>+ Tambah</strong> untuk menambahkan.
                                    </td>
                                </tr>
                            @endforelse

                            @if($pendapatanList->isNotEmpty())
                                <!-- Baris Total Pendapatan -->
                                <tr class="bg-emerald-50/80 font-bold border-t-2 border-emerald-300 text-slate-900">
                                    <td class="py-3 px-5 sm:px-6 font-black text-xs sm:text-sm text-emerald-950 uppercase">
                                        TOTAL PENDAPATAN
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-emerald-950 whitespace-nowrap">
                                        Rp. {{ number_format($totPendAnggaran, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-emerald-950 whitespace-nowrap">
                                        Rp. {{ number_format($totPendRealisasi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-emerald-950 whitespace-nowrap">
                                        Rp. {{ number_format(abs($totPendSelisih), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- KARTU 2: BELANJA DESA (TEMA WARNA: WARM AMBER / ORANGE)           -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
                <!-- Header Kartu -->
                <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>2. Belanja Desa</span>
                    </h2>
                    <div class="flex items-center gap-2.5">
                        <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/60">
                            Pengeluaran Kas
                        </span>
                        <button type="button" @click="openCreateModal('belanja')"
                                class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl flex items-center gap-1.5 transition shadow-xs active:scale-95">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Tabel Belanja Desa -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-amber-600 text-white font-bold text-xs sm:text-sm">
                                <th class="py-3 px-5 sm:px-6 text-left font-bold tracking-wide w-2/5">Item / Kegiatan</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Rencana / Anggaran</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Realisasi</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Lebih/Kurang</th>
                                <th class="py-3 px-4 text-center font-bold tracking-wide w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $belanjaGrouped = $belanjaList->groupBy(fn($i) => trim($i->kategori) ?: 'PELAKSANAAN PEMBANGUNAN DESA');
                                $totBelAnggaran = (float) $belanjaList->sum('anggaran');
                                $totBelRealisasi = (float) $belanjaList->sum('realisasi');
                                $totBelSelisih = $totBelRealisasi - $totBelAnggaran;
                            @endphp

                            @forelse($belanjaGrouped as $catName => $items)
                                <!-- Subkategori Header -->
                                <tr class="bg-slate-50/70 border-y border-slate-100">
                                    <td colspan="5" class="pt-3 pb-2 px-5 sm:px-6 font-bold text-xs text-amber-800 uppercase tracking-wider">
                                        {{ $catName }}
                                    </td>
                                </tr>
                                <!-- Baris Data Item -->
                                @foreach($items as $item)
                                    @php
                                        $itemAnggaran = (float) $item->anggaran;
                                        $itemRealisasi = (float) $item->realisasi;
                                        $itemSelisih = $itemRealisasi - $itemAnggaran;
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-5 sm:px-6 font-medium text-slate-700 pl-7 sm:pl-9">
                                            {{ $item->nama_bidang }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format($itemAnggaran, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format($itemRealisasi, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format(abs($itemSelisih), 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" @click="openEditModal({{ $item }})"
                                                        class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.beranda.apbd.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-2.5 py-1 text-[11px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs font-semibold">
                                        Belum ada data Belanja Desa untuk tahun {{ $selectedYear }}. Klik <strong>+ Tambah</strong> untuk menambahkan.
                                    </td>
                                </tr>
                            @endforelse

                            @if($belanjaList->isNotEmpty())
                                <!-- Baris Total Belanja -->
                                <tr class="bg-amber-50/80 font-bold border-t-2 border-amber-300 text-slate-900">
                                    <td class="py-3 px-5 sm:px-6 font-black text-xs sm:text-sm text-amber-950 uppercase">
                                        TOTAL BELANJA
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-amber-950 whitespace-nowrap">
                                        Rp. {{ number_format($totBelAnggaran, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-amber-950 whitespace-nowrap">
                                        Rp. {{ number_format($totBelRealisasi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-amber-950 whitespace-nowrap">
                                        Rp. {{ number_format(abs($totBelSelisih), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- KARTU 3: PEMBIAYAAN DESA (TEMA WARNA: ROYAL BLUE / INDIGO)        -->
            <!-- ================================================================= -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
                <!-- Header Kartu -->
                <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>3. Pembiayaan Desa</span>
                    </h2>
                    <div class="flex items-center gap-2.5">
                        <span class="text-[11px] font-bold text-blue-800 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200/60">
                            Pembiayaan Kas
                        </span>
                        <button type="button" @click="openCreateModal('pembiayaan')"
                                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl flex items-center gap-1.5 transition shadow-xs active:scale-95">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Tabel Pembiayaan Desa -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-blue-600 text-white font-bold text-xs sm:text-sm">
                                <th class="py-3 px-5 sm:px-6 text-left font-bold tracking-wide w-2/5">Item / Kegiatan</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Rencana / Anggaran</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Realisasi</th>
                                <th class="py-3 px-4 sm:px-6 text-right font-bold tracking-wide">Lebih/Kurang</th>
                                <th class="py-3 px-4 text-center font-bold tracking-wide w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $pembiayaanGrouped = $pembiayaanList->groupBy(fn($i) => trim($i->kategori) ?: 'PENERIMAAN PEMBIAYAAN');
                                $totPembAnggaran = (float) $pembiayaanList->sum('anggaran');
                                $totPembRealisasi = (float) $pembiayaanList->sum('realisasi');
                                $totPembSelisih = $totPembRealisasi - $totPembAnggaran;
                            @endphp

                            @forelse($pembiayaanGrouped as $catName => $items)
                                <!-- Subkategori Header -->
                                <tr class="bg-slate-50/70 border-y border-slate-100">
                                    <td colspan="5" class="pt-3 pb-2 px-5 sm:px-6 font-bold text-xs text-blue-800 uppercase tracking-wider">
                                        {{ $catName }}
                                    </td>
                                </tr>
                                <!-- Baris Data Item -->
                                @foreach($items as $item)
                                    @php
                                        $itemAnggaran = (float) $item->anggaran;
                                        $itemRealisasi = (float) $item->realisasi;
                                        $itemSelisih = $itemRealisasi - $itemAnggaran;
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-5 sm:px-6 font-medium text-slate-700 pl-7 sm:pl-9">
                                            {{ $item->nama_bidang }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format($itemAnggaran, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format($itemRealisasi, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 sm:px-6 text-right font-medium text-slate-700 whitespace-nowrap">
                                            Rp. {{ number_format(abs($itemSelisih), 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" @click="openEditModal({{ $item }})"
                                                        class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.beranda.apbd.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pos pembiayaan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-2.5 py-1 text-[11px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs font-semibold">
                                        Belum ada data Pembiayaan Desa untuk tahun {{ $selectedYear }}. Klik <strong>+ Tambah</strong> untuk menambahkan.
                                    </td>
                                </tr>
                            @endforelse

                            @if($pembiayaanList->isNotEmpty())
                                <!-- Baris Total Pembiayaan -->
                                <tr class="bg-blue-50/80 font-bold border-t-2 border-blue-300 text-slate-900">
                                    <td class="py-3 px-5 sm:px-6 font-black text-xs sm:text-sm text-blue-950 uppercase">
                                        TOTAL PEMBIAYAAN
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-blue-950 whitespace-nowrap">
                                        Rp. {{ number_format($totPembAnggaran, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-blue-950 whitespace-nowrap">
                                        Rp. {{ number_format($totPembRealisasi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 sm:px-6 text-right font-black text-blue-950 whitespace-nowrap">
                                        Rp. {{ number_format(abs($totPembSelisih), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- MODAL: TAMBAH TAHUN ANGGARAN BARU                                         --}}
    {{-- ========================================================================= --}}
    <div x-show="addYearModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="addYearModalOpen = false"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.stop>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-calendar-plus text-emerald-600"></i>
                        <span>Tambah Tahun APBD</span>
                    </h3>
                    <button type="button" @click="addYearModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.beranda.apbd.tahun.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tahun Anggaran *</label>
                        <input type="number" min="2000" max="2099" name="tahun" value="{{ date('Y') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                        <span class="text-[10px] text-slate-400 mt-1 block">Contoh: 2026, 2027</span>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="addYearModalOpen = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl transition shadow-xs">
                            Simpan Tahun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: EDIT TAHUN ANGGARAN                                                --}}
    {{-- ========================================================================= --}}
    <div x-show="editYearModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="editYearModalOpen = false"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.stop>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-pen text-slate-600"></i>
                        <span>Ubah Angka Tahun</span>
                    </h3>
                    <button type="button" @click="editYearModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="'{{ url('admin/beranda/apbd/tahun') }}/' + yearToEdit" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tahun Baru *</label>
                        <input type="number" min="2000" max="2099" name="tahun" x-model="yearToEdit" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="editYearModalOpen = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl transition shadow-xs">
                            Perbarui Tahun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: HAPUS TAHUN ANGGARAN                                               --}}
    {{-- ========================================================================= --}}
    <div x-show="deleteYearModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="deleteYearModalOpen = false"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.stop>
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Hapus Tahun Anggaran?</h3>
                        <p class="text-xs text-slate-500">Tahun <strong x-text="yearToDelete"></strong></p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Tindakan ini akan menghapus seluruh data pendapatan, belanja, dan pembiayaan untuk tahun ini secara permanen.
                </p>

                <form :action="'{{ url('admin/beranda/apbd/tahun') }}/' + yearToDelete" method="POST" class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteYearModalOpen = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: TAMBAH ITEM ANGGARAN (PENDAPATAN, BELANJA, PEMBIAYAAN)             --}}
    {{-- ========================================================================= --}}
    <div x-show="createItemModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="createItemModalOpen = false"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.stop>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" 
                              :class="{
                                  'bg-emerald-500': activeJenis === 'pendapatan',
                                  'bg-rose-500': activeJenis === 'belanja',
                                  'bg-blue-500': activeJenis === 'pembiayaan'
                              }"></span>
                        <span x-text="activeJenis === 'pendapatan' ? 'Tambah Pendapatan Desa' : (activeJenis === 'belanja' ? 'Tambah Belanja Desa' : 'Tambah Pembiayaan Desa')"></span>
                    </h3>
                    <button type="button" @click="createItemModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.beranda.apbd.store') }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="apbd_year" x-model="selectedItem.tahun">
                    <input type="hidden" name="apbd_jenis" x-model="selectedItem.jenis">

                    <!-- Pilihan Kategori / Subkategori Sesuai Jenis -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kategori / Bidang *</label>
                        <template x-if="activeJenis === 'pendapatan'">
                            <select name="apbd_kategori" x-model="selectedItem.kategori" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                                <option value="PENDAPATAN TRANSFER">PENDAPATAN TRANSFER</option>
                                <option value="PENDAPATAN ASLI DESA">PENDAPATAN ASLI DESA</option>
                                <option value="PENDAPATAN LAIN-LAIN">PENDAPATAN LAIN-LAIN</option>
                            </select>
                        </template>

                        <template x-if="activeJenis === 'belanja'">
                            <select name="apbd_kategori" x-model="selectedItem.kategori" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 font-bold text-slate-800">
                                <option value="PELAKSANAAN PEMBANGUNAN DESA">PELAKSANAAN PEMBANGUNAN DESA</option>
                                <option value="PENYELENGGARAAN PEMERINTAHAN DESA">PENYELENGGARAAN PEMERINTAHAN DESA</option>
                                <option value="PEMBINAAN KEMASYARAKATAN">PEMBINAAN KEMASYARAKATAN</option>
                                <option value="PEMBERDAYAAN MASYARAKAT">PEMBERDAYAAN MASYARAKAT</option>
                                <option value="PENANGGULANGAN BENCANA">PENANGGULANGAN BENCANA</option>
                            </select>
                        </template>

                        <template x-if="activeJenis === 'pembiayaan'">
                            <select name="apbd_kategori" x-model="selectedItem.kategori" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 font-bold text-slate-800">
                                <option value="PENERIMAAN PEMBIAYAAN">PENERIMAAN PEMBIAYAAN</option>
                                <option value="PENGELUARAN PEMBIAYAAN">PENGELUARAN PEMBIAYAAN</option>
                            </select>
                        </template>
                    </div>

                    <!-- Nama Item / Kegiatan -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" 
                               x-text="activeJenis === 'belanja' ? 'Nama Kegiatan *' : 'Nama Item *'"></label>
                        <input type="text" name="apbd_alloc_name" x-model="selectedItem.nama_bidang" required
                               placeholder="Contoh: DANA DESA / pembangunan jalan"
                               class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                    </div>

                    <!-- Anggaran (Rp) & Realisasi (Rp) -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Anggaran (Rp) *</label>
                            <input type="text" name="apbd_alloc_amount" x-model="selectedItem.anggaran" required
                                   placeholder="Contoh: 80.000.000"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Realisasi (Rp) *</label>
                            <input type="text" name="apbd_alloc_realized" x-model="selectedItem.realisasi" required
                                   placeholder="Contoh: 80.000.000"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="createItemModalOpen = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-white font-bold rounded-xl transition shadow-xs"
                                :class="{
                                    'bg-emerald-700 hover:bg-emerald-800': activeJenis === 'pendapatan',
                                    'bg-rose-600 hover:bg-rose-700': activeJenis === 'belanja',
                                    'bg-blue-600 hover:bg-blue-700': activeJenis === 'pembiayaan'
                                }">
                            Simpan Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: EDIT ITEM ANGGARAN                                                 --}}
    {{-- ========================================================================= --}}
    <div x-show="editItemModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="editItemModalOpen = false"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.stop>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-edit text-slate-600"></i>
                        <span>Edit Item Anggaran</span>
                    </h3>
                    <button type="button" @click="editItemModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="'{{ url('admin/beranda/apbd') }}/' + selectedItem.id" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="apbd_year" x-model="selectedItem.tahun">
                    <input type="hidden" name="apbd_jenis" x-model="selectedItem.jenis">

                    <!-- Kategori -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kategori / Bidang *</label>
                        <template x-if="selectedItem.jenis === 'pendapatan'">
                            <select name="apbd_kategori" x-model="selectedItem.kategori" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                                <option value="PENDAPATAN TRANSFER">PENDAPATAN TRANSFER</option>
                                <option value="PENDAPATAN ASLI DESA">PENDAPATAN ASLI DESA</option>
                                <option value="PENDAPATAN LAIN-LAIN">PENDAPATAN LAIN-LAIN</option>
                            </select>
                        </template>

                        <template x-if="selectedItem.jenis === 'belanja'">
                            <select name="apbd_kategori" x-model="selectedItem.kategori" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 font-bold text-slate-800">
                                <option value="PELAKSANAAN PEMBANGUNAN DESA">PELAKSANAAN PEMBANGUNAN DESA</option>
                                <option value="PENYELENGGARAAN PEMERINTAHAN DESA">PENYELENGGARAAN PEMERINTAHAN DESA</option>
                                <option value="PEMBINAAN KEMASYARAKATAN">PEMBINAAN KEMASYARAKATAN</option>
                                <option value="PEMBERDAYAAN MASYARAKAT">PEMBERDAYAAN MASYARAKAT</option>
                                <option value="PENANGGULANGAN BENCANA">PENANGGULANGAN BENCANA</option>
                            </select>
                        </template>

                        <template x-if="selectedItem.jenis === 'pembiayaan'">
                            <select name="apbd_kategori" x-model="selectedItem.kategori" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 font-bold text-slate-800">
                                <option value="PENERIMAAN PEMBIAYAAN">PENERIMAAN PEMBIAYAAN</option>
                                <option value="PENGELUARAN PEMBIAYAAN">PENGELUARAN PEMBIAYAAN</option>
                            </select>
                        </template>
                    </div>

                    <!-- Nama Item / Kegiatan -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Item / Kegiatan *</label>
                        <input type="text" name="apbd_alloc_name" x-model="selectedItem.nama_bidang" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                    </div>

                    <!-- Anggaran (Rp) & Realisasi (Rp) -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Anggaran (Rp) *</label>
                            <input type="text" name="apbd_alloc_amount" x-model="selectedItem.anggaran" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Realisasi (Rp) *</label>
                            <input type="text" name="apbd_alloc_realized" x-model="selectedItem.realisasi" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium text-slate-800">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="editItemModalOpen = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition shadow-xs">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
