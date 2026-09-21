@extends('layouts.admin')

@section('title', 'Maklumat Pelayanan')
@section('header-title', 'Maklumat Pelayanan Publik')
@section('header-subtitle', 'Mengatur teks janji layanan yang muncul pada beranda.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update-kemitraan') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="maklumat">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Teks Maklumat Pelayanan Publik</h3>
            <p class="text-xs text-slate-500">Teks ini akan muncul ketika warga mengklik tombol Maklumat Pelayanan di Beranda.</p>
        </div>
        
        <div class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Teks Maklumat *</label>
                <textarea name="maklumat_text" rows="5" class="tinymce-editor w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600">{{ old('maklumat_text', $profile['maklumat_text'] ?? "Dengan ini, kami seluruh ASN dan Pegawai Pemerintah Kelurahan Patokan menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji.") }}</textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Maklumat</button>
        </div>
    </form>
    
    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="layanan">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Capaian Metrik Layanan</h3>
            <p class="text-xs text-slate-500">Angka kepuasan masyarakat dan kecepatan pelayanan.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Rata-rata Waktu Pelayanan</label>
                <input type="text" name="srv_time" value="{{ old('srv_time', $profile['service_metrics']['avg_time'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Indeks Kepuasan Masyarakat (IKM)</label>
                <input type="text" name="srv_ikm" value="{{ old('srv_ikm', $profile['service_metrics']['ikm_score'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Metrik Layanan</button>
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
