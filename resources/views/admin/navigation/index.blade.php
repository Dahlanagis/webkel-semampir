@extends('layouts.admin')

@section('title', 'Kelola Header Navigasi')
@section('header-title', 'Kelola Header Navigasi')
@section('header-subtitle', 'Manajemen tautan menu Profil, Layanan, Dokumen, dan Informasi pada Navbar Beranda')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Daftar Menu Header</h2>
                <p class="text-sm text-slate-500 mt-1">Atur urutan dan tautan untuk menu dropdown di navbar publik.</p>
            </div>
            <button type="button" @click="$dispatch('open-modal', 'modal-add-menu')" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-bold rounded-xl shadow-sm transition inline-flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Tautan
            </button>
        </div>

        @php
            $sections = [
                'profil' => 'Dropdown Profil',
                'layanan' => 'Dropdown Layanan',
                'dokumen' => 'Dropdown Dokumen',
                'informasi' => 'Dropdown Informasi'
            ];
        @endphp

        @foreach($sections as $sectionKey => $sectionLabel)
            <div class="mb-8 last:mb-0">
                <h3 class="text-md font-extrabold text-slate-700 mb-4 border-b border-slate-100 pb-2 uppercase">{{ $sectionLabel }}</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="py-3 px-4 font-bold">Urutan</th>
                                <th class="py-3 px-4 font-bold">Label Tautan</th>
                                <th class="py-3 px-4 font-bold">Target URL</th>
                                <th class="py-3 px-4 font-bold text-center">Status</th>
                                <th class="py-3 px-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100">
                            @if(isset($menus[$sectionKey]) && count($menus[$sectionKey]) > 0)
                                @foreach($menus[$sectionKey] as $menu)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3 px-4 text-center w-16 font-bold text-slate-600">{{ $menu->order }}</td>
                                        <td class="py-3 px-4 font-semibold text-slate-800">{{ $menu->title }}</td>
                                        <td class="py-3 px-4 text-slate-500 font-mono text-xs">{{ $menu->url }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($menu->is_active)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 uppercase">Aktif</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">Sembunyi</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" @click="$dispatch('open-edit-menu', {{ json_encode($menu) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                @if(!in_array($menu->url, ['/visi-misi', '/sejarah', '/struktur-organisasi']))
                                                <form action="{{ route('admin.navigation.destroy', $menu->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus tautan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-500 text-sm">Belum ada tautan di menu ini.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Tambah Menu -->
<div x-data="{ open: false, section: 'profil' }" @open-modal.window="if($event.detail === 'modal-add-menu') { open = true; section = 'profil'; }" x-show="open" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div x-show="open" @click="open = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div x-show="open" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
            <form action="{{ route('admin.navigation.store') }}" method="POST">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800" id="modal-title">Tambah Tautan Menu</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Bagian (Dropdown)</label>
                        <select name="section" x-model="section" required class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-sm focus:ring-slate-500 focus:border-slate-500">
                            <option value="profil">Dropdown Profil</option>
                            <option value="layanan">Dropdown Layanan</option>
                            <option value="dokumen">Dropdown Dokumen</option>
                            <option value="informasi">Dropdown Informasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Label Tautan</label>
                        <input type="text" name="title" required placeholder="Contoh: Sejarah Kelurahan" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Target URL (Opsional)</label>
                        <input type="text" name="url" placeholder="Contoh: /visi-misi, /sejarah, atau https://example.com" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                        <p class="mt-1 text-[11px] text-slate-500">Kosongkan jika ingin otomatis membuat entri baru, atau isi tautan internal (contoh: <code>/visi-misi</code>) atau URL luar.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Urutan Tampil</label>
                        <input type="number" name="order" value="0" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Publikasi</label>
                        <div class="grid grid-cols-2 gap-3" x-data="{ pubStatus: '1' }">
                            <label :class="pubStatus === '1' ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition select-none">
                                <input type="radio" name="is_active" value="1" x-model="pubStatus" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                        Publikasikan
                                    </div>
                                    <div class="text-[10px] text-slate-500">Tampil di menu</div>
                                </div>
                            </label>

                            <label :class="pubStatus === '0' ? 'border-amber-500 bg-amber-50/60 ring-1 ring-amber-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition select-none">
                                <input type="radio" name="is_active" value="0" x-model="pubStatus" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                        Simpan Draf
                                    </div>
                                    <div class="text-[10px] text-slate-500">Sembunyikan</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="open = false" class="px-4 py-2 text-slate-600 font-semibold text-sm hover:bg-slate-200 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-600 hover:bg-slate-700 text-white font-bold text-sm rounded-xl shadow-sm transition">Simpan Tautan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Menu -->
<div x-data="{ open: false, data: {} }" @open-edit-menu.window="data = $event.detail; open = true" x-show="open" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div x-show="open" @click="open = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div x-show="open" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
            <form :action="`/admin/navigation/${data.id}`" method="POST">
                @csrf @method('PUT')
                <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800" id="modal-title">Edit Tautan Menu</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Bagian (Dropdown)</label>
                        <select name="section" x-model="data.section" required class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-sm focus:ring-slate-500 focus:border-slate-500">
                            <option value="profil">Dropdown Profil</option>
                            <option value="layanan">Dropdown Layanan</option>
                            <option value="dokumen">Dropdown Dokumen</option>
                            <option value="informasi">Dropdown Informasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Label Tautan</label>
                        <input type="text" name="title" x-model="data.title" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Target URL</label>
                        <input type="text" name="url" x-model="data.url" placeholder="Contoh: /sejarah atau https://example.com" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Urutan Tampil</label>
                        <input type="number" name="order" x-model="data.order" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-slate-500 focus:border-slate-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Publikasi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label :class="(data && (data.is_active == 1 || data.is_active === true)) ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition select-none">
                                <input type="radio" name="is_active" value="1" :checked="data && (data.is_active == 1 || data.is_active === true)" @change="data.is_active = 1" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                        Publikasikan
                                    </div>
                                    <div class="text-[10px] text-slate-500">Tampil di menu</div>
                                </div>
                            </label>

                            <label :class="(data && (data.is_active == 0 || data.is_active === false)) ? 'border-amber-500 bg-amber-50/60 ring-1 ring-amber-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition select-none">
                                <input type="radio" name="is_active" value="0" :checked="data && (data.is_active == 0 || data.is_active === false)" @change="data.is_active = 0" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                        Simpan Draf
                                    </div>
                                    <div class="text-[10px] text-slate-500">Sembunyikan</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="open = false" class="px-4 py-2 text-slate-600 font-semibold text-sm hover:bg-slate-200 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-600 hover:bg-slate-700 text-white font-bold text-sm rounded-xl shadow-sm transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
