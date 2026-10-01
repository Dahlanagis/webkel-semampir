@extends('layouts.admin')

@section('title', 'Pengumuman & Marquee Running Text')
@section('header-title', 'Pengumuman & Running Text Beranda')
@section('header-subtitle', 'Pengaturan teks berjalan marquee di header dan banner pengumuman mendesak portal publik')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, selectedAnn: null }">

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-bullhorn text-slate-700"></i>
                <span>Daftar Pengumuman & Teks Berjalan Marquee</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Konten yang diaktifkan akan langsung tampil pada running text header portal publik</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('home') }}" target="_blank" 
               class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition flex items-center gap-1.5">
                <i class="fas fa-external-link-alt text-[10px]"></i>
                <span>Lihat Website</span>
            </a>

            <button type="button" @click="createModalOpen = true" 
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-1.5">
                <i class="fas fa-plus"></i>
                <span>Tambah Pengumuman</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.pengumuman.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="w-full sm:w-80 relative">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}"
                       placeholder="Cari judul atau isi pengumuman..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-600 shadow-sm">
                <i class="fas fa-search text-slate-400 absolute left-3 top-2.5 text-xs"></i>
                @if(request('q'))
                    <a href="{{ route('admin.pengumuman.index') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-slate-600 shadow-sm font-medium text-slate-700">
                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status Tayang</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Tayang (Aktif)</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif / Draf</option>
                    <option value="urgent" {{ request('status') == 'urgent' ? 'selected' : '' }}>Prioritas Tinggi</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5 w-28">Tipe Badge</th>
                        <th class="py-3.5 px-4 sm:px-5 w-36">Kategori</th>
                        <th class="py-3.5 px-4 sm:px-5 min-w-[260px]">Judul Pengumuman</th>
                        <th class="py-3.5 px-4 sm:px-5 min-w-[220px]">Isi Teks / Running Text</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center w-36 whitespace-nowrap">Status Tayang</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right w-28 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($announcements as $ann)
                        @php
                            $badgeRaw = strtolower(trim($ann->badge_type ?? 'info'));
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <!-- Badge Type -->
                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap align-top">
                                @if($badgeRaw === 'info')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-blue-100 text-blue-900 rounded-md border border-blue-200 inline-block">
                                        INFO
                                    </span>
                                @elseif($badgeRaw === 'warning' || $badgeRaw === 'penting')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-amber-100 text-amber-900 rounded-md border border-amber-200 inline-block">
                                        PENTING
                                    </span>
                                @elseif($badgeRaw === 'danger' || $badgeRaw === 'mendesak')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-rose-100 text-rose-900 rounded-md border border-rose-200 inline-block">
                                        MENDESAK
                                    </span>
                                @elseif($badgeRaw === 'bansos')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-emerald-100 text-emerald-900 rounded-md border border-emerald-200 inline-block">
                                        BANSOS
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-slate-100 text-slate-800 rounded-md border border-slate-200 inline-block">
                                        {{ strtoupper($ann->badge_type) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4 sm:px-5 text-slate-700 align-top">
                                <span class="px-2 py-0.5 text-[10px] bg-slate-100 text-slate-700 rounded-md border border-slate-200 inline-block">
                                    {{ $ann->category->name ?? 'Pengumuman Umum' }}
                                </span>
                            </td>

                            <!-- Judul -->
                            <td class="py-3.5 px-4 sm:px-5 align-top">
                                <div class="space-y-1">
                                    <span class="font-bold text-slate-900 text-xs sm:text-[13px] leading-snug block">
                                        {{ $ann->title }}
                                    </span>
                                    @if($ann->is_urgent)
                                        <span class="inline-block px-2 py-0.5 text-[9px] font-black uppercase tracking-wider bg-rose-500 text-white rounded shadow-sm">
                                            PRIORITAS TINGGI
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Content -->
                            <td class="py-3.5 px-4 sm:px-5 text-slate-700 align-top">
                                <p class="text-xs leading-relaxed text-slate-600 line-clamp-3">
                                    {{ $ann->content }}
                                </p>
                                @if($ann->link_url)
                                    <a href="{{ $ann->link_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 hover:underline mt-1">
                                        <i class="fas fa-link text-[9px]"></i>
                                        <span class="truncate max-w-[180px]">{{ $ann->link_url }}</span>
                                    </a>
                                @endif
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3.5 px-4 sm:px-5 text-center whitespace-nowrap align-middle">
                                <form action="{{ route('admin.pengumuman.toggle', $ann->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition shadow-sm border {{ $ann->is_active ? 'bg-slate-100 text-slate-900 border-slate-300 hover:bg-slate-200' : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100' }}"
                                            title="Klik untuk mengubah status">
                                        <span class="w-2 h-2 rounded-full {{ $ann->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        <span>{{ $ann->is_active ? 'Tampil (Aktif)' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap space-x-1 align-middle">
                                <button type="button" @click="selectedAnn = {{ json_encode($ann) }}; editModalOpen = true" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>

                                <form action="{{ route('admin.pengumuman.destroy', $ann->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengumuman ini?')">
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
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <p class="font-semibold text-slate-600">Belum ada pengumuman / running text yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>


    <!-- MODAL TAMBAH PENGUMUMAN -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="createModalOpen" @click="createModalOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="createModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <form action="{{ route('admin.pengumuman.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold">Tambah Pengumuman / Running Text</h3>
                        <button type="button" @click="createModalOpen = false" class="text-slate-300 hover:text-white">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Pengumuman *</label>
                            <input type="text" name="title" required placeholder="Contoh: Jam Operasional Pelayanan Khusus Bulan Ramadhan" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Warning Badge</label>
                            <select name="badge_type" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-slate-600">
                                <option value="info">Info Layanan (Warna Biru)</option>
                                <option value="penting">Peringatan / Penting (Warna Kuning)</option>
                                <option value="danger">Mendesak (Warna Merah)</option>
                                <option value="bansos">Bantuan Sosial (Warna Hijau)</option>
                            </select>
                        </div>

                        <div x-data="{ isManualCat: false, manualCatVal: '' }">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-700 uppercase tracking-wider">Kategori Pengumuman *</label>
                                <button type="button" @click="isManualCat = !isManualCat; if(!isManualCat) manualCatVal = ''" class="text-[11px] font-bold text-slate-700 hover:text-slate-900 transition flex items-center gap-1">
                                    <span x-text="isManualCat ? '← Pilih Kategori' : '+ Ketik Manual'"></span>
                                </button>
                            </div>
                            <div x-show="!isManualCat">
                                <select name="category_id" 
                                        :required="!isManualCat"
                                        @change="if($event.target.value === 'manual'){ isManualCat = true; $event.target.value = ''; }" 
                                        class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-slate-600">
                                    <option value="">-- Pilih Kategori --</option>
                                    @php
                                        $typeLabels = [
                                            'pengumuman' => 'Kategori Pengumuman',
                                            'berita' => 'Kategori Berita & Informasi',
                                            'agenda' => 'Kategori Agenda Kegiatan',
                                            'galeri' => 'Kategori Galeri Kegiatan',
                                            'dokumen' => 'Kategori Dokumen Publik',
                                        ];
                                        $groupedCats = $categories->groupBy('type');
                                    @endphp
                                    @foreach(['pengumuman', 'berita', 'agenda', 'galeri', 'dokumen'] as $gType)
                                        @if(isset($groupedCats[$gType]) && $groupedCats[$gType]->count() > 0)
                                            <optgroup label="📢 {{ $typeLabels[$gType] ?? ucfirst($gType) }}">
                                                @foreach($groupedCats[$gType] as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                    @foreach($groupedCats as $gType => $cats)
                                        @if(!in_array($gType, ['pengumuman', 'berita', 'agenda', 'galeri', 'dokumen']))
                                            <optgroup label="📢 {{ ucfirst($gType) }}">
                                                @foreach($cats as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                    <option value="manual" class="font-bold text-slate-800">+ Ketik Kategori Baru (Manual)...</option>
                                </select>
                            </div>
                            <div x-show="isManualCat" x-cloak>
                                <div class="relative">
                                    <input type="text" name="custom_category" x-model="manualCatVal" :required="isManualCat" placeholder="Ketik nama kategori baru..." class="w-full p-2.5 pr-8 border border-slate-400 bg-slate-50/50 rounded-xl focus:ring-2 focus:ring-slate-600 shadow-sm font-semibold text-slate-800 text-xs">
                                    <button type="button" @click="isManualCat = false; manualCatVal = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs" title="Batal input manual">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Teks Marquee / Running Text *</label>
                            <textarea name="content" rows="3" required placeholder="Teks yang akan berjalan di header beranda..." class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600"></textarea>
                        </div>

                        <!-- Status Publikasi -->
                        <div class="space-y-1.5" x-data="{ pubStatus: '1' }">
                            <label class="block font-bold text-slate-700 text-xs uppercase tracking-wider">Status Publikasi</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label :class="pubStatus === '1' ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none">
                                    <input type="radio" name="is_active" value="1" x-model="pubStatus" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                            Publikasikan
                                        </div>
                                        <div class="text-[10px] text-slate-500">Tayang di running text portal</div>
                                    </div>
                                </label>

                                <label :class="pubStatus === '0' ? 'border-amber-500 bg-amber-50/60 ring-1 ring-amber-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none">
                                    <input type="radio" name="is_active" value="0" x-model="pubStatus" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                            Simpan Draf
                                        </div>
                                        <div class="text-[10px] text-slate-500">Disimpan sebagai draf</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-1">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-rose-700">
                                <input type="checkbox" name="is_urgent" value="1" class="rounded text-rose-600">
                                <span>Tandai Sebagai Prioritas Sangat Mendesak</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold rounded-xl shadow">Simpan Pengumuman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL EDIT PENGUMUMAN -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="editModalOpen" @click="editModalOpen = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="editModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <template x-if="selectedAnn">
                    <form :action="'{{ url('admin/pengumuman') }}/' + selectedAnn.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-gradient-to-r from-slate-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                            <h3 class="text-base font-bold">Edit Pengumuman</h3>
                            <button type="button" @click="editModalOpen = false" class="text-slate-300 hover:text-white">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Pengumuman *</label>
                                <input type="text" name="title" x-model="selectedAnn.title" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Warning Badge</label>
                                <select name="badge_type" x-model="selectedAnn.badge_type" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-slate-600">
                                    <option value="info">Info Layanan (Warna Biru)</option>
                                    <option value="penting">Peringatan / Penting (Warna Kuning)</option>
                                    <option value="danger">Mendesak (Warna Merah)</option>
                                    <option value="bansos">Bantuan Sosial (Warna Hijau)</option>
                                </select>
                            </div>

                            <div x-data="{ isManualCatEdit: false, manualCatEditVal: '' }" x-init="$watch('selectedAnn', () => { isManualCatEdit = false; manualCatEditVal = ''; })">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Kategori Pengumuman *</label>
                                    <button type="button" @click="isManualCatEdit = !isManualCatEdit; if(!isManualCatEdit) manualCatEditVal = ''" class="text-[11px] font-bold text-slate-700 hover:text-slate-900 transition flex items-center gap-1">
                                        <span x-text="isManualCatEdit ? '← Pilih Kategori' : '+ Ketik Manual'"></span>
                                    </button>
                                </div>
                                <div x-show="!isManualCatEdit">
                                    <select name="category_id" 
                                            x-model="selectedAnn.category_id" 
                                            :required="!isManualCatEdit"
                                            @change="if($event.target.value === 'manual'){ isManualCatEdit = true; }" 
                                            class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-slate-600">
                                        <option value="">-- Pilih Kategori --</option>
                                        @php
                                            $typeLabels = [
                                                'pengumuman' => 'Kategori Pengumuman',
                                                'berita' => 'Kategori Berita & Informasi',
                                                'agenda' => 'Kategori Agenda Kegiatan',
                                                'galeri' => 'Kategori Galeri Kegiatan',
                                                'dokumen' => 'Kategori Dokumen Publik',
                                            ];
                                            $groupedCats = $categories->groupBy('type');
                                        @endphp
                                        @foreach(['pengumuman', 'berita', 'agenda', 'galeri', 'dokumen'] as $gType)
                                            @if(isset($groupedCats[$gType]) && $groupedCats[$gType]->count() > 0)
                                                <optgroup label="📢 {{ $typeLabels[$gType] ?? ucfirst($gType) }}">
                                                    @foreach($groupedCats[$gType] as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                        @foreach($groupedCats as $gType => $cats)
                                            @if(!in_array($gType, ['pengumuman', 'berita', 'agenda', 'galeri', 'dokumen']))
                                                <optgroup label="📢 {{ ucfirst($gType) }}">
                                                    @foreach($cats as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                        <option value="manual" class="font-bold text-slate-800">+ Ketik Kategori Baru (Manual)...</option>
                                    </select>
                                </div>
                                <div x-show="isManualCatEdit" x-cloak>
                                    <div class="relative">
                                        <input type="text" name="custom_category" x-model="manualCatEditVal" :required="isManualCatEdit" placeholder="Ketik nama kategori baru..." class="w-full p-2.5 pr-8 border border-slate-400 bg-slate-50/50 rounded-xl focus:ring-2 focus:ring-slate-600 shadow-sm font-semibold text-slate-800 text-xs">
                                        <button type="button" @click="isManualCatEdit = false; manualCatEditVal = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs" title="Batal input manual">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Teks Marquee / Running Text *</label>
                                <textarea name="content" x-model="selectedAnn.content" rows="3" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600"></textarea>
                            </div>

                            <!-- Status Publikasi -->
                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-700 text-xs uppercase tracking-wider">Status Publikasi</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label :class="(selectedAnn && (selectedAnn.is_active == 1 || selectedAnn.is_active === true)) ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none">
                                        <input type="radio" name="is_active" value="1" :checked="selectedAnn && (selectedAnn.is_active == 1 || selectedAnn.is_active === true)" @change="selectedAnn.is_active = 1" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                                Publikasikan
                                            </div>
                                            <div class="text-[10px] text-slate-500">Tayang di running text portal</div>
                                        </div>
                                    </label>

                                    <label :class="(selectedAnn && (selectedAnn.is_active == 0 || selectedAnn.is_active === false)) ? 'border-amber-500 bg-amber-50/60 ring-1 ring-amber-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none">
                                        <input type="radio" name="is_active" value="0" :checked="selectedAnn && (selectedAnn.is_active == 0 || selectedAnn.is_active === false)" @change="selectedAnn.is_active = 0" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                                Simpan Draf
                                            </div>
                                            <div class="text-[10px] text-slate-500">Disimpan sebagai draf</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-1">
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-rose-700">
                                    <input type="checkbox" name="is_urgent" value="1" :checked="selectedAnn && selectedAnn.is_urgent" class="rounded text-rose-600">
                                    <span>Prioritas Sangat Mendesak</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold rounded-xl shadow">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
