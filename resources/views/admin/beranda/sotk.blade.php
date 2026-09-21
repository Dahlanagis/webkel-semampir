@extends('layouts.admin')

@section('title', 'Struktur Organisasi (SOTK)')
@section('header-title', 'Struktur Organisasi (SOTK)')
@section('header-subtitle', 'Kelola informasi nama dan foto pejabat struktural kelurahan.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="sotk">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Nama & Foto Pejabat Struktural (SOTK)</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-xs">
            <!-- Sekel -->
            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Nama Sekretaris Kelurahan</label>
                    <input type="text" name="sekel_name" value="{{ old('sekel_name', $profile['sekel_name'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Foto Sekel</label>
                    <input type="file" name="sekel_photo" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 3/4, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg">
                    @if(!empty($profile['sekel_photo']))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $profile['sekel_photo']) }}" class="h-24 w-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto Sekel Saat Ini">
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Kasi Pem -->
            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Nama Kasi Pemerintahan</label>
                    <input type="text" name="kasi_pem_name" value="{{ old('kasi_pem_name', $profile['kasi_pem_name'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Foto Kasi Pem</label>
                    <input type="file" name="kasi_pem_photo" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 3/4, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg">
                    @if(!empty($profile['kasi_pem_photo']))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $profile['kasi_pem_photo']) }}" class="h-24 w-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto Kasi Pem Saat Ini">
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Kasi Kesra -->
            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Nama Kasi Kesra</label>
                    <input type="text" name="kasi_kesra_name" value="{{ old('kasi_kesra_name', $profile['kasi_kesra_name'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Foto Kasi Kesra</label>
                    <input type="file" name="kasi_kesra_photo" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 3/4, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg">
                    @if(!empty($profile['kasi_kesra_photo']))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $profile['kasi_kesra_photo']) }}" class="h-24 w-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto Kasi Kesra Saat Ini">
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Kasi Ekbang -->
            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Nama Kasi Ekbang</label>
                    <input type="text" name="kasi_ekbang_name" value="{{ old('kasi_ekbang_name', $profile['kasi_ekbang_name'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Foto Kasi Ekbang</label>
                    <input type="file" name="kasi_ekbang_photo" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 3/4, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg">
                    @if(!empty($profile['kasi_ekbang_photo']))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $profile['kasi_ekbang_photo']) }}" class="h-24 w-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto Kasi Ekbang Saat Ini">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="border-b border-slate-100 pb-3 mt-6">
            <h3 class="text-base font-bold text-slate-900">Rincian Tugas Pokok & Fungsi (TUPOKSI)</h3>
        </div>
        <div class="flex flex-wrap justify-center -m-3 text-xs">
            <!-- Lurah -->
            <div class="w-full sm:w-1/2 lg:w-1/3 p-3">
                <label class="block font-bold text-slate-700 mb-1.5">TUPOKSI Lurah</label>
                <textarea name="lurah_tupoksi" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('lurah_tupoksi', $profile['lurah_tupoksi'] ?? '') }}</textarea>
            </div>
            <!-- Sekel -->
            <div class="w-full sm:w-1/2 lg:w-1/3 p-3">
                <label class="block font-bold text-slate-700 mb-1.5">TUPOKSI Sekretaris Kelurahan</label>
                <textarea name="sekel_tupoksi" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('sekel_tupoksi', $profile['sekel_tupoksi'] ?? '') }}</textarea>
            </div>
            <!-- Kasi Pem -->
            <div class="w-full sm:w-1/2 lg:w-1/3 p-3">
                <label class="block font-bold text-slate-700 mb-1.5">TUPOKSI Kasi Pemerintahan</label>
                <textarea name="kasi_pem_tupoksi" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('kasi_pem_tupoksi', $profile['kasi_pem_tupoksi'] ?? '') }}</textarea>
            </div>
            <!-- Kasi Kesra -->
            <div class="w-full sm:w-1/2 lg:w-1/3 p-3">
                <label class="block font-bold text-slate-700 mb-1.5">TUPOKSI Kasi Pelayanan & Kesra</label>
                <textarea name="kasi_kesra_tupoksi" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('kasi_kesra_tupoksi', $profile['kasi_kesra_tupoksi'] ?? '') }}</textarea>
            </div>
            <!-- Kasi Ekbang -->
            <div class="w-full sm:w-1/2 lg:w-1/3 p-3">
                <label class="block font-bold text-slate-700 mb-1.5">TUPOKSI Kasi Pemberdayaan & Ekbang</label>
                <textarea name="kasi_ekbang_tupoksi" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('kasi_ekbang_tupoksi', $profile['kasi_ekbang_tupoksi'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Data SOTK</button>
        </div>
    </form>
</div>

<!-- TinyMCE Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        plugins: 'lists link image media table code help fullscreen wordcount',
        toolbar: 'styles | bold underline removeformat | forecolor backcolor | bullist numlist align | table | link image media | fullscreen code help',
        menubar: false,
        height: 300,
        placeholder: 'Ketik konten di sini (bisa sisipkan gambar/tabel)...',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #334155; }',
        setup: function (editor) {
            editor.on('init', function () {
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
