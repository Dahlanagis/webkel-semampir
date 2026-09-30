@extends('layouts.admin')

@section('title', 'Maklumat Pelayanan')
@section('header-title', 'Maklumat Pelayanan Publik')
@section('header-subtitle', 'Mengatur teks komitmen layanan, nomor ketetapan, dan foto piagam resmi.')

@section('content')
<div class="space-y-6">

    <!-- Alert Notifikasi -->
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs font-semibold shadow-sm">
            <i class="fas fa-exclamation-circle text-rose-600 mt-0.5 text-base shrink-0"></i>
            <div>
                <span class="font-bold">Gagal memperbarui data:</span>
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form 1: Maklumat Pelayanan & Unggah Piagam/Foto Resmi -->
    <form action="{{ route('admin.beranda.update-maklumat') }}" method="POST" enctype="multipart/form-data" 
          x-data="{
              dragover: false,
              fileName: '',
              fileSize: '',
              fileType: '',
              previewUrl: null,
              errorMessage: null,
              deleteExisting: false,
              hasExisting: {{ !empty($profile['maklumat_file']) ? 'true' : 'false' }},
              existingUrl: '{{ !empty($profile['maklumat_file']) ? asset('storage/' . $profile['maklumat_file']) : '' }}',
              existingType: '{{ $profile['maklumat_file_type'] ?? 'image' }}',
              existingName: '{{ $profile['maklumat_file_name'] ?? 'Piagam_Maklumat_Resmi.jpg' }}',
              existingSize: '{{ $profile['maklumat_file_size'] ?? '' }}',

              validateAndPreview(file) {
                  this.errorMessage = null;
                  if (!file) return;

                  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'application/pdf'];
                  if (!allowedTypes.includes(file.type)) {
                      this.errorMessage = 'Format file tidak didukung! Harap unggah foto (JPG, PNG, WEBP) atau berkas PDF.';
                      this.resetFileInput();
                      return;
                  }

                  const maxSize = 3 * 1024 * 1024; // 3 MB
                  if (file.size > maxSize) {
                      this.errorMessage = 'Ukuran file melebihi batas 3 MB! Ukuran berkas saat ini: ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB.';
                      this.resetFileInput();
                      return;
                  }

                  this.fileName = file.name;
                  this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
                  this.fileType = file.type.startsWith('image/') ? 'image' : 'pdf';
                  this.deleteExisting = false;

                  if (this.fileType === 'image') {
                      const reader = new FileReader();
                      reader.onload = (e) => {
                          this.previewUrl = e.target.result;
                      };
                      reader.readAsDataURL(file);
                  } else {
                      this.previewUrl = null;
                  }
              },

              handleDrop(e) {
                  this.dragover = false;
                  const files = e.dataTransfer.files;
                  if (files && files.length > 0) {
                      this.$refs.fileInput.files = files;
                      this.validateAndPreview(files[0]);
                  }
              },

              handleFileChange(e) {
                  const files = e.target.files;
                  if (files && files.length > 0) {
                      this.validateAndPreview(files[0]);
                  }
              },

              resetFileInput() {
                  this.fileName = '';
                  this.fileSize = '';
                  this.fileType = '';
                  this.previewUrl = null;
                  if (this.$refs.fileInput) {
                      this.$refs.fileInput.value = '';
                  }
              },

              markDeleteExisting() {
                  this.deleteExisting = true;
                  this.hasExisting = false;
                  this.resetFileInput();
              }
          }"
          class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        @csrf
        <input type="hidden" name="section" value="maklumat">
        <input type="hidden" name="delete_maklumat_file" :value="deleteExisting ? '1' : '0'">

        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-contract text-slate-700"></i>
                    <span>Teks & Dokumen Maklumat Pelayanan</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Komitmen resmi aparatur yang dipublikasikan secara transparan kepada seluruh warga.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold self-start sm:self-auto">
                <i class="fas fa-shield-alt text-amber-500"></i>
                Standar Pelayanan Publik
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Nomor SK / Ketetapan Resmi</label>
                <div class="relative">
                    <input type="text" name="maklumat_nomor_sk" 
                           value="{{ old('maklumat_nomor_sk', $profile['maklumat_nomor_sk'] ?? '188.45/04/426.411.01/2026') }}" 
                           placeholder="Contoh: 188.45/04/426.411.01/2026" 
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-700 font-medium">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-stamp"></i>
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Nomor Surat Keputusan penetapan standar maklumat</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Keterangan / Keterangan Dokumen</label>
                <div class="relative">
                    <input type="text" name="maklumat_caption" 
                           value="{{ old('maklumat_caption', $profile['maklumat_caption'] ?? 'Dokumen Piagam Penetapan Maklumat Standar Pelayanan Publik Kelurahan Semampir') }}" 
                           placeholder="Contoh: Dokumen Piagam Resmi Penetapan Standar Pelayanan" 
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-700 font-medium">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Teks keterangan singkat yang tampil di bawah foto/lampiran</span>
            </div>
        </div>

        <div class="space-y-2 text-xs">
            <label class="block font-bold text-slate-700">Teks Naskah Maklumat Pelayanan *</label>
            <textarea name="maklumat_text" id="maklumat_editor" rows="5" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-600">{{ old('maklumat_text', $profile['maklumat_text'] ?? "Dengan ini, kami seluruh ASN dan Pegawai Pemerintah Kelurahan Semampir menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji.") }}</textarea>
            <span class="text-[10px] text-slate-400">Teks ini akan ditampilkan di modal piagam maklumat resmi di halaman beranda.</span>
        </div>

        <!-- AREA UPLOAD FOTO / DOKUMEN PENDUKUNG MAKLUMAT (DRAG & DROP) -->
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-xs font-bold text-slate-900">
                        Unggah Foto / Berkas Piagam Maklumat Resmi
                    </label>
                    <p class="text-[11px] text-slate-500">Lampirkan foto piagam fisik berstempel atau pindaian dokumen SK (PDF/Gambar).</p>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                    Opsional (Maks. 3 MB)
                </span>
            </div>

            <!-- Error Banner Realtime -->
            <div x-show="errorMessage" x-cloak class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 font-medium">
                <i class="fas fa-exclamation-triangle text-rose-500 shrink-0"></i>
                <span x-text="errorMessage"></span>
            </div>

            <!-- Drag & Drop Zone -->
            <div class="relative border-2 border-dashed rounded-2xl p-6 transition-all duration-200 text-center cursor-pointer overflow-hidden"
                 :class="dragover ? 'border-amber-500 bg-amber-50/50 scale-[1.005]' : 'border-slate-300 hover:border-slate-400 bg-slate-50/50 hover:bg-slate-50'"
                 @dragover.prevent="dragover = true"
                 @dragleave.prevent="dragover = false"
                 @drop.prevent="handleDrop($event)"
                 @click="$refs.fileInput.click()">

                <input type="file" 
                       name="maklumat_file" 
                       x-ref="fileInput" 
                       @change="handleFileChange($event)"
                       accept="image/jpeg,image/png,image/webp,image/jpg,application/pdf" 
                       class="hidden">

                <div x-show="!fileName && !hasExisting" class="space-y-2.5 py-4">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl border border-amber-500/20 shadow-sm transition-transform group-hover:scale-110">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">
                            <span class="text-amber-600 hover:underline">Klik untuk memilih file</span> atau tarik dan lepas berkas ke sini
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1">
                            Mendukung berkas format: <strong class="text-slate-600">JPG, PNG, WEBP, atau PDF</strong> (Maksimal 3 MB)
                        </p>
                    </div>
                </div>

                <!-- Tampilan Preview Berkas Baru Terpilih -->
                <div x-show="fileName" x-cloak class="py-2 space-y-3" @click.stop>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold mb-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        Berkas Baru Siap Diunggah
                    </div>

                    <!-- Image Preview -->
                    <template x-if="fileType === 'image' && previewUrl">
                        <div class="relative mx-auto max-w-xs rounded-xl overflow-hidden border border-slate-200 shadow-md bg-white p-2">
                            <img :src="previewUrl" alt="Pratinjau Maklumat" class="w-full max-h-56 object-contain rounded-lg">
                        </div>
                    </template>

                    <!-- PDF Preview -->
                    <template x-if="fileType === 'pdf'">
                        <div class="mx-auto max-w-sm rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex items-center gap-3 text-left">
                            <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName"></p>
                                <p class="text-[10px] text-slate-400" x-text="'Dokumen PDF • ' + fileSize"></p>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-center gap-2 pt-2">
                        <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">
                            <i class="fas fa-sync-alt mr-1"></i> Ganti File
                        </button>
                        <button type="button" @click="resetFileInput()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-lg transition">
                            <i class="fas fa-trash-alt mr-1"></i> Batalkan
                        </button>
                    </div>
                </div>

                <!-- Tampilan Berkas yang Sudah Tersimpan di Server -->
                <div x-show="!fileName && hasExisting" x-cloak class="py-2 space-y-3" @click.stop>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-[11px] font-bold mb-2">
                        <i class="fas fa-file-check text-slate-600"></i>
                        Berkas Piagam Resmi Saat Ini
                    </div>

                    <!-- Gambar Tersimpan -->
                    <template x-if="existingType === 'image' && existingUrl">
                        <div class="relative mx-auto max-w-xs rounded-xl overflow-hidden border border-slate-200 shadow-md bg-white p-2">
                            <img :src="existingUrl" alt="Piagam Maklumat Tersimpan" class="w-full max-h-56 object-contain rounded-lg">
                        </div>
                    </template>

                    <!-- PDF Tersimpan -->
                    <template x-if="existingType === 'pdf'">
                        <div class="mx-auto max-w-sm rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex items-center gap-3 text-left">
                            <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="existingName"></p>
                                <p class="text-[10px] text-slate-400" x-text="'Dokumen PDF Tersimpan • ' + existingSize"></p>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-center gap-2 pt-2">
                        <a :href="existingUrl" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition inline-flex items-center">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Berkas
                        </a>
                        <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold rounded-lg transition">
                            <i class="fas fa-sync-alt mr-1"></i> Ganti Berkas
                        </button>
                        <button type="button" @click="markDeleteExisting()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-lg transition">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Berkas
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">
                <i class="fas fa-info-circle mr-1"></i> Perubahan akan langsung disinkronkan ke website beranda warga.
            </span>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-md flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Simpan Maklumat & Berkas</span>
            </button>
        </div>
    </form>
    

</div>

<script>
    $(document).ready(function() {
        if (typeof window.initSimpelSummernote === 'function') {
            window.initSimpelSummernote('#maklumat_editor', {
                height: 220,
                placeholder: 'Ketik teks maklumat pelayanan di sini...'
            });
        }
    });
</script>
@endsection

