@extends('layouts.admin')

@section('title', 'Transparansi & APBD')
@section('header-title', 'Transparansi APBD & Dana Desa')
@section('header-subtitle', 'Mengatur data realisasi APBD/Dana Desa.')

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: false,
    editModalOpen: false,
    selectedAlloc: null,
    selectedIndex: null
}">

    <!-- Alert Status -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-times-circle text-rose-600 mt-0.5"></i>
            <div>
                <span class="font-bold">Gagal memproses data:</span>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Utama: Info Anggaran -->
    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="apbd">
        <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold text-slate-900">Total Anggaran & Realisasi (APBD)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Atur info ringkasan anggaran untuk tahun berjalan.</p>
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan Ringkasan</button>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Tahun Anggaran *</label>
                <input type="number" name="apbd_year" value="{{ old('apbd_year', $profile['apbd']['year'] ?? date('Y')) }}" required class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Total Pagu (Rp) *</label>
                <input type="text" name="apbd_total" value="{{ old('apbd_total', $profile['apbd']['total_budget'] ?? '') }}" required placeholder="Contoh: 1.500.000.000" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Realisasi (Rp) *</label>
                <input type="text" name="apbd_realized" value="{{ old('apbd_realized', $profile['apbd']['realized_budget'] ?? '') }}" required placeholder="Contoh: 985.400.000" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Persentase (%) *</label>
                <input type="text" name="apbd_realized_pct" value="{{ old('apbd_realized_pct', $profile['apbd']['realized_pct'] ?? '') }}" required placeholder="Contoh: 67.9" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
            </div>
        </div>
    </form>

    <!-- Data Table Container: Detail Alokasi -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Header Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-emerald-700 shrink-0"></i>
                    <span>Detail Alokasi per Bidang</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar rincian anggaran untuk masing-masing bidang pembangunan.</p>
            </div>

            <button type="button" @click="createModalOpen = true" 
               class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 shrink-0">
                <i class="fas fa-plus"></i>
                <span>Tambah Bidang Alokasi</span>
            </button>
        </div>

        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 sm:px-5">Nama Bidang</th>
                        <th class="py-3 px-4 sm:px-5">Anggaran (Rp)</th>
                        <th class="py-3 px-4 sm:px-5">Persentase (%)</th>
                        <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($profile['apbd']['allocations'] ?? [] as $index => $alloc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 sm:px-5 font-bold text-slate-900">
                                {{ $alloc['name'] }}
                                @if(!empty($alloc['desc']))
                                    <p class="text-[11px] font-normal text-slate-500 mt-1 line-clamp-1">{{ $alloc['desc'] }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">Rp {{ $alloc['amount'] }}</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">
                                <span class="px-2 py-1 bg-sky-50 text-sky-700 rounded-lg font-bold border border-sky-100">{{ $alloc['pct'] }}%</span>
                            </td>
                            <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button type="button" @click="selectedAlloc = {{ json_encode($alloc) }}; selectedIndex = {{ $index }}; editModalOpen = true;" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>

                                <form action="{{ route('admin.beranda.apbd.destroy', $index) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bidang alokasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400">
                                <p class="font-semibold text-slate-600">Belum ada rincian alokasi bidang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- MODAL 1: TAMBAH ALOKASI -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <form action="{{ route('admin.beranda.apbd.store') }}" method="POST">@csrf
                    
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                            <h3 class="text-base font-bold text-slate-800">Tambah Bidang Alokasi</h3>
                        </div>
                        <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs text-slate-700">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Nama Bidang <span class="text-rose-500">*</span></label>
                            <input type="text" name="apbd_alloc_name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Anggaran (Rp) <span class="text-rose-500">*</span></label>
                                <input type="text" name="apbd_alloc_amount" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Persentase (%) <span class="text-rose-500">*</span></label>
                                <input type="text" name="apbd_alloc_pct" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Deskripsi Singkat</label>
                            <textarea name="apbd_alloc_desc" rows="3" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium"></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition">Simpan Alokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDIT ALOKASI -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <template x-if="selectedAlloc !== null">
                    <form :action="'{{ url('admin/kelola-beranda/apbd') }}/' + selectedIndex" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                                <h3 class="text-base font-bold text-slate-800">Edit Bidang Alokasi</h3>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs text-slate-700">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Nama Bidang <span class="text-rose-500">*</span></label>
                                <input type="text" name="apbd_alloc_name" x-model="selectedAlloc.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Anggaran (Rp) <span class="text-rose-500">*</span></label>
                                    <input type="text" name="apbd_alloc_amount" x-model="selectedAlloc.amount" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Persentase (%) <span class="text-rose-500">*</span></label>
                                    <input type="text" name="apbd_alloc_pct" x-model="selectedAlloc.pct" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                </div>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Deskripsi Singkat</label>
                                <textarea name="apbd_alloc_desc" x-model="selectedAlloc.desc" rows="3" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium"></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
