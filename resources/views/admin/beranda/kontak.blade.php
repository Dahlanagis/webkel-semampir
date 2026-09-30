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
                <input type="text" name="office_hours_mon_thu" value="{{ old('office_hours_mon_thu', $profile['office_hours_mon_thu'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Jam Kerja Jumat *</label>
                <input type="text" name="office_hours_fri" value="{{ old('office_hours_fri', $profile['office_hours_fri'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">No. Telepon Kantor *</label>
                <input type="text" name="phone" value="{{ old('phone', $profile['phone'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">WhatsApp Darurat</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $profile['whatsapp'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Email Resmi *</label>
                <input type="email" name="email" value="{{ old('email', $profile['email'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">
            </div>
            <div class="sm:col-span-2 mt-2">
                <label class="block font-bold text-slate-700 mb-1.5">Teks Halaman Layanan WhatsApp</label>
                <textarea name="whatsapp_service_text" id="wa_editor" rows="3" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-600">{{ old('whatsapp_service_text', $profile['whatsapp_service_text'] ?? '') }}</textarea>
                <p class="text-[10px] text-slate-500 mt-1">Teks ini akan muncul sebagai pengantar di halaman khusus Layanan WhatsApp CS.</p>
            </div>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">Simpan Kontak</button>
        </div>
    </form>
</div>

<!-- Summernote Initialization -->
<script>
    $(document).ready(function() {
        if (typeof window.initSimpelSummernote === 'function') {
            window.initSimpelSummernote('#wa_editor', {
                height: 220,
                placeholder: 'Ketik konten di sini (bisa sisipkan gambar/tabel)...'
            });
        }
    });
</script>
@endsection
