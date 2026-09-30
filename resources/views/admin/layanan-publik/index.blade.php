@extends('layouts.admin')

@section('title', 'Layanan Publik Beranda')
@section('header-title', 'Pengaturan Layanan Publik Beranda')
@section('header-subtitle', 'Kelola daftar layanan cepat yang tampil di halaman beranda')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, selectedService: null }">

    <!-- Header Action Bar & Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Daftar Layanan Publik</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola layanan cepat yang bisa diklik warga di halaman depan</p>
        </div>

        <button @click="createModalOpen = true" 
                class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Layanan Baru</span>
        </button>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5">Urutan</th>
                        <th class="py-3.5 px-4 sm:px-5">Judul & Deskripsi</th>
                        <th class="py-3.5 px-4 sm:px-5">Icon</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">URL Aksi</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">Status Aktif</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right">Aksi Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($services as $svc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap text-center">
                                <span class="font-mono font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded border border-slate-200 inline-block">
                                    {{ $svc->order }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                                    {{ $svc->title }}
                                    @if($svc->badge_label)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-amber-100 text-amber-800 font-bold border border-amber-200 uppercase tracking-wide">{{ $svc->badge_label }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 max-w-xs">
                                    {{ $svc->description }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap">
                                <span class="font-mono font-bold text-slate-950 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200/80 inline-block">
                                    {{ $svc->icon }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-center">
                                @if($svc->action_url)
                                    <span class="text-[11px] text-sky-600 font-medium truncate max-w-[150px] inline-block" title="{{ $svc->action_url }}">
                                        {{ $svc->action_url }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Default (Form)</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-center whitespace-nowrap">
                                <form action="{{ route('admin.layanan-publik.toggle', $svc->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition shadow-sm border {{ $svc->is_active ? 'bg-slate-100 text-slate-900 border-slate-300 hover:bg-slate-200' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $svc->is_active ? 'bg-slate-600' : 'bg-slate-400' }}"></span>
                                        <span>{{ $svc->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button @click="selectedService = {{ json_encode($svc) }}; editModalOpen = true" 
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>

                                <form action="{{ route('admin.layanan-publik.destroy', $svc->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); return window.confirmDelete(this, '{{ addslashes($svc->name) }}', 'Layanan ini akan dihapus secara permanen beserta data SOP dan persyaratannya.');">
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
                                <p class="font-semibold text-slate-600">Belum ada layanan publik yang terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
            {{ $services->links() }}
        </div>
    </div>


    <!-- ========================================== -->
    <!-- MODAL 1: TAMBAH LAYANAN BARU               -->
    <!-- ========================================== -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="createModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="createModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                
                <form action="{{ route('admin.layanan-publik.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold">Tambah Layanan Publik Baru</h3>
                        <button type="button" @click="createModalOpen = false" class="text-slate-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Layanan *</label>
                            <input type="text" name="title" required placeholder="Contoh: Pengajuan KTP" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Layanan *</label>
                            <textarea name="description" rows="2" required placeholder="Penjelasan singkat..." class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Icon (Heroicons) *</label>
                                <input type="text" name="icon" required placeholder="Contoh: document-text" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Label Badge (Opsional)</label>
                                <input type="text" name="badge_label" placeholder="Contoh: Terpopuler" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">URL Aksi (Opsional)</label>
                            <input type="text" name="action_url" placeholder="Kosongkan jika arahnya ke halaman form pengajuan surat" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil (Order)</label>
                            <input type="number" name="order" value="0" min="0" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div>
                                <label class="block font-bold text-slate-800 text-xs uppercase tracking-wider">Status Publikasi</label>
                                <span class="text-[10px] text-slate-500">Layanan aktif dan langsung tampil di portal warga</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold rounded-xl shadow">Simpan Layanan Baru</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- MODAL 2: EDIT LAYANAN                      -->
    <!-- ========================================== -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="editModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="editModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                
                <template x-if="selectedService">
                    <form :action="'{{ url('admin/layanan-publik') }}/' + selectedService.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-gradient-to-r from-slate-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                            <h3 class="text-base font-bold">Edit Layanan Publik</h3>
                            <button type="button" @click="editModalOpen = false" class="text-slate-300 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Layanan *</label>
                                <input type="text" name="title" x-model="selectedService.title" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Layanan *</label>
                                <textarea name="description" x-model="selectedService.description" rows="2" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Icon (Heroicons) *</label>
                                    <input type="text" name="icon" x-model="selectedService.icon" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Label Badge (Opsional)</label>
                                    <input type="text" name="badge_label" x-model="selectedService.badge_label" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">URL Aksi (Opsional)</label>
                                <input type="text" name="action_url" x-model="selectedService.action_url" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil (Order)</label>
                                <input type="number" name="order" x-model="selectedService.order" min="0" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <label class="block font-bold text-slate-800 text-xs uppercase tracking-wider">Status Publikasi</label>
                                    <span class="text-[10px] text-slate-500">Layanan aktif dan langsung tampil di portal warga</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" :checked="selectedService.is_active" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
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
