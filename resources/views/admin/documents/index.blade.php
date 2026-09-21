@extends('layouts.admin')

@section('title', 'Manajemen Dokumen Publik')
@section('header-title', 'Dokumen Publik & Transparansi')
@section('header-subtitle', 'Kelola dokumen PDF untuk diunduh warga')

@section('content')
<div class="space-y-6" x-data="documentManagement()">

    <!-- Alert Status -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-times-circle text-rose-600 mt-0.5"></i>
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

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-folder-open text-emerald-700 shrink-0"></i>
                <span>Daftar Dokumen Publik</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Atur dokumen transparansi dan dokumen layanan publik di sini</p>
        </div>

        <button @click="createModalOpen = true" 
           class="px-4 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
            <i class="fas fa-plus"></i>
            <span>Tambah Dokumen</span>
        </button>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-5">Kode</th>
                        <th class="py-3.5 px-4 sm:px-5">Nama Dokumen</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">File</th>
                        <th class="py-3.5 px-4 sm:px-5 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-[10px] font-extrabold bg-blue-100 text-blue-900 rounded-md border border-blue-200 block mb-1 w-max">
                                    {{ $doc->code ?? 'UMUM' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 font-bold text-slate-900">
                                {{ $doc->name }}
                                <p class="text-[11px] font-normal text-slate-500 mt-1 line-clamp-1">{{ $doc->description }}</p>
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-center">
                                @if($doc->files_count > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[10px] font-bold">
                                        <i class="fas fa-file-pdf"></i> {{ $doc->files_count }} File
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">Tidak ada file</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-center whitespace-nowrap">
                                @if($doc->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button @click="openEditModal({{ json_encode($doc) }})" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>

                                <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen ini?')">
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
                                <p class="font-semibold text-slate-600">Belum ada dokumen yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
            {{ $documents->links() }}
        </div>
    </div>


    <!-- MODAL 1: TAMBAH DOKUMEN -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                            <h3 class="text-base font-bold text-slate-800">Tambah Dokumen Baru</h3>
                        </div>
                        <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                        
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Nama / Judul Dokumen (Isi Tahun & Bulan) <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" x-model="newDocumentName" required placeholder="Misal: Laporan APBDes - 2023" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Kode Dokumen</label>
                                <input type="text" name="code" placeholder="Misal: APBD" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-mono font-bold uppercase">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Persyaratan / Deskripsi</label>
                            <textarea name="description" rows="4" placeholder="Tuliskan daftar persyaratan di sini. Pisahkan dengan baris baru (Enter) untuk setiap poin persyaratan..." 
                            x-init="
                                tinymce.init({
                                    target: $el,
                                    plugins: 'lists link image media table code help fullscreen wordcount',
                                    toolbar: 'styles | bold underline removeformat | forecolor backcolor | bullist numlist align | table | link image media | fullscreen code help',
                                    menubar: false,
                                    height: 350,
                                    setup: function (editor) {
                                        editor.on('init', function () {
                                            var container = editor.getContainer();
                                            container.style.border = '2px solid #6ee7b7';
                                            container.style.borderRadius = '0.5rem';
                                            container.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
                                            container.style.overflow = 'hidden';
                                        });
                                        editor.on('change', function () {
                                            editor.save();
                                        });
                                    }
                                });
                            "
                            class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium leading-relaxed"></textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Gunakan tombol Enter untuk membuat daftar persyaratan baru (akan ditampilkan sebagai bullet list).</p>
                        </div>

                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block font-bold text-slate-800">Daftar File PDF</label>
                                <button type="button" @click="createFiles.push({id: Date.now()})" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-[11px] font-bold rounded-lg transition-colors flex items-center gap-1.5">
                                    <i class="fas fa-plus"></i> Tambah File
                                </button>
                            </div>
                            
                            <template x-for="(file, index) in createFiles" :key="file.id">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="flex-1 w-full">
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Nama/Tahun File <span class="text-rose-500">*</span></label>
                                        <input type="text" name="file_names[]" required placeholder="Misal: Tahun 2023" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white text-xs">
                                    </div>
                                    <div class="flex-1 w-full">
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">File PDF <span class="text-rose-500">*</span></label>
                                        <input type="file" name="pdf_documents[]" accept="application/pdf" required class="w-full text-xs">
                                    </div>
                                    <div class="mt-4 sm:mt-5 self-end">
                                        <button type="button" @click="createFiles.splice(index, 1)" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors tooltip-trigger" title="Hapus Baris">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="createFiles.length === 0" class="text-center p-4 border border-dashed border-slate-300 rounded-xl text-slate-400 text-xs font-medium">
                                Belum ada file. Klik tombol "Tambah File" di atas.
                            </div>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                <span class="font-bold text-slate-800">Tampilkan di halaman publik</span>
                            </label>
                        </div>

                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition">Simpan Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL 2: EDIT DOKUMEN -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-xl w-full border border-slate-200 my-8">
                <template x-if="selectedDoc">
                    <form :action="'{{ url('admin/documents') }}/' + selectedDoc.id" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                                <h3 class="text-base font-bold text-slate-800">Edit Dokumen</h3>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                            
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Nama / Judul Dokumen (Isi Tahun & Bulan) <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="selectedDoc.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Kode Dokumen</label>
                                    <input type="text" name="code" x-model="selectedDoc.code" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-mono font-bold uppercase">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Persyaratan / Deskripsi</label>
                                <textarea name="description" x-model="selectedDoc.description" rows="4" placeholder="Tuliskan daftar persyaratan di sini. Pisahkan dengan baris baru (Enter)..." 
                                x-init="
                                    setTimeout(() => {
                                        tinymce.init({
                                            target: $el,
                                            plugins: 'lists link image media table code help fullscreen wordcount',
                                            toolbar: 'styles | bold underline removeformat | forecolor backcolor | bullist numlist align | table | link image media | fullscreen code help',
                                            menubar: false,
                                            height: 350,
                                            setup: function (editor) {
                                                editor.on('init', function () {
                                                    var container = editor.getContainer();
                                                    container.style.border = '2px solid #0284c7'; // sky-600 for edit
                                                    container.style.borderRadius = '0.5rem';
                                                    container.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
                                                    container.style.overflow = 'hidden';
                                                });
                                                editor.on('change', function () {
                                                    editor.save();
                                                    $el.dispatchEvent(new Event('input'));
                                                });
                                            }
                                        });
                                    }, 50);
                                "
                                class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 font-medium leading-relaxed"></textarea>
                                <p class="text-[10px] text-slate-400 mt-1">Gunakan tombol Enter untuk membuat daftar persyaratan baru (akan ditampilkan sebagai bullet list).</p>
                            </div>

                            <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="block font-bold text-slate-800">Daftar File PDF Terunggah</label>
                                    <button type="button" @click="editFiles.push({id: Date.now()})" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-[11px] font-bold rounded-lg transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-plus"></i> Tambah File Baru
                                    </button>
                                </div>

                                <!-- Existing Files -->
                                <template x-if="selectedDoc && selectedDoc.files && selectedDoc.files.length > 0">
                                    <div class="space-y-2 mb-4">
                                        <template x-for="file in selectedDoc.files" :key="file.id">
                                            <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-xl">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-xs" x-text="file.name"></div>
                                                        <a :href="'{{ asset('storage') }}/' + file.file_path" target="_blank" class="text-[10px] text-emerald-600 hover:underline font-medium">Buka File</a>
                                                    </div>
                                                </div>
                                                <button type="button" @click="deleteExistingFile(file.id)" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors tooltip-trigger" title="Hapus File">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!selectedDoc || !selectedDoc.files || selectedDoc.files.length === 0">
                                    <div class="text-center p-3 border border-dashed border-slate-300 rounded-xl text-slate-400 text-xs font-medium mb-4">
                                        Belum ada file yang terunggah.
                                    </div>
                                </template>

                                <!-- New Files to upload -->
                                <template x-if="editFiles.length > 0">
                                    <div class="pt-4 border-t border-slate-200 space-y-3">
                                        <label class="block text-xs font-bold text-slate-700 mb-2">File Baru yang Akan Diunggah</label>
                                        <template x-for="(file, index) in editFiles" :key="file.id">
                                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-3 bg-white border border-emerald-200 rounded-xl">
                                                <div class="flex-1 w-full">
                                                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Nama/Tahun File <span class="text-rose-500">*</span></label>
                                                    <input type="text" name="file_names[]" required placeholder="Misal: Tahun 2023" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white text-xs">
                                                </div>
                                                <div class="flex-1 w-full">
                                                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">File PDF <span class="text-rose-500">*</span></label>
                                                    <input type="file" name="pdf_documents[]" accept="application/pdf" required class="w-full text-xs">
                                                </div>
                                                <div class="mt-4 sm:mt-5 self-end">
                                                    <button type="button" @click="editFiles.splice(index, 1)" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors tooltip-trigger" title="Batal Tambah">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="is_active" value="1" :checked="selectedDoc.is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                    <span class="font-bold text-slate-800">Tampilkan di halaman publik</span>
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

    <!-- Kategori modal removed -->

</div>

<!-- TinyMCE Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

<script>
function documentManagement() {
    return {
        createModalOpen: false,
        editModalOpen: false,
        createCategoryModalOpen: false,
        selectedDoc: null,
        categoryName: '',
        newDocumentName: '',
        createFiles: [{id: Date.now()}],
        editFiles: [],
        openEditModal(doc) {
            this.selectedDoc = { ...doc };
            this.editFiles = [];
            this.editModalOpen = true;
        },
        deleteExistingFile(id) {
            if(!confirm('Yakin ingin menghapus file ini?')) return;
            fetch('{{ url("admin/documents/file") }}/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.selectedDoc.files = this.selectedDoc.files.filter(f => f.id !== id);
                    // Update main table count visually
                    window.location.reload(); 
                } else {
                    alert('Gagal menghapus file.');
                }
            });
        }
    };
}
</script>

@endsection
