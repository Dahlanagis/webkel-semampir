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
                <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 58l7-7m0 0l7 7m-7-7v18M11 5h10M11 9h10"></path></svg>
                <span>Daftar Pengumuman & Teks Berjalan Marquee</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Konten yang diaktifkan akan langsung tampil pada running text header portal publik</p>
        </div>

        <button @click="createModalOpen = true" 
                class="px-4 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Pengumuman / Marquee</span>
        </button>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5">Tipe Badge</th>
                        <th class="py-3.5 px-4 sm:px-5">Kategori</th>
                        <th class="py-3.5 px-4 sm:px-5">Judul Pengumuman</th>
                        <th class="py-3.5 px-4 sm:px-5">Isi Teks / Running Text</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">Urgent</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">Status Tayang</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($announcements as $ann)
                        <tr class="hover:bg-slate-50 transition">
                            <!-- Badge Type -->
                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap">
                                @if($ann->badge_type === 'info')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-blue-100 text-blue-900 rounded-md border border-blue-200">
                                        INFO LAYANAN
                                    </span>
                                @elseif($ann->badge_type === 'warning')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-amber-100 text-amber-900 rounded-md border border-amber-200">
                                        PERINGATAN
                                    </span>
                                @elseif($ann->badge_type === 'danger')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-rose-100 text-rose-900 rounded-md border border-rose-200">
                                        MENDESAK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] bg-slate-100 text-slate-700 rounded">
                                        {{ $ann->badge_type }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4 sm:px-5 text-slate-700">
                                <span class="px-2 py-0.5 text-[10px] bg-slate-100 text-slate-700 rounded-md border border-slate-200 whitespace-nowrap">
                                    {{ $ann->category->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Judul -->
                            <td class="py-3.5 px-4 sm:px-5 font-bold text-slate-900">
                                {{ $ann->title }}
                            </td>

                            <!-- Content -->
                            <td class="py-3.5 px-4 sm:px-5 text-slate-700 max-w-xs">
                                <p class="line-clamp-2 text-xs leading-snug">{{ $ann->content }}</p>
                            </td>

                            <!-- Urgent Flag -->
                            <td class="py-3.5 px-4 sm:px-5 text-center whitespace-nowrap">
                                @if($ann->is_urgent)
                                    <span class="px-2 py-0.5 text-[10px] font-extrabold bg-rose-500 text-white rounded shadow-sm animate-pulse">
                                        PRIORITAS TINGGI
                                    </span>
                                @else
                                    <span class="text-slate-400 font-mono text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3.5 px-4 sm:px-5 text-center whitespace-nowrap">
                                <form action="{{ route('admin.pengumuman.toggle', $ann->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition shadow-sm border {{ $ann->is_active ? 'bg-emerald-100 text-emerald-900 border-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $ann->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        <span>{{ $ann->is_active ? 'Tampil (Aktif)' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button @click="selectedAnn = {{ json_encode($ann) }}; editModalOpen = true" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
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

        <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
            {{ $announcements->links() }}
        </div>
    </div>


    <!-- MODAL TAMBAH PENGUMUMAN -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="createModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="createModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <form action="{{ route('admin.pengumuman.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-emerald-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold">Tambah Pengumuman / Running Text</h3>
                        <button type="button" @click="createModalOpen = false" class="text-emerald-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Pengumuman *</label>
                            <input type="text" name="title" required placeholder="Contoh: Jam Operasional Pelayanan Khusus Bulan Ramadhan" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Warning Badge</label>
                            <select name="badge_type" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600">
                                <option value="info">Info Layanan (Warna Biru)</option>
                                <option value="warning">Peringatan (Warna Kuning)</option>
                                <option value="danger">Mendesak (Warna Merah)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Pengumuman *</label>
                            <select name="category_id" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Teks Marquee / Running Text *</label>
                            <textarea name="content" rows="3" required placeholder="Teks yang akan berjalan di header beranda..." class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600"></textarea>
                        </div>

                        <div class="flex items-center gap-4 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600">
                                <span>Langsung Tayangkan</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer font-bold text-rose-700">
                                <input type="checkbox" name="is_urgent" value="1" class="rounded text-rose-600">
                                <span>Tandai Sangat Mendesak</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold rounded-xl shadow">Simpan Pengumuman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PENGUMUMAN -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="editModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="editModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <template x-if="selectedAnn">
                    <form :action="'{{ url('admin/pengumuman') }}/' + selectedAnn.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-gradient-to-r from-emerald-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                            <h3 class="text-base font-bold">Edit Pengumuman</h3>
                            <button type="button" @click="editModalOpen = false" class="text-emerald-300 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Pengumuman *</label>
                                <input type="text" name="title" x-model="selectedAnn.title" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Warning Badge</label>
                                <select name="badge_type" x-model="selectedAnn.badge_type" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600">
                                    <option value="info">Info Layanan (Warna Biru)</option>
                                    <option value="warning">Peringatan (Warna Kuning)</option>
                                    <option value="danger">Mendesak (Warna Merah)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Pengumuman *</label>
                                <select name="category_id" x-model="selectedAnn.category_id" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Teks Marquee / Running Text *</label>
                                <textarea name="content" x-model="selectedAnn.content" rows="3" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600"></textarea>
                            </div>

                            <div class="flex items-center gap-4 pt-1">
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                                    <input type="checkbox" name="is_active" value="1" :checked="selectedAnn.is_active" class="rounded text-emerald-600">
                                    <span>Aktif Tayang</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer font-bold text-rose-700">
                                    <input type="checkbox" name="is_urgent" value="1" :checked="selectedAnn.is_urgent" class="rounded text-rose-600">
                                    <span>Prioritas Mendesak</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold rounded-xl shadow">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
