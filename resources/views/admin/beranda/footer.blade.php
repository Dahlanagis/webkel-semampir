@extends('layouts.admin')

@section('title', 'Info Footer & Media Sosial')
@section('header-title', 'Info Footer & Media Sosial')
@section('header-subtitle', 'Mengatur teks deskripsi dan link media sosial yang tampil di footer website.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="footer">

        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Deskripsi Footer</h3>
            <p class="text-xs text-slate-500">Teks singkat tentang kelurahan yang muncul di bagian bawah web.</p>
        </div>

        <div class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="footer_description" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('footer_description', $profile['footer_description'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="border-b border-slate-100 pb-3 mt-6">
            <h3 class="text-base font-bold text-slate-900">Tautan Media Sosial</h3>
            <p class="text-xs text-slate-500">Masukkan link lengkap (termasuk https://) ke akun resmi kelurahan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mt-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Link Instagram</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <input type="text" name="social_instagram" value="{{ old('social_instagram', $profile['social_instagram'] ?? '') }}" class="w-full pl-9 p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="https://instagram.com/...">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Link YouTube</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <input type="text" name="social_youtube" value="{{ old('social_youtube', $profile['social_youtube'] ?? '') }}" class="w-full pl-9 p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="https://youtube.com/...">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Link TikTok</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <input type="text" name="social_tiktok" value="{{ old('social_tiktok', $profile['social_tiktok'] ?? '') }}" class="w-full pl-9 p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="https://tiktok.com/...">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Link WhatsApp (Portal Pelayanan)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <input type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $profile['social_whatsapp'] ?? '') }}" class="w-full pl-9 p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="https://wa.me/...">
                </div>
            </div>
        </div>

        </div>

        <div class="border-b border-slate-100 pb-3 mt-6">
            <h3 class="text-base font-bold text-slate-900">QR Code Pelayanan</h3>
            <p class="text-xs text-slate-500">Gambar QR yang tampil di footer.</p>
        </div>

        <div class="space-y-4 text-xs mt-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Upload QR Code (JPG/PNG)</label>
                <input type="file" name="qr_code_image" accept="image/jpeg, image/png, image/webp" @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 1/1, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" class="w-full p-2 border border-slate-300 rounded-lg text-xs bg-white">
                @if(!empty($profile['qr_code_image']))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $profile['qr_code_image']) }}" class="h-32 w-32 object-cover rounded-lg border border-slate-200 shadow-sm" alt="QR Code Saat Ini">
                    </div>
                @endif
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end mt-6">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Perubahan</button>
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
        height: 250,
        placeholder: 'Ketik konten di sini...',
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
