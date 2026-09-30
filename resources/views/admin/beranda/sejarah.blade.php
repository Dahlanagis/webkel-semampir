@extends('layouts.admin')

@section('title', 'Sejarah & Asal Usul')
@section('header-title', 'Sejarah & Asal Usul Kelurahan')
@section('header-subtitle', 'Kelola dokumentasi sejarah, latar belakang nama, dan foto masa lampau Kelurahan Semampir')

@section('content')
<div class="space-y-6">

    <!-- Header Action Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-scroll text-slate-700"></i>
                <span>Histori & Asal Usul Wilayah</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Konten ini ditayangkan langsung pada halaman profil publik <code>/sejarah</code></p>
        </div>

        <a href="{{ route('sejarah') }}" target="_blank" 
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

    <form action="{{ route('admin.beranda.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        @csrf
        <input type="hidden" name="section" value="sejarah">

        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-book-open text-emerald-600"></i>
                <span>Materi Sejarah & Foto Dokumentasi</span>
            </h3>
        </div>

        <div class="space-y-5 text-xs">
            <!-- Banner / Foto Dokumentasi Sejarah -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Foto Banner Sejarah / Arsip Foto Lama (Opsional)
                </label>
                <input type="file" name="history_hero_image" accept="image/jpeg, image/png, image/webp" 
                       @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 16/9, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; } }) }" 
                       class="w-full p-2 border border-slate-300 rounded-xl text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-slate-600">
                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Rasio 16:9 disarankan.</p>
                
                @if(!empty($profile['history_hero_image']))
                    <div class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-200 inline-block">
                        <span class="text-[10px] font-bold text-slate-500 block mb-1.5">Foto Banner Saat Ini:</span>
                        <img src="{{ asset('storage/' . $profile['history_hero_image']) }}" class="max-w-md h-auto object-cover rounded-lg border border-slate-200 shadow-sm" alt="Banner Sejarah Saat Ini">
                    </div>
                @endif
            </div>

            <!-- Teks Sejarah -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Teks Sejarah & Asal Usul Kelurahan <span class="text-rose-500">*</span>
                </label>
                <textarea name="history_text" id="history_editor" rows="12" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">{{ old('history_text', $profile['history_text'] ?? '') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Tuliskan narasi sejarah pendirian, etimologi penamaan, dan tonggak peristiwa penting kelurahan.</p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan Sejarah</span>
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        if (typeof window.initSimpelSummernote === 'function') {
            window.initSimpelSummernote('#history_editor', {
                height: 350,
                placeholder: 'Tuliskan narasi lengkap sejarah dan asal-usul kelurahan...'
            });
        }
    });
</script>
@endsection
