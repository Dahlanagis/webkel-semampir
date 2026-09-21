@extends('layouts.admin')

@section('title', 'Master Layanan & Jenis Surat')
@section('header-title', 'Master Layanan Publik & Jenis Surat')
@section('header-subtitle', 'Kelola etalase layanan beranda, kode surat, dan berkas persyaratan pengajuan warga dalam 1 menu terintegrasi')

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: false,
    editModalOpen: false,
    selectedType: null,
    createDocsArray: [''],
    editDocsArray: ['']
}">

    <!-- Alert Status -->

    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <span class="font-bold">Gagal menyimpan data:</span>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Layanan</span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $totalServices }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-700 font-bold">📜</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Status Aktif</span>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-900 mt-1">{{ $activeServices }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-700 font-bold">✅</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-amber-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Tampil di Beranda</span>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-900 mt-1">{{ $homepageServices }}</h3>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-700 font-bold">🌐</div>
        </div>
    </div>

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.jenis-layanan.index') }}" class="flex items-center gap-2.5">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode, atau deskripsi..." class="pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-600 shadow-sm w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow transition">Cari</button>
        </form>

        <button @click="createModalOpen = true" 
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Layanan Baru</span>
        </button>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Ikon & Nama Layanan</th>
                        <th class="py-3.5 px-5">Kode Surat</th>
                        <th class="py-3.5 px-5">Ketentuan (Waktu & Biaya)</th>
                        <th class="py-3.5 px-5">Dokumen Syarat Wajib</th>
                        <th class="py-3.5 px-5 text-center">Tampil Beranda</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($serviceTypes as $st)
                        <tr class="hover:bg-slate-50 transition">
                            <!-- Ikon & Nama Layanan -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-lg shrink-0 shadow-sm">
                                        {{ $st->icon ?: '📜' }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm flex flex-wrap items-center gap-2">
                                            <span>{{ $st->name }}</span>
                                            @if($st->badge_label)
                                                <span class="px-2 py-0.5 text-[9px] font-extrabold bg-amber-100 text-amber-800 rounded border border-amber-200 uppercase">
                                                    {{ $st->badge_label }}
                                                </span>
                                            @endif
                                            @if($st->pdf_document)
                                                <a href="{{ asset('storage/' . $st->pdf_document) }}" target="_blank" class="px-2 py-0.5 text-[9px] font-extrabold bg-emerald-100 text-emerald-800 rounded border border-emerald-200 hover:bg-emerald-200 transition inline-flex items-center gap-1">
                                                    <span>📄 PDF SOP</span>
                                                </a>
                                            @else
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-slate-100 text-slate-500 rounded border border-slate-200">
                                                    ⚠️ Belum Ada PDF
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 max-w-xs">
                                            {{ $st->description ?? 'Tidak ada deskripsi singkat.' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kode Surat -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="font-mono font-bold text-emerald-950 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 inline-block">
                                    {{ $st->code ?? 'UMUM' }}
                                </span>
                            </td>

                            <!-- Ketentuan Pelayanan -->
                            <td class="py-3.5 px-5">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-600">
                                        <i class="fas fa-clock text-slate-400 w-3"></i> 
                                        <span>{{ $st->operational_hours ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-600">
                                        <i class="fas fa-hourglass-half text-slate-400 w-3"></i> 
                                        <span>{{ $st->estimated_time ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-600">
                                        <i class="fas fa-coins text-emerald-500 w-3"></i> 
                                        <span>{{ $st->cost ?: 'Gratis' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Dokumen Persyaratan Wajib -->
                            <td class="py-3.5 px-5">
                                @if(is_array($st->required_documents) && count($st->required_documents) > 0)
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @foreach($st->required_documents as $doc)
                                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded border border-slate-200">
                                                {{ $doc }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tidak ada syarat khusus</span>
                                @endif
                            </td>

                            <!-- Tampil di Beranda Toggle -->
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <form action="{{ route('admin.jenis-layanan.toggle-homepage', $st->id) }}" method="POST">@csrf @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition shadow-sm border {{ $st->show_on_homepage ? 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100' : 'bg-slate-100 text-slate-500 border-slate-300' }}">
                                        <span>{{ $st->show_on_homepage ? '🌐 Beranda' : '🚫 Sembunyi' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Status Aktif Toggle -->
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <form action="{{ route('admin.jenis-layanan.toggle', $st->id) }}" method="POST">@csrf @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition shadow-sm border {{ $st->is_active ? 'bg-emerald-100 text-emerald-900 border-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        <span>{{ $st->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap space-x-1">
                                <button @click="selectedType = {{ json_encode($st) }}; editDocsArray = (selectedType.required_documents && selectedType.required_documents.length > 0) ? [...selectedType.required_documents] : ['']; editModalOpen = true;" 
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>

                                <form action="{{ route('admin.jenis-layanan.destroy', $st->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400"><p class="font-semibold text-slate-600">Belum ada layanan & jenis surat terdaftar.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
            {{ $serviceTypes->links() }}
        </div>
    </div>


    <!-- MODAL 1: TAMBAH LAYANAN & JENIS SURAT BARU -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <form action="{{ route('admin.jenis-layanan.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                            <h3 class="text-base font-bold text-slate-800">Tambah Layanan & Jenis Surat Baru</h3>
                        </div>
                        <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                        
                        <!-- Nama & Kode -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-800 mb-1">Nama Layanan / Jenis Surat <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required placeholder="Surat Keterangan Usaha (SKU)" 
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Kode Surat</label>
                                <input type="text" name="code" placeholder="SKU" 
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-mono uppercase font-bold">
                            </div>
                        </div>



                        <!-- Deskripsi Layanan -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Deskripsi Singkat Layanan</label>
                            <textarea name="description" rows="2" placeholder="Penjelasan singkat fungsi dan peruntukan surat ini bagi warga..." 
                                class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium"></textarea>
                        </div>

                        <!-- Dokumen Persyaratan Wajib -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Dokumen Persyaratan Wajib</label>
                            <template x-for="(doc, index) in createDocsArray" :key="index">
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" x-model="createDocsArray[index]" name="required_documents[]" placeholder="Contoh: Foto KTP / Kartu Keluarga" 
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                    <button type="button" @click="createDocsArray.splice(index, 1)" x-show="createDocsArray.length > 1" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="createDocsArray.push('')" class="mt-1 text-xs font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Persyaratan
                            </button>
                            <p class="text-[11px] text-slate-400 mt-1">Daftar berkas lampiran yang disiapkan warga. (Kosongkan baris jika tidak jadi menambah)</p>
                        </div>

                        <!-- Info SOP & Jam Operasional -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-800 mb-1">Jam Operasional</label>
                                <input type="text" name="operational_hours" placeholder="Senin - Jumat, 08:00 - 14:00 WIB" 
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Estimasi Waktu Penyelesaian</label>
                                <input type="text" name="estimated_time" placeholder="Misal: 1 Hari Kerja" 
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Biaya Pelayanan</label>
                                <input type="text" name="cost" placeholder="Misal: Gratis" 
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>
                            <div x-data="{ selectedFileName: null }" class="sm:col-span-2">
                                <label class="block font-bold text-slate-800 mb-1">Upload File PDF Surat/Formulir (Opsional)</label>
                                <input type="file" name="pdf_document" accept="application/pdf"
                                    @change="selectedFileName = $event.target.files[0] ? $event.target.files[0].name : null"
                                    class="w-full p-2 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                <p class="text-[11px] text-slate-400 mt-1">Unggah file formulir resmi atau dokumen SOP berbentuk PDF.</p>
                                <template x-if="selectedFileName">
                                    <div class="mt-2 p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center gap-2 text-xs font-semibold text-emerald-900">
                                        <span>📄 File Terpilih:</span>
                                        <span class="font-mono text-[11px] truncate" x-text="selectedFileName"></span>
                                    </div>
                                </template>
                                @error('pdf_document')
                                    <p class="mt-1.5 p-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Options Checkboxes -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="show_on_homepage" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                <span class="font-bold text-slate-800">🌐 Tampilkan di Beranda Utama (Homepage Kartu Layanan)</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                <span class="font-bold text-slate-800">✅ Status Layanan Aktif</span>
                            </label>
                        </div>

                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition">Simpan Layanan Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL 2: EDIT LAYANAN & JENIS SURAT -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <template x-if="selectedType">
                    <form :action="'{{ url('admin/jenis-layanan') }}/' + selectedType.id" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                                <h3 class="text-base font-bold text-slate-800">Edit Layanan & Jenis Surat</h3>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                            
                            <!-- Nama & Kode -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block font-bold text-slate-800 mb-1">Nama Layanan / Jenis Surat <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" x-model="selectedType.name" required 
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Kode Surat</label>
                                    <input type="text" name="code" x-model="selectedType.code" 
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-mono uppercase font-bold">
                                </div>
                            </div>



                            <!-- Deskripsi Layanan -->
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Deskripsi Singkat Layanan</label>
                                <textarea name="description" x-model="selectedType.description" rows="2" 
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium"></textarea>
                            </div>

                            <!-- Dokumen Persyaratan Wajib -->
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Dokumen Persyaratan Wajib</label>
                                <template x-for="(doc, index) in editDocsArray" :key="index">
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" x-model="editDocsArray[index]" name="required_documents[]" placeholder="Contoh: Foto KTP / Kartu Keluarga" 
                                            class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                        <button type="button" @click="editDocsArray.splice(index, 1)" x-show="editDocsArray.length > 1" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="editDocsArray.push('')" class="mt-1 text-xs font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Persyaratan
                                </button>
                                <p class="text-[11px] text-slate-400 mt-1">Daftar berkas lampiran yang disiapkan warga. (Kosongkan baris jika tidak jadi menambah)</p>
                            </div>

                            <!-- Info SOP & Jam Operasional -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block font-bold text-slate-800 mb-1">Jam Operasional</label>
                                    <input type="text" name="operational_hours" x-model="selectedType.operational_hours" placeholder="Senin - Jumat, 08:00 - 14:00 WIB" 
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Estimasi Waktu Penyelesaian</label>
                                    <input type="text" name="estimated_time" x-model="selectedType.estimated_time" placeholder="Misal: 1 Hari Kerja" 
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Biaya Pelayanan</label>
                                    <input type="text" name="cost" x-model="selectedType.cost" placeholder="Misal: Gratis" 
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                </div>
                                <div x-data="{ selectedFileName: null }" class="sm:col-span-2">
                                    <label class="block font-bold text-slate-800 mb-1">Upload File PDF Surat/Formulir (Biarkan kosong jika tidak ingin mengubah)</label>
                                    <input type="file" name="pdf_document" accept="application/pdf"
                                        @change="selectedFileName = $event.target.files[0] ? $event.target.files[0].name : null"
                                        class="w-full p-2 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                    
                                    <template x-if="selectedFileName">
                                        <div class="mt-2 p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center gap-2 text-xs font-semibold text-emerald-900">
                                            <span>📄 File Baru Terpilih:</span>
                                            <span class="font-mono text-[11px] truncate" x-text="selectedFileName"></span>
                                        </div>
                                    </template>

                                    <template x-if="!selectedFileName && selectedType.pdf_document">
                                        <div class="mt-2.5 p-2.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-rose-500 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 shadow-sm">PDF</div>
                                                <div class="min-w-0 text-[11px]">
                                                    <div class="font-bold text-slate-900 truncate" x-text="selectedType.pdf_document.split('/').pop()"></div>
                                                    <div class="text-[10px] text-emerald-700 font-medium">Dokumen PDF Aktif Saat Ini</div>
                                                </div>
                                            </div>
                                            <a :href="'{{ asset('storage') }}/' + selectedType.pdf_document" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg transition shrink-0 flex items-center gap-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                <span>Buka PDF</span>
                                            </a>
                                        </div>
                                    </template>
                                    @error('pdf_document')
                                        <p class="mt-1.5 p-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
                                            <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Options Checkboxes -->
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="show_on_homepage" value="1" :checked="selectedType.show_on_homepage" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                    <span class="font-bold text-slate-800">🌐 Tampilkan di Beranda Utama (Homepage Kartu Layanan)</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="is_active" value="1" :checked="selectedType.is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                    <span class="font-bold text-slate-800">✅ Status Layanan Aktif</span>
                                </label>
                            </div>

                        </div>

                        <!-- Footer Buttons -->
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
