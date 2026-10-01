@extends('layouts.admin')

@section('title', 'Kelola Agenda Kegiatan')
@section('header-title', 'Agenda & Jadwal Kegiatan')
@section('header-subtitle', 'Kelola jadwal rapat, kegiatan kemasyarakatan, sosialisasi, dan pelayanan lapangan Kelurahan Semampir.')

@section('content')
<div class="space-y-6" x-data="{
    showModal: false,
    modalMode: 'create',
    form: {
        id: null,
        title: '',
        category_id: '',
        custom_category: '',
        date: '{{ date('Y-m-d') }}',
        time_start: '08:00',
        time_end: 'Selesai',
        location: 'Balai Pertemuan Kelurahan Semampir',
        organizer: 'Pemerintah Kelurahan Semampir',
        coordinator: '',
        status: 'upcoming',
        description: '',
        is_active: true
    },
    openCreate() {
        this.modalMode = 'create';
        this.form = {
            id: null,
            title: '',
            category_id: '',
            custom_category: '',
            date: '{{ date('Y-m-d') }}',
            time_start: '08:00',
            time_end: 'Selesai',
            location: 'Balai Pertemuan Kelurahan Semampir',
            organizer: 'Pemerintah Kelurahan Semampir',
            coordinator: '',
            status: 'upcoming',
            description: '',
            is_active: true
        };
        this.showModal = true;
    },
    openEdit(item) {
        this.modalMode = 'edit';
        this.form = {
            id: item.id,
            title: item.title || '',
            category_id: item.category_id || '',
            custom_category: '',
            date: item.date ? item.date.substring(0, 10) : '',
            time_start: item.time_start || '',
            time_end: item.time_end || '',
            location: item.location || '',
            organizer: item.organizer || '',
            coordinator: item.coordinator || '',
            status: item.status || 'upcoming',
            description: item.description || '',
            is_active: Boolean(item.is_active)
        };
        this.showModal = true;
    }
}">

    <!-- Flash Status Notification -->
    @if(session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-2xs text-xs font-semibold">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <!-- QUICK STATS SUMMARY CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Total Agenda -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Agenda</p>
                <h4 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-tight">{{ $totalCount }}</h4>
            </div>
        </div>

        <!-- 2. Akan Datang -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mendatang</p>
                <h4 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-tight">{{ $upcomingCount }}</h4>
            </div>
        </div>

        <!-- 3. Bulan Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bulan Ini</p>
                <h4 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-tight">{{ $thisMonthCount }}</h4>
            </div>
        </div>

        <!-- 4. Telah Terlaksana -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai</p>
                <h4 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-tight">{{ $completedCount }}</h4>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">
        
        <!-- Action Header Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <span>Daftar Agenda Kegiatan Kelurahan</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Jadwal kegiatan yang dipublikasikan ke portal kelurahan untuk warga dan aparatur.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.kategori.index', ['type' => 'agenda']) }}" 
                   class="px-3.5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition duration-150 flex items-center gap-1.5 shrink-0">
                    <i class="fas fa-tags text-slate-500 text-xs"></i>
                    <span>Master Kategori</span>
                </a>
                <button type="button" @click="openCreate()" 
                        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition duration-150 flex items-center gap-2 cursor-pointer shrink-0">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Agenda Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <form method="GET" action="{{ route('admin.agenda.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kegiatan, lokasi, atau penyelenggara..." 
                       class="w-full pl-9 pr-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 placeholder:text-slate-400 text-xs font-medium bg-slate-50/50">
                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full py-2.5 px-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-700 text-xs font-semibold bg-white cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Month -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <input type="month" name="month" value="{{ request('month') }}" onchange="this.form.submit()" 
                       class="w-full py-2 px-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-700 text-xs font-semibold bg-white cursor-pointer">
                @if(request('q') || request('status') || request('month'))
                    <a href="{{ route('admin.agenda.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition shrink-0" title="Reset Filter">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- TABLE LIST VIEW -->
        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 text-center w-28">Jadwal & Waktu</th>
                        <th class="py-3 px-4 min-w-[200px]">Kegiatan / Acara</th>
                        <th class="py-3 px-4 min-w-[160px]">Lokasi & Penyelenggara</th>
                        <th class="py-3 px-4 text-center w-32">Status</th>
                        <th class="py-3 px-4 text-center w-20">Publikasi</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($agendas as $agenda)
                        @php
                            $badge = $agenda->status_badge;
                            $eventDate = \Carbon\Carbon::parse($agenda->date);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition group">
                            <!-- Kolom Jadwal (Tile Tanggal) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex flex-col items-center justify-center p-2 rounded-xl bg-slate-100 border border-slate-200/80 min-w-[70px]">
                                    <span class="text-sm font-black text-slate-900 leading-none">{{ $eventDate->format('d') }}</span>
                                    <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider mt-0.5">{{ $eventDate->translatedFormat('M Y') }}</span>
                                    @if($agenda->time_start)
                                        <span class="text-[9px] text-slate-500 font-semibold mt-1 flex items-center gap-0.5">
                                            <i class="far fa-clock text-[8px]"></i>
                                            {{ $agenda->time_start }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Judul & Rincian -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($agenda->category)
                                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $agenda->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-xs text-slate-900 leading-snug">{{ $agenda->title }}</h4>
                                    @if($agenda->description)
                                        <p class="text-[11px] text-slate-500 line-clamp-1 leading-relaxed">{{ $agenda->description }}</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Lokasi & Penyelenggara -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 text-xs text-slate-800 font-semibold">
                                        <i class="fas fa-map-marker-alt text-rose-500 text-[10px] shrink-0"></i>
                                        <span class="truncate">{{ $agenda->location }}</span>
                                    </div>
                                    @if($agenda->organizer)
                                        <div class="text-[10px] text-slate-500 flex items-center gap-1">
                                            <i class="fas fa-users text-slate-400 text-[9px]"></i>
                                            <span class="truncate">{{ $agenda->organizer }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Status -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['class'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>

                            <!-- Kolom Toggle Aktif -->
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('admin.agenda.toggle', $agenda->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg text-xs transition cursor-pointer {{ $agenda->is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-300 hover:bg-slate-100' }}" 
                                            title="{{ $agenda->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                        <i class="fas {{ $agenda->is_active ? 'fa-toggle-on text-lg text-emerald-600' : 'fa-toggle-off text-lg text-slate-300' }}"></i>
                                    </button>
                                </form>
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" @click="openEdit({{ json_encode($agenda) }})" 
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-800 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1 shadow-2xs" 
                                            title="Edit Agenda">
                                        <i class="fas fa-edit text-amber-600 text-[10px]"></i>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('admin.agenda.destroy', $agenda->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda: {{ addslashes($agenda->title) }}?')" 
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Agenda">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <h4 class="font-bold text-sm text-slate-700">Belum Ada Agenda Kegiatan</h4>
                                    <p class="text-xs text-slate-500">Tidak ada agenda yang cocok dengan filter pencarian ini atau belum ada agenda ditambahkan.</p>
                                    <button type="button" @click="openCreate()" class="mt-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition cursor-pointer inline-flex items-center gap-1.5">
                                        <i class="fas fa-plus text-xs"></i>
                                        <span>Tambah Agenda Baru</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($agendas->hasPages())
            <div class="pt-2">
                {{ $agendas->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL POP-UP: TAMBAH & EDIT AGENDA KEGIATAN -->
    <!-- ========================================== -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         style="display: none;"
         @keydown.escape.window="showModal = false">
        
        <!-- Backdrop -->
        <div class="fixed inset-0" @click="showModal = false"></div>

        <!-- Modal Dialog -->
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 z-10 space-y-5"
             @click.stop>
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                        <i :class="modalMode === 'create' ? 'fas fa-calendar-plus' : 'fas fa-calendar-check'"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm" 
                            x-text="modalMode === 'create' ? 'Tambah Agenda Kegiatan Baru' : 'Edit Agenda Kegiatan'"></h4>
                        <p class="text-[11px] text-slate-500">Lengkapi informasi jadwal, tempat, dan penyelenggara kegiatan.</p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Body Form Modal -->
            <form :action="modalMode === 'create' ? '{{ route('admin.agenda.store') }}' : ('{{ url('admin/agenda') }}/' + form.id)" 
                  method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="modalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Nama Kegiatan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Nama / Judul Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" x-model="form.title" required 
                           placeholder="Misal: Rapat Koordinasi Penataan RT/RW Triwulan II" 
                           class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 font-medium bg-white">
                </div>

                <!-- Kategori & Status -->
                <div class="grid grid-cols-2 gap-3">
                    <div x-data="{ isManualCat: false }">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-bold text-slate-700">Kategori Kegiatan</label>
                            <button type="button" @click="isManualCat = !isManualCat; if(!isManualCat) form.custom_category = ''" class="text-[10px] text-amber-600 hover:text-amber-800 font-bold flex items-center gap-1 transition">
                                <span x-text="isManualCat ? '← Pilih Kategori' : '+ Ketik Manual'"></span>
                            </button>
                        </div>
                        <div x-show="!isManualCat">
                            <select name="category_id" x-model="form.category_id" 
                                    @change="if($event.target.value === 'manual'){ isManualCat = true; form.category_id = ''; }"
                                    class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-700 bg-white">
                                <option value="">Pilih Kategori (Opsional)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                                <option value="manual" class="font-bold text-amber-700">+ Ketik Kategori Baru (Manual)...</option>
                            </select>
                        </div>
                        <div x-show="isManualCat" x-cloak>
                            <div class="relative">
                                <input type="text" name="custom_category" x-model="form.custom_category" placeholder="Ketik nama kategori baru (cth: Rapat Warga)..." class="w-full p-2.5 pr-8 border border-amber-400 bg-amber-50/40 rounded-xl focus:ring-2 focus:ring-amber-500 shadow-sm font-semibold text-slate-800 text-xs">
                                <button type="button" @click="isManualCat = false; form.custom_category = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs" title="Batal input manual">
                                    ✕
                                </button>
                            </div>
                            <p class="text-[10px] text-amber-600 mt-1 font-medium">✨ Kategori baru akan otomatis dibuat dan tersimpan.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Kegiatan <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="form.status" required 
                                class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-700 font-semibold bg-white">
                            <option value="upcoming">Akan Datang</option>
                            <option value="ongoing">Sedang Berlangsung</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                </div>

                <!-- Tanggal & Jam -->
                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" x-model="form.date" required 
                               class="w-full p-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jam Mulai</label>
                        <input type="text" name="time_start" x-model="form.time_start" placeholder="08:00 WIB" 
                               class="w-full p-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jam Selesai</label>
                        <input type="text" name="time_end" x-model="form.time_end" placeholder="Selesai" 
                               class="w-full p-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Lokasi / Tempat -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Lokasi / Tempat Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="location" x-model="form.location" required 
                           placeholder="Misal: Pendopo Kantor Kelurahan Semampir / Balai RW 03" 
                           class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 font-medium bg-white">
                </div>

                <!-- Penyelenggara & Koordinator -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Penyelenggara</label>
                        <input type="text" name="organizer" x-model="form.organizer" placeholder="Misal: Seksi Pemerintahan" 
                               class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Koordinator / Narahubung</label>
                        <input type="text" name="coordinator" x-model="form.coordinator" placeholder="Misal: Budi / 08123456789" 
                               class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Uraian / Keterangan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Keterangan / Rincian Agenda <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="description" x-model="form.description" rows="3" 
                              placeholder="Uraian singkat tujuan agenda, pakaian/dresscode peserta, atau perlengkapan yang perlu dibawa..." 
                              class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-700 bg-white leading-relaxed resize-y"></textarea>
                </div>

                <!-- Status Publikasi (Draf vs Publikasi) -->
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 text-xs uppercase tracking-wider">Status Publikasi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label :class="form.is_active ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none">
                            <input type="radio" name="is_active" value="1" :checked="form.is_active" @change="form.is_active = true" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <div>
                                <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                    Publikasikan
                                </div>
                                <div class="text-[10px] text-slate-500">Tayang di agenda website</div>
                            </div>
                        </label>

                        <label :class="!form.is_active ? 'border-amber-500 bg-amber-50/60 ring-1 ring-amber-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none">
                            <input type="radio" name="is_active" value="0" :checked="!form.is_active" @change="form.is_active = false" class="text-amber-600 focus:ring-amber-500 w-4 h-4">
                            <div>
                                <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                    Simpan Draf
                                </div>
                                <div class="text-[10px] text-slate-500">Disimpan sebagai draf</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tombol Submit Form -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showModal = false" 
                            class="px-4 py-2 border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-save"></i>
                        <span x-text="modalMode === 'create' ? 'Simpan Agenda' : 'Perbarui Agenda'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
