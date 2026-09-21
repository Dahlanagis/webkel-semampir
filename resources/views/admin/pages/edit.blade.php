@extends('layouts.admin')

@section('title', 'Edit Konten Halaman')
@section('header-title', 'Edit Konten Halaman: ' . $page->title)
@section('header-subtitle', 'Susun halaman dengan mudah menggunakan sistem Blok Konten')

@section('content')
<div class="space-y-6" x-data="pageBuilder()">
    <div class="flex items-center justify-between mb-2">
        <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl shadow-sm transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ url('/halaman/' . $page->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold rounded-xl shadow-sm transition text-sm border border-emerald-200">
            <i class="fas fa-external-link-alt"></i> Lihat Halaman
        </a>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        @csrf
        @method('PUT')
        
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Halaman (Dari Kelola Header)</label>
                <input type="text" value="{{ $page->title }}" readonly class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-500 cursor-not-allowed">
                <p class="mt-1 text-[11px] text-slate-400">Untuk mengubah judul, silakan ubah melalui menu <strong>Kelola Header</strong>.</p>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Status Tampil</label>
                <label class="inline-flex items-center mt-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $page->is_active ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <span class="ml-2 text-sm text-slate-700 font-semibold">Aktifkan Halaman Ini</span>
                </label>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipe Halaman</label>
                <select name="type" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="standard" {{ $page->type === 'standard' ? 'selected' : '' }}>Standard (Konten Blok)</option>
                    <option value="sotk" {{ $page->type === 'sotk' ? 'selected' : '' }}>SOTK (Struktur Organisasi Otomatis)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Teks Badge (Opsional)</label>
                <input type="text" name="badge_text" value="{{ $page->badge_text }}" placeholder="Contoh: BAGAN STRUKTUR RESMI" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Banner Image (Opsional)</label>
                <input type="file" name="banner_image" accept="image/*" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-sm">
                @if($page->banner_image)
                    <div class="mt-2 text-xs text-slate-500">
                        Banner saat ini: <a href="{{ asset('storage/' . $page->banner_image) }}" target="_blank" class="text-emerald-600 hover:underline">Lihat Gambar</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Susunan Blok Konten</h3>
            
            <!-- Daftar Blok -->
            <div class="space-y-4">
                <template x-for="(block, index) in blocks" :key="index">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden transition group">
                        
                        <!-- Block Header -->
                        <div class="bg-slate-100 px-4 py-2 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                                <span class="font-bold text-sm text-slate-700 uppercase tracking-wider" x-text="'Blok ' + getBlockName(block.type)"></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="moveUp(index)" class="w-7 h-7 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition" title="Naikkan"><i class="fas fa-chevron-up"></i></button>
                                <button type="button" @click="moveDown(index)" class="w-7 h-7 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition" title="Turunkan"><i class="fas fa-chevron-down"></i></button>
                                <div class="w-px h-4 bg-slate-300 mx-1"></div>
                                <button type="button" @click="removeBlock(index)" class="w-7 h-7 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition" title="Hapus Blok"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>

                        <!-- Block Content Area -->
                        <div class="p-4">
                            <!-- TEXT BLOCK -->
                            <template x-if="block.type === 'text'">
                                <div x-data="{
                                        mceId: 'tinymce-block-' + Math.random().toString(36).substring(2, 9),
                                        editorInstance: null
                                     }"
                                     x-init="
                                        $nextTick(() => {
                                            tinymce.init({
                                                target: $refs.textarea,
                                                height: 300,
                                                menubar: false,
                                                plugins: 'advlist autolink lists link charmap preview searchreplace visualblocks code',
                                                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link code',
                                                setup: (editor) => {
                                                    editorInstance = editor;
                                                    editor.on('init', () => {
                                                        editor.setContent(block.content || '');
                                                    });
                                                    editor.on('change keyup blur', () => {
                                                        block.content = editor.getContent();
                                                    });
                                                }
                                            });
                                        });
                                        $watch('block.content', (val) => {
                                            if (editorInstance && editorInstance.getContent() !== val) {
                                                editorInstance.setContent(val || '');
                                            }
                                        });
                                     "
                                >
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Teks / Paragraf</label>
                                    <textarea x-ref="textarea" :id="mceId" class="w-full border border-slate-200 rounded-lg"></textarea>
                                </div>
                            </template>

                            <!-- IMAGE BLOCK -->
                            <template x-if="block.type === 'image'">
                                <div class="flex flex-col gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Pilih Foto</label>
                                        <input type="file" accept="image/*" @change="uploadImage($event, block)" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                        <div x-show="block.uploading" class="text-xs text-blue-600 mt-2 font-bold animate-pulse">Sedang mengunggah...</div>
                                    </div>
                                    
                                    <template x-if="block.url">
                                        <div>
                                            <img :src="block.url" class="h-32 object-contain bg-slate-100 rounded-lg border border-slate-200 mb-3">
                                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan Gambar (Opsional)</label>
                                            <input type="text" x-model="block.caption" placeholder="Contoh: Balai Desa Patokan saat peresmian" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- CARDS BLOCK -->
                            <template x-if="block.type === 'cards'">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-3">Isi Kartu-kartu</label>
                                    <div class="space-y-3">
                                        <template x-for="(item, itemIndex) in block.items" :key="itemIndex">
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm relative pr-10">
                                                <input type="text" x-model="item.title" placeholder="Judul Kartu (misal: Potensi Pertanian)" class="w-full font-bold px-2 py-1.5 border-b border-transparent hover:border-slate-200 focus:border-emerald-500 focus:outline-none focus:ring-0 text-sm mb-1">
                                                <textarea x-model="item.content" rows="2" placeholder="Penjelasan singkat kartu..." class="w-full px-2 py-1.5 border-b border-transparent hover:border-slate-200 focus:border-emerald-500 focus:outline-none focus:ring-0 text-sm text-slate-600"></textarea>
                                                
                                                <button type="button" @click="block.items.splice(itemIndex, 1)" class="absolute top-3 right-3 text-slate-300 hover:text-rose-500 transition">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" @click="block.items.push({title: '', content: ''})" class="mt-3 text-xs font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1.5">
                                        <i class="fas fa-plus"></i> Tambah Kartu Lain
                                    </button>
                                </div>
                            </template>

                            <!-- ALERT BLOCK -->
                            <template x-if="block.type === 'alert'">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Teks Peringatan / Pengumuman</label>
                                    <div class="flex gap-2 mb-3">
                                        <select x-model="block.style" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                            <option value="amber">Kuning (Peringatan)</option>
                                            <option value="blue">Biru (Info)</option>
                                            <option value="emerald">Hijau (Sukses)</option>
                                            <option value="rose">Merah (Penting)</option>
                                        </select>
                                    </div>
                                    <input type="text" x-model="block.title" placeholder="Judul Pengumuman (Opsional)" class="w-full px-3 py-2 font-bold border border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 mb-2">
                                    <textarea x-model="block.content" rows="2" placeholder="Isi pesan peringatan..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="blocks.length === 0" class="text-center py-10 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                    <div class="w-16 h-16 bg-white border border-slate-200 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                        <i class="fas fa-layer-group text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 mb-1">Halaman Masih Kosong</h4>
                    <p class="text-sm text-slate-500">Mulai susun halaman dengan menambahkan blok konten di bawah.</p>
                </div>
            </div>

            <!-- Add Block Buttons -->
            <div class="mt-6 flex flex-wrap gap-2 justify-center p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                <span class="w-full text-center text-[10px] font-black uppercase tracking-wider text-emerald-600/70 mb-1">Tambahkan Blok Konten</span>
                <button type="button" @click="addBlock('text')" class="px-4 py-2 bg-white text-emerald-700 font-bold text-sm rounded-lg shadow-sm border border-emerald-200 hover:bg-emerald-600 hover:text-white transition group">
                    <i class="fas fa-align-left mr-1.5 opacity-50 group-hover:opacity-100"></i> Paragraf Teks
                </button>
                <button type="button" @click="addBlock('image')" class="px-4 py-2 bg-white text-emerald-700 font-bold text-sm rounded-lg shadow-sm border border-emerald-200 hover:bg-emerald-600 hover:text-white transition group">
                    <i class="fas fa-image mr-1.5 opacity-50 group-hover:opacity-100"></i> Gambar / Foto
                </button>
                <button type="button" @click="addBlock('cards')" class="px-4 py-2 bg-white text-emerald-700 font-bold text-sm rounded-lg shadow-sm border border-emerald-200 hover:bg-emerald-600 hover:text-white transition group">
                    <i class="fas fa-th-large mr-1.5 opacity-50 group-hover:opacity-100"></i> Kotak Kartu
                </button>
                <button type="button" @click="addBlock('alert')" class="px-4 py-2 bg-white text-emerald-700 font-bold text-sm rounded-lg shadow-sm border border-emerald-200 hover:bg-emerald-600 hover:text-white transition group">
                    <i class="fas fa-exclamation-circle mr-1.5 opacity-50 group-hover:opacity-100"></i> Kotak Peringatan
                </button>
            </div>
        </div>

        <input type="hidden" name="content" :value="JSON.stringify(blocks)">

        <div class="flex justify-end pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition text-sm">
                <i class="fas fa-save mr-2"></i> Simpan Susunan Halaman
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pageBuilder', () => ({
        blocks: [],
        init() {
            let dbContent = {!! json_encode($page->content) !!};
            if (dbContent && typeof dbContent === 'string') {
                try {
                    let parsed = JSON.parse(dbContent);
                    if (Array.isArray(parsed)) {
                        this.blocks = parsed;
                    } else {
                        // Fallback jika tidak array
                        this.blocks = [{ type: 'text', content: dbContent }];
                    }
                } catch (e) {
                    // Fallback jika berupa HTML lama (TinyMCE)
                    // Hapus tag HTML jika memungkinkan, atau biarkan html as is
                    this.blocks = [{ type: 'text', content: dbContent }];
                }
            }
        },
        getBlockName(type) {
            const names = {
                'text': 'Teks Paragraf',
                'image': 'Gambar',
                'cards': 'Kumpulan Kartu',
                'alert': 'Kotak Peringatan'
            };
            return names[type] || type;
        },
        addBlock(type) {
            if (type === 'text') this.blocks.push({ type: 'text', content: '' });
            if (type === 'image') this.blocks.push({ type: 'image', url: '', caption: '', uploading: false });
            if (type === 'cards') this.blocks.push({ type: 'cards', items: [{ title: '', content: '' }] });
            if (type === 'alert') this.blocks.push({ type: 'alert', title: '', content: '', style: 'amber' });
        },
        removeBlock(index) {
            if (confirm('Hapus blok konten ini?')) {
                this.blocks.splice(index, 1);
            }
        },
        moveUp(index) {
            if (index > 0) {
                let temp = this.blocks[index];
                this.blocks[index] = this.blocks[index - 1];
                this.blocks[index - 1] = temp;
            }
        },
        moveDown(index) {
            if (index < this.blocks.length - 1) {
                let temp = this.blocks[index];
                this.blocks[index] = this.blocks[index + 1];
                this.blocks[index + 1] = temp;
            }
        },
        async uploadImage(event, block) {
            const file = event.target.files[0];
            if(!file) return;
            
            let formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            block.uploading = true;
            
            try {
                let res = await fetch('{{ route("admin.media.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (res.ok) {
                    let data = await res.json();
                    block.url = data.location;
                } else {
                    alert('Gagal mengunggah foto. Pastikan ukuran di bawah 10MB.');
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
            }
            
            block.uploading = false;
        }
    }))
})
</script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        height: 200,
        menubar: false,
        plugins: 'advlist autolink lists link charmap preview searchreplace visualblocks code',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link code',
    });
</script>
@endsection
