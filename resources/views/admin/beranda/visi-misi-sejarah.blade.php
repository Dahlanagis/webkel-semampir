@extends('layouts.admin')

@section('title', 'Visi & Misi Kelurahan')
@section('header-title', 'Visi & Misi Kelurahan')
@section('header-subtitle', 'Kelola rumusan visi pembangunan dan misi pelayanan Kelurahan Semampir')

@section('content')
<div class="space-y-6">

    <!-- Header Action Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-bullseye text-slate-700"></i>
                <span>Arah Kebijakan, Visi & Misi</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Konten ini ditayangkan langsung pada halaman profil publik <code>/visi-misi</code></p>
        </div>

        <a href="{{ route('visi-misi') }}" target="_blank" 
           class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition flex items-center gap-1.5 self-start sm:self-auto shadow-sm">
            <i class="fas fa-external-link-alt text-[10px]"></i>
            <span>Lihat Halaman Publik</span>
        </a>
    </div>

    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        @csrf
        <input type="hidden" name="section" value="visi_misi">

        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-pen-nib text-emerald-600"></i>
                <span>Rumusan Visi & Misi Kelurahan</span>
            </h3>
        </div>

        <div class="space-y-5 text-xs">
            <!-- Teks Visi -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Teks Visi Kelurahan <span class="text-rose-500">*</span>
                </label>
                <textarea name="vision" id="visi_editor" rows="4" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">{{ old('vision', $profile['vision'] ?? '') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Visi memuat cita-cita besar dan tujuan jangka panjang Kelurahan Semampir.</p>
            </div>

            <!-- Teks Misi -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Teks Misi Kelurahan <span class="text-rose-500">*</span>
                </label>
                <textarea name="mission" id="misi_editor" rows="8" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">{{ old('mission', $profile['mission'] ?? '') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Misi memuat langkah-langkah konkret dan program kerja utama kelurahan.</p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Simpan Visi & Misi</span>
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        if (typeof window.initSimpelSummernote === 'function') {
            window.initSimpelSummernote('#visi_editor', {
                height: 200,
                placeholder: 'Ketik rumusan visi kelurahan...'
            });
            window.initSimpelSummernote('#misi_editor', {
                height: 280,
                placeholder: 'Ketik rincian misi kelurahan (bisa berupa daftar bernomor)...'
            });
        }
    });
</script>
@endsection
