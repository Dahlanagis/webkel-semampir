@extends('layouts.admin')

@section('title', 'Kontak & Jam Operasional')
@section('header-title', 'Jam Operasional & Kontak')
@section('header-subtitle', 'Kelola informasi kontak pelayanan, telepon, email, dan alamat kantor.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="kontak">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Jam Operasional Pelayanan & Kontak</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jam Kerja Sen - Kam *</label>
                <input type="text" name="office_hours_mon_thu" value="{{ old('office_hours_mon_thu', $profile['office_hours_mon_thu'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jam Kerja Jumat *</label>
                <input type="text" name="office_hours_fri" value="{{ old('office_hours_fri', $profile['office_hours_fri'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">No. Telepon Kantor *</label>
                <input type="text" name="phone" value="{{ old('phone', $profile['phone'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">WhatsApp Darurat</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $profile['whatsapp'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Email Resmi *</label>
                <input type="email" name="email" value="{{ old('email', $profile['email'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div class="sm:col-span-2 mt-2">
                <label class="block font-bold text-slate-700 mb-1.5">Teks Halaman Layanan WhatsApp</label>
                <textarea name="whatsapp_service_text" rows="3" class="tinymce-editor w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('whatsapp_service_text', $profile['whatsapp_service_text'] ?? '') }}</textarea>
                <p class="text-[10px] text-slate-500 mt-1">Teks ini akan muncul sebagai pengantar di halaman khusus Layanan WhatsApp CS.</p>
            </div>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Kontak</button>
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
