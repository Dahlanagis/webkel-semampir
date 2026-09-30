@extends('layouts.admin')

@section('title', 'Tugas Pokok & Fungsi (TUPOKSI)')
@section('header-title', 'Tugas Pokok & Fungsi (TUPOKSI)')
@section('header-subtitle', 'Kelola pedoman tugas pokok, fungsi, dan wewenang perangkat aparatur Kelurahan Semampir')

@section('content')
<div class="space-y-6">

    <!-- Header Action Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-slate-700"></i>
                <span>Pengaturan Halaman TUPOKSI</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Konten ini ditayangkan langsung pada menu profil publik <code>/halaman/tupoksi</code></p>
        </div>

        <a href="{{ url('/halaman/tupoksi') }}" target="_blank" 
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

    <form action="{{ route('admin.beranda.tupoksi.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        @csrf

        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-file-pen text-emerald-600"></i>
                <span>Materi & Rincian Tugas Pokok & Fungsi</span>
            </h3>
        </div>

        <div class="space-y-5 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Judul Halaman <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $page->title ?? 'Tugas Pokok & Fungsi (Tupoksi)') }}" required 
                       class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-semibold text-slate-800">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Subjudul / Keterangan Singkat
                </label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $page->subtitle ?? 'Landasan tugas pokok, wewenang, dan fungsi kerja aparatur Pemerintah Kelurahan.') }}" 
                       class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 text-slate-800">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Uraian Isi Tugas Pokok & Fungsi (TUPOKSI) <span class="text-rose-500">*</span>
                </label>
                <textarea name="content" id="tupoksi_editor" rows="12" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">{{ old('content', $page->content ?? '') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Uraikan tugas pokok Lurah, Sekretaris Kelurahan, dan para Kepala Seksi (Kasi).</p>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" {{ ($page->is_active ?? true) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span>Tayangkan halaman ini secara aktif di portal publik</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan TUPOKSI</span>
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        if (typeof window.initSimpelSummernote === 'function') {
            window.initSimpelSummernote('#tupoksi_editor', {
                height: 350,
                placeholder: 'Ketik rincian tugas pokok dan fungsi aparatur kelurahan...'
            });
        }
    });
</script>
@endsection
