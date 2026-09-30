@extends('layouts.admin')

@section('title', 'Struktur Organisasi (SOTK)')
@section('header-title', 'Struktur Organisasi (SOTK)')
@section('header-subtitle', 'Kelola informasi nama dan foto pejabat struktural kelurahan.')

@section('content')
<div class="space-y-6" x-data="{
    coreOfficers: [
        {
            key: 'sekel',
            role_label: {{ json_encode($profile['sekel_role'] ?? 'Sekretaris Kelurahan') }},
            badge_title: 'Sekretariat',
            badge_style: 'bg-slate-100 text-slate-800 border-slate-200',
            name: {{ json_encode($profile['sekel_name'] ?? '') }},
            photo: {{ json_encode($profile['sekel_photo'] ?? '') }},
            previewPhoto: {{ !empty($profile['sekel_photo']) ? json_encode(asset('storage/' . $profile['sekel_photo'])) : 'null' }},
            tupoksi: {{ json_encode($profile['sekel_tupoksi'] ?? '') }},
            file: null
        },
        {
            key: 'kasi_pem',
            role_label: {{ json_encode($profile['kasi_pem_role'] ?? 'Kasi Pemerintahan & Trantib') }},
            badge_title: 'Pemerintahan',
            badge_style: 'bg-slate-100 text-slate-800 border-slate-200',
            name: {{ json_encode($profile['kasi_pem_name'] ?? '') }},
            photo: {{ json_encode($profile['kasi_pem_photo'] ?? '') }},
            previewPhoto: {{ !empty($profile['kasi_pem_photo']) ? json_encode(asset('storage/' . $profile['kasi_pem_photo'])) : 'null' }},
            tupoksi: {{ json_encode($profile['kasi_pem_tupoksi'] ?? '') }},
            file: null
        },
        {
            key: 'kasi_kesra',
            role_label: {{ json_encode($profile['kasi_kesra_role'] ?? 'Kasi Pelayanan & Kesra') }},
            badge_title: 'Sosial & Kesra',
            badge_style: 'bg-slate-100 text-slate-800 border-slate-200',
            name: {{ json_encode($profile['kasi_kesra_name'] ?? '') }},
            photo: {{ json_encode($profile['kasi_kesra_photo'] ?? '') }},
            previewPhoto: {{ !empty($profile['kasi_kesra_photo']) ? json_encode(asset('storage/' . $profile['kasi_kesra_photo'])) : 'null' }},
            tupoksi: {{ json_encode($profile['kasi_kesra_tupoksi'] ?? '') }},
            file: null
        },
        {
            key: 'kasi_ekbang',
            role_label: {{ json_encode($profile['kasi_ekbang_role'] ?? 'Kasi Pemberdayaan & Ekbang') }},
            badge_title: 'Perekonomian',
            badge_style: 'bg-slate-100 text-slate-800 border-slate-200',
            name: {{ json_encode($profile['kasi_ekbang_name'] ?? '') }},
            photo: {{ json_encode($profile['kasi_ekbang_photo'] ?? '') }},
            previewPhoto: {{ !empty($profile['kasi_ekbang_photo']) ? json_encode(asset('storage/' . $profile['kasi_ekbang_photo'])) : 'null' }},
            tupoksi: {{ json_encode($profile['kasi_ekbang_tupoksi'] ?? '') }},
            file: null
        }
    ],
    members: {{ json_encode(array_values($profile['sotk_members'] ?? [])) }},
    showModal: false,
    modalType: 'member', // 'core' | 'member'
    modalIndex: null,
    modalForm: {
        name: '',
        position: '',
        tupoksi: '',
        photo: '',
        previewPhoto: null,
        file: null
    },
    openEditCore(index) {
        this.modalType = 'core';
        this.modalIndex = index;
        const co = this.coreOfficers[index];
        this.modalForm = {
            name: co.name,
            position: co.role_label,
            tupoksi: co.tupoksi || '',
            photo: co.photo || '',
            previewPhoto: co.previewPhoto,
            file: co.file || null
        };
        this.showModal = true;
    },
    openAddMember() {
        this.modalType = 'member';
        this.modalIndex = null;
        this.modalForm = {
            name: '',
            position: '',
            tupoksi: '',
            photo: '',
            previewPhoto: null,
            file: null
        };
        this.showModal = true;
    },
    openEditMember(index) {
        this.modalType = 'member';
        this.modalIndex = index;
        const m = this.members[index];
        this.modalForm = {
            name: m.name,
            position: m.position,
            tupoksi: m.tupoksi || '',
            photo: m.photo || '',
            previewPhoto: m.previewPhoto || (m.photo ? '{{ asset('storage') }}/' + m.photo : null),
            file: m.file || null
        };
        this.showModal = true;
    },
    saveModal() {
        if (!this.modalForm.name || !this.modalForm.name.trim()) {
            alert('Silakan masukkan nama lengkap pejabat / anggota.');
            return;
        }

        if (!this.modalForm.position || !this.modalForm.position.trim()) {
            alert('Silakan masukkan jabatan / posisi.');
            return;
        }

        if (this.modalType === 'core') {
            const co = this.coreOfficers[this.modalIndex];
            co.name = this.modalForm.name.trim();
            co.role_label = this.modalForm.position.trim();
            co.tupoksi = (this.modalForm.tupoksi || '').trim();
            if (this.modalForm.file) {
                co.file = this.modalForm.file;
                co.previewPhoto = this.modalForm.previewPhoto;
                this.$nextTick(() => {
                    const fi = document.getElementById('hidden_file_' + co.key);
                    if (fi && co.file) {
                        const dt = new DataTransfer();
                        dt.items.add(co.file);
                        fi.files = dt.files;
                    }
                });
            }
            this.showModal = false;
            return;
        }

        // Modal member tambahan
        if (!this.modalForm.position || !this.modalForm.position.trim()) {
            alert('Silakan masukkan jabatan / posisi anggota.');
            return;
        }

        if (this.modalIndex === null) {
            const newIndex = this.members.length;
            const newMember = {
                name: this.modalForm.name.trim(),
                position: this.modalForm.position.trim(),
                tupoksi: (this.modalForm.tupoksi || '').trim(),
                photo: '',
                previewPhoto: this.modalForm.previewPhoto,
                file: this.modalForm.file
            };
            this.members.push(newMember);

            if (this.modalForm.file) {
                this.$nextTick(() => {
                    const fi = document.getElementById('hidden_member_file_' + newIndex);
                    if (fi && newMember.file) {
                        const dt = new DataTransfer();
                        dt.items.add(newMember.file);
                        fi.files = dt.files;
                    }
                });
            }
        } else {
            const target = this.members[this.modalIndex];
            target.name = this.modalForm.name.trim();
            target.position = this.modalForm.position.trim();
            target.tupoksi = (this.modalForm.tupoksi || '').trim();
            if (this.modalForm.file) {
                target.file = this.modalForm.file;
                target.previewPhoto = this.modalForm.previewPhoto;
                this.$nextTick(() => {
                    const fi = document.getElementById('hidden_member_file_' + this.modalIndex);
                    if (fi && target.file) {
                        const dt = new DataTransfer();
                        dt.items.add(target.file);
                        fi.files = dt.files;
                    }
                });
            } else if (!this.modalForm.previewPhoto && !this.modalForm.photo) {
                target.file = null;
                target.previewPhoto = null;
                target.photo = '';
            }
        }

        this.showModal = false;
    },
    removeMember(index) {
        const m = this.members[index];
        if (confirm('Hapus ' + (m.name || 'anggota ini') + ' dari daftar SOTK?')) {
            this.members.splice(index, 1);
        }
    },
    prepareSubmit(e) {
        // Hubungkan file pejabat inti jika diubah
        this.coreOfficers.forEach(co => {
            if (co.file) {
                const fi = document.getElementById('hidden_file_' + co.key);
                if (fi && fi.files.length === 0) {
                    const dt = new DataTransfer();
                    dt.items.add(co.file);
                    fi.files = dt.files;
                }
            }
        });

        // Hubungkan file anggota staf tambahan jika diubah
        this.members.forEach((m, idx) => {
            if (m.file) {
                const fi = document.getElementById('hidden_member_file_' + idx);
                if (fi && fi.files.length === 0) {
                    const dt = new DataTransfer();
                    dt.items.add(m.file);
                    fi.files = dt.files;
                }
            }
        });
    }
}">

    <form action="{{ route('admin.beranda.update') }}" method="POST" enctype="multipart/form-data" @submit="prepareSubmit($event)" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        @csrf
        <input type="hidden" name="section" value="sotk">

        <!-- Hidden Inputs: 4 Pejabat Inti -->
        <template x-for="co in coreOfficers" :key="co.key">
            <div class="hidden">
                <input type="hidden" :name="co.key + '_name'" :value="co.name">
                <input type="hidden" :name="co.key + '_role'" :value="co.role_label">
                <input type="hidden" :name="co.key + '_tupoksi'" :value="co.tupoksi">
                <input type="file" :name="co.key + '_photo'" :id="'hidden_file_' + co.key" class="hidden">
            </div>
        </template>

        <!-- Hidden Inputs: Anggota Tambahan -->
        <template x-for="(member, index) in members" :key="index">
            <div class="hidden">
                <input type="hidden" :name="'members[' + index + '][name]'" :value="member.name">
                <input type="hidden" :name="'members[' + index + '][position]'" :value="member.position">
                <input type="hidden" :name="'members[' + index + '][tupoksi]'" :value="member.tupoksi || ''">
                <input type="hidden" :name="'members[' + index + '][existing_photo]'" :value="member.photo || ''">
                <input type="file" :name="'member_photos[' + index + ']'" :id="'hidden_member_file_' + index" class="hidden">
            </div>
        </template>

        <!-- ======================================================= -->
        <!-- SATU TABEL TERPADU: PEJABAT STRUKTURAL INTI & STAF SOTK -->
        <!-- ======================================================= -->
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            <i class="fas fa-sitemap"></i>
                        </span>
                        Daftar Struktur Organisasi & Staf Kelurahan (SOTK)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola informasi nama, jabatan, dan tugas pokok pejabat serta aparatur kelurahan dalam satu tabel terpadu.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full w-fit" x-text="(4 + members.length) + ' Aparatur Kelurahan'">
                        4 Aparatur Kelurahan
                    </span>
                    <button type="button" @click="openAddMember()" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition duration-150 flex items-center gap-2 w-fit cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Anggota Baru</span>
                    </button>
                </div>
            </div>

            <!-- Table List View (Satu Tabel Terpadu) -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-xs mt-4 bg-white">
                <table class="w-full text-left border-collapse min-w-[740px]">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3.5 px-3 text-center w-12">#</th>
                            <th class="py-3.5 px-4 text-center w-20">Foto</th>
                            <th class="py-3.5 px-4 w-1/3">Nama Lengkap & Jabatan</th>
                            <th class="py-3.5 px-4">Tugas Pokok & Fungsi (TUPOKSI)</th>
                            <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <!-- 1. 4 Pejabat Struktural Inti (Fixed Rows) -->
                        <template x-for="(co, index) in coreOfficers" :key="co.key">
                            <tr class="hover:bg-slate-50/70 transition group">
                                <!-- No -->
                                <td class="py-3.5 px-3 text-center font-bold text-slate-400 text-xs" x-text="index + 1"></td>

                                <!-- Foto -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="relative w-11 h-11 rounded-full border-2 border-slate-200 overflow-hidden bg-slate-100 shrink-0 shadow-2xs">
                                            <template x-if="co.previewPhoto">
                                                <img :src="co.previewPhoto" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!co.previewPhoto">
                                                <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-600 font-bold text-xs uppercase" x-text="co.role_label.substring(0, 2)">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </td>

                                <!-- Nama Lengkap & Jabatan -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs tracking-tight" x-text="co.name || '- Belum diisi -'"></div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border"
                                              :class="co.badge_style"
                                              x-text="co.role_label"></span>
                                    </div>
                                </td>

                                <!-- TUPOKSI -->
                                <td class="py-3.5 px-4">
                                    <template x-if="co.tupoksi && co.tupoksi.trim() !== ''">
                                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed" x-text="co.tupoksi"></p>
                                    </template>
                                    <template x-if="!co.tupoksi || co.tupoksi.trim() === ''">
                                        <span class="text-[11px] text-slate-400 italic">Belum ada uraian tugas khusus</span>
                                    </template>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button" @click="openEditCore(index)" 
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 rounded-lg text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1 shadow-2xs" 
                                            title="Edit Pejabat Ini">
                                        <i class="fas fa-edit text-blue-600 text-[10px]"></i>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- 2. Anggota & Staf Tambahan -->
                        <template x-for="(member, index) in members" :key="index">
                            <tr class="hover:bg-slate-50/70 transition group border-t border-slate-100">
                                <!-- No -->
                                <td class="py-3.5 px-3 text-center font-bold text-slate-400 text-xs" x-text="4 + index + 1"></td>

                                <!-- Foto Thumbnail -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="relative w-11 h-11 rounded-full border-2 border-slate-200 overflow-hidden bg-slate-100 shrink-0 shadow-2xs">
                                            <template x-if="member.previewPhoto || member.photo">
                                                <img :src="member.previewPhoto ? member.previewPhoto : '{{ asset('storage') }}/' + member.photo" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!member.previewPhoto && !member.photo">
                                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-600 font-bold text-xs uppercase" x-text="member.name ? member.name.substring(0, 2) : 'ST'">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </td>

                                <!-- Nama Lengkap & Jabatan -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs tracking-tight" x-text="member.name"></div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200"
                                              x-text="member.position"></span>
                                    </div>
                                </td>

                                <!-- TUPOKSI -->
                                <td class="py-3.5 px-4">
                                    <template x-if="member.tupoksi && member.tupoksi.trim() !== ''">
                                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed" x-text="member.tupoksi"></p>
                                    </template>
                                    <template x-if="!member.tupoksi || member.tupoksi.trim() === ''">
                                        <span class="text-[11px] text-slate-400 italic">Tidak ada rincian tugas khusus</span>
                                    </template>
                                </td>

                                <!-- Aksi (Edit & Hapus) -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" @click="openEditMember(index)" 
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1 shadow-2xs" 
                                                title="Edit Anggota">
                                            <i class="fas fa-edit text-blue-600 text-[10px]"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" @click="removeMember(index)" 
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" 
                                                title="Hapus Anggota Ini">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Footer Bar Tabel -->
                <div class="p-3 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span x-text="'Total: ' + (4 + members.length) + ' Aparatur Kelurahan'"></span>
                    <button type="button" @click="openAddMember()" class="text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-plus text-xs"></i>
                        <span>Tambah Anggota Baru</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TUPOKSI PIMPINAN: KEPALA KELURAHAN (LURAH) -->
        <!-- ========================================== -->
        <div class="border-t border-slate-100 pt-6">
            <div class="flex items-center justify-between gap-2 pb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        <i class="fas fa-crown"></i>
                    </span>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">TUPOKSI Pimpinan: Kepala Kelurahan (Lurah)</h4>
                        <p class="text-[11px] text-slate-500">Tugas pokok dan fungsi Lurah yang ditampilkan pada bagan struktur organisasi di website.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-amber-50 text-amber-700 border border-amber-200/60">
                    Pimpinan Kelurahan
                </span>
            </div>
            <div class="mt-3">
                <textarea name="lurah_tupoksi" rows="3" 
                          class="w-full p-3.5 text-xs text-slate-700 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition leading-relaxed resize-y placeholder:text-slate-400 shadow-2xs" 
                          placeholder="Tuliskan uraian tugas pokok dan fungsi Kepala Kelurahan (Lurah)...">{{ old('lurah_tupoksi', $profile['lurah_tupoksi'] ?? '') }}</textarea>
                <div class="text-[10px] text-slate-400 flex items-center justify-between pt-1">
                    <span><i class="fas fa-check-circle text-emerald-500 mr-1"></i>Tampil pada rincian TUPOKSI pimpinan di halaman struktur organisasi</span>
                </div>
            </div>
        </div>

        <!-- Tombol Submit Form -->
        <div class="pt-5 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-7 py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150 cursor-pointer flex items-center gap-2">
                <i class="fas fa-save text-emerald-400"></i>
                <span>Simpan Perubahan SOTK</span>
            </button>
        </div>
    </form>

    <!-- ========================================== -->
    <!-- MODAL POP-UP: TAMBAH & EDIT PEJABAT / ANGGOTA -->
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
        
        <!-- Modal Backdrop Click to Close -->
        <div class="fixed inset-0" @click="showModal = false"></div>

        <!-- Modal Dialog Box -->
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 z-10 space-y-5"
             @click.stop>
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                        <i :class="modalType === 'core' ? 'fas fa-id-badge' : (modalIndex === null ? 'fas fa-user-plus' : 'fas fa-user-edit')"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm" 
                            x-text="modalType === 'core' ? ('Edit Pejabat: ' + modalForm.position) : (modalIndex === null ? 'Tambah Anggota SOTK Baru' : 'Edit Data Anggota SOTK')"></h4>
                        <p class="text-[11px] text-slate-500"
                           x-text="modalType === 'core' ? 'Perbarui nama, foto profil, dan uraian tugas pokok jabatan ini.' : 'Lengkapi data profil jabatan dan tugas anggota staf.'"></p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Body Form Modal -->
            <div class="space-y-4 text-xs">
                <!-- Foto Profil -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Foto Pejabat / Anggota</label>
                    <div class="flex items-center gap-4 p-3.5 bg-slate-50/80 rounded-xl border border-slate-200">
                        <div class="w-16 h-20 rounded-lg border-2 border-slate-300 overflow-hidden bg-slate-200 shrink-0 flex items-center justify-center relative shadow-2xs">
                            <template x-if="modalForm.previewPhoto">
                                <img :src="modalForm.previewPhoto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!modalForm.previewPhoto">
                                <div class="text-center text-slate-400">
                                    <i class="fas fa-user text-2xl"></i>
                                </div>
                            </template>
                        </div>
                        <div class="space-y-1.5 flex-1">
                            <p class="text-xs font-bold text-slate-800">Unggah Foto (3:4)</p>
                            <p class="text-[11px] text-slate-500">Format file JPG, PNG, atau WEBP (Maks. 5 MB).</p>
                            <div class="flex items-center gap-2 pt-0.5">
                                <button type="button" @click="$refs.modalFileInput.click()" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 shadow-2xs cursor-pointer transition">
                                    <i class="fas fa-camera text-blue-600"></i>
                                    <span x-text="modalForm.previewPhoto ? 'Ganti Foto' : 'Pilih Foto'"></span>
                                </button>
                                <template x-if="modalForm.previewPhoto && modalType !== 'core'">
                                    <button type="button" @click="modalForm.previewPhoto = null; modalForm.file = null; modalForm.photo = ''" 
                                            class="text-[11px] text-rose-600 hover:text-rose-800 font-semibold cursor-pointer">
                                        Hapus Foto
                                    </button>
                                </template>
                            </div>
                            <input type="file" x-ref="modalFileInput" accept="image/jpeg, image/png, image/webp" class="hidden"
                                   @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 3/4, onCrop: (blob, url) => { modalForm.file = new File([blob], file.name, {type: file.type}); modalForm.previewPhoto = url; } }); $event.target.value = ''; }">
                        </div>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="modalForm.name" placeholder="Misal: Siti Rahmawati, S.Kom" 
                           class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 text-slate-800 font-medium bg-white">
                </div>

                <!-- Jabatan / Posisi -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Jabatan / Posisi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="modalForm.position" placeholder="Misal: Sekretaris Kelurahan, Kasi Pemerintahan, Bendahara" 
                           class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 text-slate-800 font-medium bg-white">
                </div>

                <!-- TUPOKSI -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Tugas Pokok & Fungsi (TUPOKSI) <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea x-model="modalForm.tupoksi" rows="3" placeholder="Uraikan ringkas tugas pokok yang diemban pada jabatan ini..." 
                              class="w-full p-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 text-slate-700 bg-white leading-relaxed resize-y"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Tugas pokok akan tampil di bagian rincian TUPOKSI bagan struktur organisasi di website.</p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showModal = false" 
                        class="px-4 py-2 border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="saveModal()" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span x-text="modalType === 'core' ? 'Simpan Pejabat' : (modalIndex === null ? 'Simpan ke Daftar' : 'Simpan Perubahan')"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
