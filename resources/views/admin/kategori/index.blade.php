@extends('layouts.admin')

@section('title', 'Manajemen Kategori Informasi')
@section('header-title', 'Kategori Informasi Terpusat')
@section('header-subtitle', 'Kelola semua kategori untuk Berita, Galeri Kegiatan, dan Pengumuman dari satu tempat')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, selectedCategory: null }">

    <!-- Alert Status -->

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span>Daftar Kategori {{ ucfirst($type) }}</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola label kategori agar informasi mudah difilter oleh warga</p>
        </div>

        <button @click="createModalOpen = true" class="px-4 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kategori {{ ucfirst($type) }}</span>
        </button>
    </div>

    <!-- Type Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="{{ route('admin.kategori.index', ['type' => 'berita']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $type === 'berita' ? 'bg-emerald-800 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            Kategori Berita
        </a>
        <a href="{{ route('admin.kategori.index', ['type' => 'pengumuman']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $type === 'pengumuman' ? 'bg-emerald-800 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            Kategori Pengumuman
        </a>
        <a href="{{ route('admin.kategori.index', ['type' => 'galeri']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $type === 'galeri' ? 'bg-emerald-800 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            Kategori Galeri
        </a>
        <a href="{{ route('admin.kategori.index', ['type' => 'dokumen']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $type === 'dokumen' ? 'bg-emerald-800 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            Kategori Dokumen Publik
        </a>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5 w-16 text-center">Warna</th>
                        <th class="py-3.5 px-4 sm:px-5">Nama Kategori</th>
                        <th class="py-3.5 px-4 sm:px-5">Slug / URL</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">Digunakan</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 sm:px-5 text-center">
                                <div class="w-6 h-6 rounded-md shadow-sm mx-auto bg-{{ $category->color_code }}-500"></div>
                            </td>
                            <td class="py-3.5 px-4 sm:px-5 font-bold text-slate-900">
                                {{ $category->name }}
                                @if($category->description)
                                    <div class="text-[10px] text-slate-500 font-normal mt-0.5 line-clamp-1 max-w-xs">{{ $category->description }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 sm:px-5 font-mono text-slate-500">
                                {{ $category->slug }}
                            </td>
                            @php
                                $relationMap = [
                                    'berita' => 'posts',
                                    'galeri' => 'galleries',
                                    'pengumuman' => 'announcements',
                                    'dokumen' => 'documents'
                                ];
                                $countAttr = ($relationMap[$type] ?? 'posts') . '_count';
                            @endphp
                            <td class="py-3.5 px-4 sm:px-5 text-center font-bold text-emerald-700 bg-emerald-50/50">
                                {{ $category->{$countAttr} ?? 0 }} Data
                            </td>
                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button type="button" 
                                        @click="selectedCategory = {
                                            id: {{ $category->id }},
                                            name: {{ json_encode($category->name) }},
                                            description: {{ json_encode($category->description ?? '') }},
                                            color_code: '{{ $category->color_code }}',
                                            updateUrl: '{{ route('admin.kategori.update', $category->id) }}'
                                        }; editModalOpen = true"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300 inline-block">
                                    Edit
                                </button>

                                <form action="{{ route('admin.kategori.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Kategori yang sedang digunakan mungkin tidak dapat dihapus.')">
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
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <p class="font-semibold text-slate-600">Belum ada kategori untuk {{ ucfirst($type) }}.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="createModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="createModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <form action="{{ route('admin.kategori.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    <div class="bg-gradient-to-r from-emerald-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold">Tambah Kategori {{ ucfirst($type) }} Baru</h3>
                        <button type="button" @click="createModalOpen = false" class="text-emerald-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori *</label>
                            <input type="text" name="name" required placeholder="Contoh: Infrastruktur" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600 font-semibold text-sm">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="2" placeholder="Penjelasan mengenai kategori ini..." class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600"></textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 mb-2">Pilih Warna Label Kategori</label>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $colors = ['slate', 'gray', 'zinc', 'neutral', 'stone', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'];
                                @endphp
                                @foreach($colors as $color)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="color_code" value="{{ $color }}" class="peer sr-only" {{ $color === 'slate' ? 'checked' : '' }}>
                                        <div class="w-6 h-6 rounded-md bg-{{ $color }}-500 ring-2 ring-offset-2 ring-transparent peer-checked:ring-{{ $color }}-600 hover:scale-110 transition shadow-sm"></div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold rounded-xl shadow">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="editModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="editModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <template x-if="selectedCategory">
                    <form :action="selectedCategory.updateUrl" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-gradient-to-r from-sky-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                            <h3 class="text-base font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>Edit Kategori</span>
                            </h3>
                            <button type="button" @click="editModalOpen = false" class="text-sky-300 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori *</label>
                                <input type="text" name="name" x-model="selectedCategory.name" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-600 font-semibold text-sm">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Singkat</label>
                                <textarea name="description" x-model="selectedCategory.description" rows="2" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-600"></textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 mb-2">Pilih Warna Label Kategori</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($colors as $color)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color_code" value="{{ $color }}" class="peer sr-only" x-bind:checked="selectedCategory.color_code === '{{ $color }}'">
                                            <div class="w-6 h-6 rounded-md bg-{{ $color }}-500 ring-2 ring-offset-2 ring-transparent peer-checked:ring-{{ $color }}-600 hover:scale-110 transition shadow-sm"></div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-extrabold rounded-xl shadow transition transform hover:-translate-y-0.5">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
