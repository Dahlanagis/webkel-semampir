@extends('layouts.admin')

@section('title', 'Alamat & Lokasi')
@section('header-title', 'Alamat & Peta Lokasi')
@section('header-subtitle', 'Kelola informasi alamat kantor dan peta lokasi.')

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="lokasi">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Alamat & Peta Lokasi</h3>
        </div>
        <div class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Alamat Lengkap *</label>
                <textarea name="address" rows="3" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600">{{ old('address', $profile['address'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Link Embed Google Maps *</label>
                <input type="url" name="map_embed" value="{{ old('map_embed', $profile['map_embed'] ?? '') }}" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="https://www.google.com/maps/embed?pb=...">
                <p class="text-[10px] text-slate-500 mt-1">Masukkan URL yang ada di dalam atribut "src" dari kode iframe Google Maps Anda (https://www.google.com/maps/embed?pb=...).</p>
            </div>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Simpan Lokasi</button>
        </div>
    </form>
</div>
@endsection
