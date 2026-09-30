@extends('layouts.app')

@section('title', 'Layanan WhatsApp - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')
<!-- Page Header -->
<div class="bg-slate-900 py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4">Layanan Pengaduan & Informasi WhatsApp</h1>
        <p class="text-slate-100 max-w-2xl mx-auto text-sm sm:text-base">Kanal komunikasi cepat dan interaktif antara warga dan pihak Kelurahan.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <div class="bg-white rounded-3xl p-8 lg:p-12 shadow-sm border border-slate-200 text-center flex flex-col items-center">
        
        <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-6 border-4 border-green-100 shrink-0">
            <i class="fab fa-whatsapp text-5xl"></i>
        </div>

        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-4">Halo Warga!</h2>
        <p class="text-slate-500 text-sm sm:text-base leading-relaxed max-w-2xl mb-8">
            {{ $villageProfile['whatsapp_service_text'] ?? 'Pemerintah Kelurahan menyediakan layanan WhatsApp untuk mempermudah Anda dalam mendapatkan informasi.' }}
        </p>

        <div class="w-full max-w-md bg-slate-50 p-6 rounded-2xl border border-slate-200 mb-8 space-y-4 text-left">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <span class="text-slate-500 text-sm font-semibold">Nomor WhatsApp CS</span>
                <span class="text-slate-900 font-extrabold">{{ $villageProfile['whatsapp'] ?? '0812-3456-7890' }}</span>
            </div>
            <div class="flex items-center justify-between pt-1">
                <span class="text-slate-500 text-sm font-semibold">Waktu Operasional Chat</span>
                <span class="text-slate-600 font-extrabold text-sm">Jam Kerja (Senin - Jumat)</span>
            </div>
        </div>

        @php
            // Format phone number to standard wa.me format
            $waNumber = $villageProfile['whatsapp'] ?? '6281234567890';
            $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
            if(substr($waNumber, 0, 1) == '0') {
                $waNumber = '62' . substr($waNumber, 1);
            }
        @endphp
        
        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="px-8 py-4 bg-green-500 hover:bg-green-600 text-white font-black text-lg rounded-2xl shadow-lg shadow-green-500/30 transition transform hover:-translate-y-1 flex items-center gap-3">
            <i class="fab fa-whatsapp text-2xl"></i>
            <span>Mulai Chat Sekarang</span>
        </a>

        <p class="text-slate-400 text-xs mt-6">
            Mohon gunakan bahasa yang sopan dan jelas. Pesan yang dikirim di luar jam kerja kemungkinan akan dibalas pada hari kerja berikutnya.
        </p>
    </div>
</div>
@endsection
