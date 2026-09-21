@extends('layouts.admin')

@section('title', 'Kemitraan Instansi')
@section('header-title', 'Sinergi Instansi & Kemitraan')
@section('header-subtitle', 'Kelola daftar tautan instansi terkait beserta logonya di beranda.')

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: false,
    editModalOpen: false,
    selectedPartner: null,
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

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Header Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 border-b border-slate-200">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-handshake text-emerald-700 shrink-0"></i>
                    <span>Daftar Mitra Instansi</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar logo dan tautan yang tampil di bagian bawah beranda publik.</p>
            </div>

            <button @click="createModalOpen = true" 
               class="px-4 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                <i class="fas fa-plus"></i>
                <span>Tambah Mitra</span>
            </button>
        </div>

        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5">Logo</th>
                        <th class="py-3.5 px-4 sm:px-5">Nama Mitra</th>
                        <th class="py-3.5 px-4 sm:px-5">Tautan (URL)</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($profile['kemitraan'] ?? [] as $index => $partner)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 sm:px-5 w-24">
                                @if(!empty($partner['logo']))
                                    <div class="w-16 h-12 bg-white border border-slate-200 rounded-lg p-1.5 flex items-center justify-center shadow-sm">
                                        <img src="{{ str_starts_with($partner['logo'], 'http') ? $partner['logo'] : asset('storage/' . $partner['logo']) }}" alt="{{ $partner['name'] }}" class="max-w-full max-h-full object-contain">
                                    </div>
                                @else
                                    <div class="w-16 h-12 bg-slate-100 border border-slate-200 rounded-lg flex items-center justify-center text-slate-400">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 font-bold text-slate-900">
                                {{ $partner['name'] }}
                                @if(!empty($partner['desc']))
                                    <p class="text-[11px] font-normal text-slate-500 mt-1 line-clamp-1">{{ $partner['desc'] }}</p>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 sm:px-5">
                                <a href="{{ $partner['url'] }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 hover:underline inline-flex items-center gap-1">
                                    <span class="line-clamp-1 max-w-[200px]">{{ $partner['url'] }}</span>
                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                </a>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button @click="selectedPartner = {{ json_encode($partner) }}; selectedIndex = {{ $index }}; editModalOpen = true;" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>

                                <form action="{{ route('admin.beranda.kemitraan.destroy', $index) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mitra ini?')">
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
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-slate-200">
                                    <i class="fas fa-handshake text-xl text-slate-400"></i>
                                </div>
                                <p class="font-semibold text-slate-600">Belum ada mitra instansi yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- MODAL 1: TAMBAH MITRA -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <form action="{{ route('admin.beranda.kemitraan.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                            <h3 class="text-base font-bold text-slate-800">Tambah Mitra Baru</h3>
                        </div>
                        <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                        
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Nama Instansi / Mitra <span class="text-rose-500">*</span></label>
                            <input type="text" name="partner_name" required placeholder="Misal: Kementerian Dalam Negeri" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Tautan / URL</label>
                            <input type="url" name="partner_url" placeholder="https://..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Deskripsi Singkat</label>
                            <input type="text" name="partner_desc" placeholder="Penjelasan singkat kemitraan..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                        </div>

                        <div x-data="{ selectedFileName: null }">
                            <label class="block font-bold text-slate-800 mb-1">Logo Mitra <span class="text-rose-500">*</span></label>
                            <input type="file" name="partner_logo" accept="image/png, image/jpeg, image/webp" required
                                @change="selectedFileName = $event.target.files[0] ? $event.target.files[0].name : null"
                                class="w-full p-2 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                            <p class="text-[11px] text-slate-400 mt-1">Maks. 2MB. Format: JPG, PNG, WEBP</p>
                            <template x-if="selectedFileName">
                                <div class="mt-2 p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center gap-2 text-xs font-semibold text-emerald-900">
                                    <span>🖼️ File Terpilih:</span>
                                    <span class="font-mono text-[11px] truncate" x-text="selectedFileName"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition">Simpan Mitra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL 2: EDIT MITRA -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <template x-if="selectedPartner !== null">
                    <form :action="'{{ url('admin/kelola-beranda/kemitraan') }}/' + selectedIndex" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                                <h3 class="text-base font-bold text-slate-800">Edit Mitra Instansi</h3>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                            
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Nama Instansi / Mitra <span class="text-rose-500">*</span></label>
                                <input type="text" name="partner_name" x-model="selectedPartner.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Tautan / URL</label>
                                <input type="url" name="partner_url" x-model="selectedPartner.url" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Deskripsi Singkat</label>
                                <input type="text" name="partner_desc" x-model="selectedPartner.desc" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>

                            <div x-data="{ selectedFileName: null }">
                                <label class="block font-bold text-slate-800 mb-1">Upload Logo Baru (Opsional)</label>
                                <input type="file" name="partner_logo" accept="image/png, image/jpeg, image/webp"
                                    @change="selectedFileName = $event.target.files[0] ? $event.target.files[0].name : null"
                                    class="w-full p-2 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                
                                <template x-if="selectedFileName">
                                    <div class="mt-2 p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center gap-2 text-xs font-semibold text-emerald-900">
                                        <span>🖼️ File Baru Terpilih:</span>
                                        <span class="font-mono text-[11px] truncate" x-text="selectedFileName"></span>
                                    </div>
                                </template>

                                <template x-if="!selectedFileName && selectedPartner.logo">
                                    <div class="mt-3 p-3 bg-slate-50 rounded-2xl border border-slate-200 inline-block">
                                        <div class="text-[10px] text-slate-500 font-bold mb-2 uppercase">Logo Saat Ini:</div>
                                        <div class="h-16 bg-white border border-slate-200 rounded-lg p-2 flex items-center justify-center shadow-sm max-w-[120px]">
                                            <img :src="selectedPartner.logo.startsWith('http') ? selectedPartner.logo : '{{ asset('storage') }}/' + selectedPartner.logo" class="max-w-full max-h-full object-contain">
                                        </div>
                                    </div>
                                </template>
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
