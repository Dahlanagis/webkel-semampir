@extends('layouts.admin')

@section('title', 'Statistik Dasar & Demografi')
@section('header-title', 'Statistik & Demografi Warga')
@section('header-subtitle', 'Mengatur data statistik penduduk, pekerjaan, dan pendidikan.')

@section('content')
<div class="space-y-6" x-data="{
    createOccOpen: false,
    editOccOpen: false,
    selectedOcc: null,
    selectedOccIndex: null,

    createEduOpen: false,
    editEduOpen: false,
    selectedEdu: null,
    selectedEduIndex: null
}">

    <!-- Alert Status -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-times-circle text-rose-600 mt-0.5"></i>
            <div>
                <span class="font-bold">Gagal memproses data:</span>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form 1: Demografi Utama -->
    <form action="{{ route('admin.beranda.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
        @csrf
        <input type="hidden" name="section" value="demografi">
        <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold text-slate-900">Demografi Penduduk Warga</h3>
                <p class="text-xs text-slate-500 mt-0.5">Total penduduk, usia produktif, balita, dll.</p>
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Simpan Demografi</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Total Populasi Penduduk</label>
                <input type="text" name="demo_total" value="{{ old('demo_total', $profile['demographics']['total'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Laki-laki</label>
                <input type="text" name="demo_male" value="{{ old('demo_male', $profile['demographics']['male'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Perempuan</label>
                <input type="text" name="demo_female" value="{{ old('demo_female', $profile['demographics']['female'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            
            <!-- Usia Produktif -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Usia Produktif (Jumlah)</label>
                <input type="text" name="demo_prod_count" value="{{ old('demo_prod_count', $profile['demographics']['productive_count'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Persentase Usia Produktif (%)</label>
                <input type="text" value="{{ $profile['demographics']['productive_pct'] ?? '' }}%" class="w-full p-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-500 font-bold" readonly title="Dihitung otomatis">
            </div>

            <!-- Anak/Balita -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Anak & Balita (Jumlah)</label>
                <input type="text" name="demo_child_count" value="{{ old('demo_child_count', $profile['demographics']['child_count'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Persentase Anak & Balita (%)</label>
                <input type="text" value="{{ $profile['demographics']['child_pct'] ?? '' }}%" class="w-full p-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-500 font-bold" readonly title="Dihitung otomatis">
            </div>

            <!-- Lansia -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Lansia (Jumlah)</label>
                <input type="text" name="demo_elderly_count" value="{{ old('demo_elderly_count', $profile['demographics']['elderly_count'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Persentase Lansia (%)</label>
                <input type="text" value="{{ $profile['demographics']['elderly_pct'] ?? '' }}%" class="w-full p-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-500 font-bold" readonly title="Dihitung otomatis">
            </div>

            <!-- Info Tambahan -->
            <div class="sm:col-span-1">
                <label class="block font-bold text-slate-700 mb-1.5">Kepadatan (Jiwa/km²)</label>
                <input type="text" name="demo_density" value="{{ old('demo_density', $profile['demographics']['density'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1.5">Rata-rata Anggota Keluarga</label>
                <input type="text" name="demo_avg_family" value="{{ old('demo_avg_family', $profile['demographics']['avg_family_size'] ?? '') }}" class="w-full p-2.5 border border-slate-300 rounded-lg">
            </div>
            
            <!-- Hidden inputs so they don't get erased during generic Demografi update -->
            @foreach($profile['demographics']['occupations'] ?? [] as $occ)
                <input type="hidden" name="occ_name[]" value="{{ $occ['name'] }}">
                <input type="hidden" name="occ_count[]" value="{{ $occ['count'] }}">
                <input type="hidden" name="occ_pct[]" value="{{ $occ['pct'] }}">
            @endforeach
            @foreach($profile['demographics']['educations'] ?? [] as $edu)
                <input type="hidden" name="edu_name[]" value="{{ $edu['name'] }}">
                <input type="hidden" name="edu_count[]" value="{{ $edu['count'] }}">
                <input type="hidden" name="edu_pct[]" value="{{ $edu['pct'] }}">
            @endforeach
        </div>
    </form>

    <!-- TABEL 1: PEKERJAAN -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-briefcase text-emerald-700 shrink-0"></i>
                    <span>Tabel Mata Pencaharian</span>
                </h3>
            </div>
            <button type="button" @click="createOccOpen = true" class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>

        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 sm:px-5">Bidang Pekerjaan</th>
                        <th class="py-3 px-4 sm:px-5">Jumlah Orang</th>
                        <th class="py-3 px-4 sm:px-5">Persentase (%)</th>
                        <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($profile['demographics']['occupations'] ?? [] as $index => $occ)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 sm:px-5 font-bold text-slate-900">{{ $occ['name'] }}</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">{{ $occ['count'] }}</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">{{ $occ['pct'] }}%</td>
                            <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button type="button" @click="selectedOcc = {{ json_encode($occ) }}; selectedOccIndex = {{ $index }}; editOccOpen = true;" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>
                                <form action="{{ route('admin.beranda.statistik.destroy', ['type' => 'occupation', 'index' => $index]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pekerjaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-400">Belum ada data pekerjaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- TABEL 2: PENDIDIKAN -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-emerald-700 shrink-0"></i>
                    <span>Tabel Tingkat Pendidikan</span>
                </h3>
            </div>
            <button type="button" @click="createEduOpen = true" class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>

        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 sm:px-5">Tingkat Pendidikan</th>
                        <th class="py-3 px-4 sm:px-5">Jumlah Orang</th>
                        <th class="py-3 px-4 sm:px-5">Persentase (%)</th>
                        <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($profile['demographics']['educations'] ?? [] as $index => $edu)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 sm:px-5 font-bold text-slate-900">{{ $edu['name'] }}</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">{{ $edu['count'] }}</td>
                            <td class="py-3 px-4 sm:px-5 font-medium text-slate-700">{{ $edu['pct'] }}%</td>
                            <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap space-x-1">
                                <button type="button" @click="selectedEdu = {{ json_encode($edu) }}; selectedEduIndex = {{ $index }}; editEduOpen = true;" class="inline-block px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">
                                    Edit
                                </button>
                                <form action="{{ route('admin.beranda.statistik.destroy', ['type' => 'education', 'index' => $index]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jenjang pendidikan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-400">Belum ada data pendidikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL OCCUPATION (PEKERJAAN) -->
    <!-- Create -->
    <div x-show="createOccOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl">
                <form action="{{ route('admin.beranda.statistik.store', 'occupation') }}" method="POST">@csrf
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">Tambah Pekerjaan</h3>
                        <button type="button" @click="createOccOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6 space-y-4 text-xs">
                        <div><label class="block font-bold mb-1">Bidang Pekerjaan</label><input type="text" name="stat_name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                        <div class="grid grid-cols-1 gap-4">
                            <div><label class="block font-bold mb-1">Jumlah</label><input type="text" name="stat_count" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3"><button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit -->
    <div x-show="editOccOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl">
                <template x-if="selectedOcc !== null">
                    <form :action="'{{ url('admin/kelola-beranda/statistik/occupation') }}/' + selectedOccIndex" method="POST">@csrf @method('PUT')
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800">Edit Pekerjaan</h3>
                            <button type="button" @click="editOccOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="p-6 space-y-4 text-xs">
                            <div><label class="block font-bold mb-1">Bidang Pekerjaan</label><input type="text" name="stat_name" x-model="selectedOcc.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                            <div class="grid grid-cols-1 gap-4">
                                <div><label class="block font-bold mb-1">Jumlah</label><input type="text" name="stat_count" x-model="selectedOcc.count" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3"><button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl">Simpan</button></div>
                    </form>
                </template>
            </div>
        </div>
    </div>


    <!-- MODAL EDUCATION (PENDIDIKAN) -->
    <!-- Create -->
    <div x-show="createEduOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl">
                <form action="{{ route('admin.beranda.statistik.store', 'education') }}" method="POST">@csrf
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">Tambah Pendidikan</h3>
                        <button type="button" @click="createEduOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6 space-y-4 text-xs">
                        <div><label class="block font-bold mb-1">Tingkat Pendidikan</label><input type="text" name="stat_name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                        <div class="grid grid-cols-1 gap-4">
                            <div><label class="block font-bold mb-1">Jumlah</label><input type="text" name="stat_count" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3"><button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit -->
    <div x-show="editEduOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl">
                <template x-if="selectedEdu !== null">
                    <form :action="'{{ url('admin/kelola-beranda/statistik/education') }}/' + selectedEduIndex" method="POST">@csrf @method('PUT')
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800">Edit Pendidikan</h3>
                            <button type="button" @click="editEduOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="p-6 space-y-4 text-xs">
                            <div><label class="block font-bold mb-1">Tingkat Pendidikan</label><input type="text" name="stat_name" x-model="selectedEdu.name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                            <div class="grid grid-cols-1 gap-4">
                                <div><label class="block font-bold mb-1">Jumlah</label><input type="text" name="stat_count" x-model="selectedEdu.count" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"></div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3"><button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl">Simpan</button></div>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
