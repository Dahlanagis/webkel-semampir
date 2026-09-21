@extends('layouts.admin')

@section('title', 'Visi Misi & Sejarah')
@section('header-title', 'Visi Misi, & Sejarah')
@section('header-subtitle', 'Mengatur teks visi misi, dan sejarah kelurahan.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="visi_misi_sejarah">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Visi Misi, & Sejarah</h3>
        </div>
        <div class="space-y-4 text-xs">

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Teks Visi Kelurahan *</label>
                <textarea name="vision" id="visi_editor" rows="3" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('vision', $profile['vision'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Teks Misi Kelurahan (Bisa multi-baris) *</label>
                <textarea name="mission" id="misi_editor" rows="6" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('mission', $profile['mission'] ?? '') }}</textarea>
            </div>
            
            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 mb-4">Pengaturan Sejarah Kelurahan</h4>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Gambar Banner Sejarah (Opsional)</label>
                <input type="file" name="history_hero_image" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 16/9, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg text-xs bg-white">
                @if(!empty($profile['history_hero_image']))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $profile['history_hero_image']) }}" class="w-full sm:w-1/2 h-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Banner Sejarah Saat Ini">
                    </div>
                @endif
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Teks Sejarah Kelurahan (Opsional)</label>
                <textarea name="history_text" id="history_editor" rows="8" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('history_text', $profile['history_text'] ?? '') }}</textarea>
            </div>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Perubahan</button>
        </div>
    </form>
</div>

<!-- TinyMCE Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#visi_editor, #misi_editor, #history_editor',
        plugins: 'lists link image media table code help fullscreen wordcount',
        toolbar: 'styles | bold underline removeformat | forecolor backcolor | bullist numlist align | table | link image media | fullscreen code help',
        menubar: false,
        height: 350,
        placeholder: 'Ketik konten di sini (bisa sisipkan gambar/tabel)...',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #334155; }',
        setup: function (editor) {
            editor.on('init', function () {
                // Menambahkan border hijau dan rounded style pada container editor
                var container = editor.getContainer();
                container.style.border = '2px solid #6ee7b7'; // emerald-300 / hijau
                container.style.borderRadius = '0.5rem';
                container.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
                container.style.overflow = 'hidden';
            });
        }
    });
</script>
@endsection
