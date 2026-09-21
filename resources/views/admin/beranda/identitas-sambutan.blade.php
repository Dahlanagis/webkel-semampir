@extends('layouts.admin')

@section('title', 'Identitas Kelurahan')
@section('header-title', 'Identitas & Pejabat Kelurahan')
@section('header-subtitle', 'Kelola informasi profil dasar kelurahan, nama lurah, dan jajaran SOTK.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="identitas_sambutan">

        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Identitas Kepala Kelurahan</h3>
            <p class="text-xs text-slate-500">Informasi dasar nama kelurahan dan pemimpin saat ini.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Nama Kelurahan *</label>
                <input type="text" name="village_name" value="{{ old('village_name', $profile['village_name'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Nama Lengkap Lurah *</label>
                <input type="text" name="head_name" value="{{ old('head_name', $profile['head_name'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">NIP Lurah</label>
                <input type="text" name="head_nip" value="{{ old('head_nip', $profile['head_nip'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Foto Lurah (JPG/PNG)</label>
                <input type="file" name="head_photo" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 3/4, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg text-xs bg-white">
                @if(!empty($profile['head_photo']))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $profile['head_photo']) }}" class="h-24 w-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto Lurah Saat Ini">
                    </div>
                @endif
            </div>
        </div>

        </div>

        <div class="border-b border-slate-100 pb-3 mt-6">
            <h3 class="text-base font-bold text-slate-900">Sambutan Kepala Kelurahan</h3>
            <p class="text-xs text-slate-500">Teks sambutan yang akan tampil di halaman depan website bersama foto Lurah.</p>
        </div>

        <div class="space-y-4 text-xs mt-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Judul Sambutan *</label>
                <input type="text" name="welcome_title" value="{{ old('welcome_title', $profile['welcome_title'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Isi Sambutan *</label>
                <textarea name="welcome_text" id="welcome_editor" rows="6" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('welcome_text', $profile['welcome_text'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end mt-6">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Identitas</button>
        </div>
    </form>


</div>

<!-- TinyMCE Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#welcome_editor',
        plugins: 'lists link image media table code help fullscreen wordcount',
        toolbar: 'styles | bold underline removeformat | forecolor backcolor | bullist numlist align | table | link image media | fullscreen code help',
        menubar: false,
        height: 350,
        placeholder: 'Ketik sambutan di sini...',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #334155; }',
        setup: function (editor) {
            editor.on('init', function () {
                var container = editor.getContainer();
                container.style.border = '2px solid #6ee7b7';
                container.style.borderRadius = '0.5rem';
                container.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
                container.style.overflow = 'hidden';
            });
        }
    });
</script>
@endsection
