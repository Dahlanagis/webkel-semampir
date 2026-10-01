<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - Portal Kelurahan Semampir</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <!-- Rich Text Editor (Trix CDN untuk form editor konten) -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery 3.7.1 (Required for Summernote) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Summernote Lite (Modern Standalone Rich Text Editor) -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <script>
        window.initSimpelSummernote = function(selector, customOptions) {
            customOptions = customOptions || {};
            var $el = $(selector);
            if (!$el.length) return;

            var uploadUrl = "{{ route('admin.media.store') }}";
            var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            var defaultOptions = {
                placeholder: customOptions.placeholder || 'Ketik konten di sini (bisa sisipkan gambar/tabel)...',
                height: customOptions.height || 360,
                dialogsInBody: true,
                dialogsFade: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onInit: function() {
                        var $editor = $(this);
                        var $editable = $editor.next('.note-editor').find('.note-editable');
                        $editable.css({
                            'font-family': '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
                            'font-size': '14px',
                            'line-height': '1.65',
                            'color': '#1e293b'
                        });

                        // Bersihkan teks yang membawa inline style warna pudar/terang bekas copas dari website
                        $editable.find('*').each(function() {
                            var c = (this.style.color || '').toLowerCase();
                            if (c && (c.indexOf('203, 213, 225') !== -1 || c.indexOf('cbd5e1') !== -1 || c.indexOf('226, 232, 240') !== -1 || c.indexOf('e2e8f0') !== -1 || c.indexOf('248, 250, 252') !== -1 || c.indexOf('f8fafc') !== -1 || c.indexOf('255, 255, 255') !== -1 || c === '#fff' || c === '#ffffff')) {
                                this.style.removeProperty('color');
                            }
                            var bg = (this.style.backgroundColor || '').toLowerCase();
                            if (bg && (bg.indexOf('248, 250, 252') !== -1 || bg.indexOf('f8fafc') !== -1 || bg.indexOf('241, 245, 249') !== -1 || bg.indexOf('f1f5f9') !== -1)) {
                                this.style.removeProperty('background-color');
                            }
                        });

                        if (typeof customOptions.onInit === 'function') {
                            customOptions.onInit($editor);
                        }
                    },
                    onPaste: function(e) {
                        var clipboardEvent = e.originalEvent || e;
                        if (clipboardEvent.clipboardData && clipboardEvent.clipboardData.getData) {
                            var textHtml = clipboardEvent.clipboardData.getData('text/html');
                            if (textHtml) {
                                e.preventDefault();
                                try {
                                    var parser = new DOMParser();
                                    var doc = parser.parseFromString(textHtml, 'text/html');
                                    var elements = doc.body.querySelectorAll('*');
                                    elements.forEach(function(node) {
                                        // Hapus styling warna pudar/background dari clipboard
                                        node.style.removeProperty('color');
                                        node.style.removeProperty('background-color');
                                        node.style.removeProperty('background');
                                        node.style.removeProperty('font-family');
                                        if (!node.getAttribute('style') || node.getAttribute('style').trim() === '') {
                                            node.removeAttribute('style');
                                        }
                                    });
                                    var cleanHtml = doc.body.innerHTML;
                                    document.execCommand('insertHTML', false, cleanHtml);
                                } catch (err) {
                                    var textPlain = clipboardEvent.clipboardData.getData('text/plain');
                                    document.execCommand('insertText', false, textPlain);
                                }
                            }
                        }
                    },
                    onImageUpload: function(files) {
                        var $editor = $(this);
                        for (var i = 0; i < files.length; i++) {
                            uploadFileToMedia(files[i], $editor, 'image');
                        }
                    },
                    onDrop: function(e) {
                        var $editor = $(this);
                        var dataTransfer = e.originalEvent.dataTransfer;
                        if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
                            var files = dataTransfer.files;
                            for (var i = 0; i < files.length; i++) {
                                var file = files[i];
                                if (file.type.startsWith('image/')) {
                                    e.preventDefault();
                                    uploadFileToMedia(file, $editor, 'image');
                                } else if (file.type.startsWith('video/')) {
                                    e.preventDefault();
                                    uploadFileToMedia(file, $editor, 'video');
                                }
                            }
                        }
                    },
                    onChange: function(contents) {
                        $el.val(contents);
                        if ($el[0]) {
                            $el[0].dispatchEvent(new Event('input', { bubbles: true }));
                            $el[0].dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                }
            };

            function uploadFileToMedia(file, $editor, type) {
                var formData = new FormData();
                formData.append('file', file);
                formData.append('folder', type === 'video' ? 'konten_video' : 'konten_gambar');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Mengunggah ' + (type === 'video' ? 'Video' : 'Foto') + '...',
                        text: file.name + ' (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB)',
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });
                }

                fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(function(res) {
                    if (!res.ok) {
                        return res.json().then(function(err) {
                            throw new Error(err.message || 'Gagal mengunggah berkas.');
                        }).catch(function(e) {
                            throw new Error(e.message || ('HTTP Error ' + res.status));
                        });
                    }
                    return res.json();
                })
                .then(function(json) {
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }
                    if (json && json.location) {
                        if (type === 'video') {
                            var videoHtml = '<div class="my-3"><video controls style="max-width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"><source src="' + json.location + '">Browser Anda tidak mendukung pemutar video HTML5.</video></div><p><br></p>';
                            $editor.summernote('pasteHTML', videoHtml);
                        } else {
                            $editor.summernote('insertImage', json.location, function($image) {
                                $image.css({
                                    'max-width': '100%',
                                    'border-radius': '8px',
                                    'margin': '6px 0'
                                });
                            });
                        }
                    }
                })
                .catch(function(err) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengunggah',
                            text: err.message
                        });
                    } else {
                        alert('Gagal mengunggah: ' + err.message);
                    }
                });
            }

            var mergedOptions = $.extend(true, {}, defaultOptions, customOptions);
            var instance = $el.summernote(mergedOptions);

            // Enhance video dialog with direct local video file picker
            $el.on('summernote.dialog.shown', function() {
                var $dialog = $('.note-video-dialog');
                if ($dialog.length && !$dialog.find('.note-video-file-wrap').length) {
                    var uploadBtnHtml = '<div class="note-video-file-wrap mt-3 pt-3 border-t border-slate-200">' +
                        '<label class="block text-xs font-bold text-slate-700 mb-1.5"><i class="fas fa-file-video mr-1 text-emerald-600"></i>Atau Unggah Berkas Video (.mp4, .webm):</label>' +
                        '<input type="file" accept="video/mp4,video/webm,video/ogg" class="note-custom-video-file block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">' +
                        '</div>';
                    $dialog.find('.note-form-group:last').after(uploadBtnHtml);

                    $dialog.find('.note-custom-video-file').on('change', function() {
                        var file = this.files[0];
                        if (file) {
                            $dialog.modal('hide');
                            uploadFileToMedia(file, $el, 'video');
                        }
                    });
                }
            });

            return instance;
        };

        // Auto-initialize any .tinymce-editor or .summernote-editor on DOM ready
        $(document).ready(function() {
            $('.tinymce-editor, .summernote-editor').each(function() {
                if (!$(this).data('summernote-initialized')) {
                    $(this).data('summernote-initialized', true);
                    window.initSimpelSummernote(this, {
                        height: 250
                    });
                }
            });
        });

        // Safe Mock / Bridge for tinymce so any legacy code gracefully delegates to Summernote
        if (typeof window.tinymce === 'undefined') {
            window.tinymce = {
                init: function(options) {
                    options = options || {};
                    var target = options.target || options.selector;
                    if (target) {
                        $(document).ready(function() {
                            var height = options.height || 260;
                            window.initSimpelSummernote(target, { height: height });
                        });
                    }
                },
                triggerSave: function() {
                    if (window.jQuery && $.fn.summernote) {
                        $('textarea').each(function() {
                            if ($(this).next('.note-editor').length) {
                                $(this).val($(this).summernote('code'));
                            }
                        });
                    }
                },
                get: function() { return null; }
            };
        }
    </script>

    <!-- Custom Styling for Summernote matching screenshot -->
    <style>
        .note-editor.note-frame {
            border: 1.5px solid #34d399 !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            background: #ffffff !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }
        .note-editor.note-frame.focus,
        .note-editor.note-frame:focus-within {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.2) !important;
        }
        .note-editor.note-frame .note-toolbar {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 10px 12px 6px 12px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        .note-editor.note-frame .note-toolbar .note-btn-group {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            padding: 2px !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .note-editor.note-frame .note-toolbar .note-btn {
            background: transparent !important;
            border: none !important;
            color: #1e293b !important;
            padding: 5px 9px !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            border-radius: 6px !important;
            transition: background 0.15s ease, color 0.15s ease !important;
            box-shadow: none !important;
            height: auto !important;
        }
        .note-editor.note-frame .note-toolbar .note-btn:hover,
        .note-editor.note-frame .note-toolbar .note-btn.active {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .note-editor.note-frame .note-editable {
            padding: 16px !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-size: 14px !important;
            line-height: 1.65 !important;
            color: #1e293b !important;
            background: #ffffff !important;
            min-height: 260px !important;
        }
        .note-editor.note-frame .note-editable p,
        .note-editor.note-frame .note-editable span,
        .note-editor.note-frame .note-editable div {
            color: #1e293b;
        }
        .note-editor.note-frame .note-editable [style*="203, 213, 225"],
        .note-editor.note-frame .note-editable [style*="cbd5e1"],
        .note-editor.note-frame .note-editable [style*="226, 232, 240"],
        .note-editor.note-frame .note-editable [style*="e2e8f0"],
        .note-editor.note-frame .note-editable [style*="248, 250, 252"],
        .note-editor.note-frame .note-editable [style*="f8fafc"] {
            color: #1e293b !important;
            background-color: transparent !important;
        }
        .note-placeholder {
            color: #94a3b8 !important;
            font-size: 14px !important;
            padding: 16px !important;
        }
        .note-statusbar {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        .note-statusbar .note-resizebar {
            padding-top: 4px !important;
            height: 14px !important;
        }
        .note-statusbar .note-resizebar .note-icon-bar {
            width: 22px !important;
            margin: 1px auto !important;
            border-top: 2px solid #cbd5e1 !important;
        }
        .note-modal .modal-dialog {
            margin-top: 100px;
        }
        .note-modal-content {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .note-modal-header {
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 14px 18px !important;
        }
        .note-modal-footer {
            border-top: 1px solid #e2e8f0 !important;
            padding: 12px 18px !important;
        }
        .note-modal .note-btn {
            border-radius: 8px !important;
            padding: 6px 14px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }
        .note-dropdown-menu {
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            padding: 6px 0 !important;
            z-index: 1055 !important;
        }
    </style>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .admin-main-wrapper {
                padding-left: 18rem !important;
            }
        }
        /* Custom scrollbar sidebar */
        aside::-webkit-scrollbar { width: 5px; }
        aside::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="h-full font-sans text-slate-800 antialiased bg-slate-50" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen bg-slate-50 relative">
        
        <!-- Mobile Backdrop Overlay -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        <!-- ========================================== -->
        <!-- 1. SIDEBAR NAVIGATION                      -->
        <!-- ========================================== -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 lg:z-30 w-72 h-screen bg-white border-r border-slate-200 text-slate-800 flex flex-col justify-between shadow-lg lg:shadow-none transition-transform duration-300 ease-in-out overflow-y-auto">
            
            <div>
                <!-- Brand Header -->
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-xs z-10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center p-1.5 shadow-sm group-hover:scale-105 transition duration-200 shrink-0">
                            <img src="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10' }}" 
                                 alt="Logo Aplikasi" 
                                 class="w-full h-full object-contain drop-shadow">
                        </div>
                        <div class="min-w-0 flex flex-col justify-center">
                            <div class="font-extrabold text-sm sm:text-base tracking-tight text-slate-900 leading-none truncate">
                                {{ strtoupper($systemSettings['app_name'] ?? 'SIMPEL KELURAHAN') }}
                            </div>
                            <div class="text-[9px] sm:text-[10px] text-slate-600/90 font-medium tracking-wide uppercase mt-1 truncate">
                                {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}
                            </div>
                        </div>
                    </a>

                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-lg focus:outline-none hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Nav Menu -->
                <nav class="px-3.5 py-4 space-y-4">

                    <!-- MENU UTAMA: DASHBOARD -->
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                                <i class="fas fa-chart-pie text-xs"></i>
                            </div>
                            <span class="tracking-wide">Dashboard Utama</span>
                        </a>
                    </div>

                    <!-- GRUP 1: PROFIL & DATA WILAYAH -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.beranda.identitas_sambutan') || request()->routeIs('admin.beranda.visi_misi_sejarah') || request()->routeIs('admin.beranda.sotk') || request()->routeIs('admin.beranda.sejarah') || request()->routeIs('admin.beranda.transparansi') || request()->routeIs('admin.beranda.statistik') || request()->routeIs('admin.beranda.statistik_wilayah') || request()->routeIs('admin.kelembagaan.*') || request()->routeIs('admin.beranda.kemitraan') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" 
                                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-landmark text-[11px] text-emerald-600"></i>
                                <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-600">Profil Kelurahan</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 pl-2 border-l-2 border-slate-100 ml-3.5 my-1">
                            <a href="{{ route('admin.beranda.identitas_sambutan') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.identitas_sambutan') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-id-card text-[11px] {{ request()->routeIs('admin.beranda.identitas_sambutan') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Profil & Sambutan</span>
                            </a>

                            <a href="{{ route('admin.beranda.visi_misi_sejarah') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.visi_misi_sejarah') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-bullseye text-[11px] {{ request()->routeIs('admin.beranda.visi_misi_sejarah') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Visi & Misi Kelurahan</span>
                            </a>

                            <a href="{{ route('admin.beranda.sotk') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.sotk') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-sitemap text-[11px] {{ request()->routeIs('admin.beranda.sotk') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Struktur Organisasi (SOTK)</span>
                            </a>

                            <a href="{{ route('admin.beranda.sejarah') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.sejarah') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-scroll text-[11px] {{ request()->routeIs('admin.beranda.sejarah') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Sejarah & Asal Usul</span>
                            </a>

                            <a href="{{ route('admin.kelembagaan.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.kelembagaan.*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-people-roof text-[11px] {{ request()->routeIs('admin.kelembagaan.*') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Lembaga Kemasyarakatan (LKD)</span>
                            </a>

                            <a href="{{ route('admin.beranda.statistik_wilayah') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.statistik_wilayah') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-chart-line text-[11px] {{ request()->routeIs('admin.beranda.statistik_wilayah') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Statistik Wilayah</span>
                            </a>

                            <a href="{{ route('admin.beranda.statistik') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.statistik') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-users text-[11px] {{ request()->routeIs('admin.beranda.statistik') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Data Penduduk & Demografi</span>
                            </a>

                            <a href="{{ route('admin.beranda.kemitraan') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.kemitraan') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-link text-[11px] {{ request()->routeIs('admin.beranda.kemitraan') ? 'text-emerald-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Link Terkait</span>
                            </a>
                        </div>
                    </div>

                    <!-- GRUP 2: LAYANAN WARGA -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.jenis-layanan.*') || request()->routeIs('admin.layanan-publik.*') || request()->routeIs('admin.documents.*') || request()->routeIs('admin.beranda.maklumat') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" 
                                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-concierge-bell text-[11px] text-blue-600"></i>
                                <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-600">Layanan Warga</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 pl-2 border-l-2 border-slate-100 ml-3.5 my-1">
                            <a href="{{ route('admin.jenis-layanan.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.jenis-layanan.*') || request()->routeIs('admin.layanan-publik.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-clipboard-check text-[11px] {{ request()->routeIs('admin.jenis-layanan.*') || request()->routeIs('admin.layanan-publik.*') ? 'text-blue-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Daftar Layanan</span>
                            </a>

                            <a href="{{ route('admin.documents.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.documents.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-folder-open text-[11px] {{ request()->routeIs('admin.documents.*') ? 'text-blue-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Dokumen Publik</span>
                            </a>

                            <a href="{{ route('admin.beranda.maklumat') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.maklumat') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-certificate text-[11px] {{ request()->routeIs('admin.beranda.maklumat') ? 'text-blue-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Maklumat Layanan</span>
                            </a>
                        </div>
                    </div>

                    <!-- GRUP 3: KABAR & PUBLIKASI -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.berita.*') || request()->routeIs('admin.pengumuman.*') || request()->routeIs('admin.agenda.*') || request()->routeIs('admin.beranda.transparansi') || request()->routeIs('admin.galeri.*') || request()->routeIs('admin.pages.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" 
                                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-newspaper text-[11px] text-amber-600"></i>
                                <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-600">Publikasi</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 pl-2 border-l-2 border-slate-100 ml-3.5 my-1">
                            <a href="{{ route('admin.berita.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.berita.*') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-bullhorn text-[11px] {{ request()->routeIs('admin.berita.*') ? 'text-amber-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Berita & Artikel</span>
                            </a>

                            <a href="{{ route('admin.pengumuman.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.pengumuman.*') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-scroll text-[11px] {{ request()->routeIs('admin.pengumuman.*') ? 'text-amber-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Pengumuman</span>
                            </a>

                            <a href="{{ route('admin.agenda.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.agenda.*') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-calendar-alt text-[11px] {{ request()->routeIs('admin.agenda.*') ? 'text-amber-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Agenda Kegiatan</span>
                            </a>

                            <a href="{{ route('admin.beranda.transparansi') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.transparansi') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-coins text-[11px] {{ request()->routeIs('admin.beranda.transparansi') ? 'text-amber-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>APBDes & Transparansi</span>
                            </a>

                            <a href="{{ route('admin.galeri.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.galeri.*') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-images text-[11px] {{ request()->routeIs('admin.galeri.*') ? 'text-amber-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Galeri Kegiatan</span>
                            </a>

                            <a href="{{ route('admin.pages.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.pages.*') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-file-lines text-[11px] {{ request()->routeIs('admin.pages.*') ? 'text-amber-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Halaman Menu</span>
                            </a>
                        </div>
                    </div>

                    <!-- GRUP 4: PENGELOLAAN WEBSITE & BERANDA -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.beranda.banner') || request()->routeIs('admin.navigation.*') || request()->routeIs('admin.beranda.kontak') || request()->routeIs('admin.beranda.lokasi') || request()->routeIs('admin.beranda.footer') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" 
                                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-desktop text-[11px] text-indigo-600"></i>
                                <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-600">Tampilan Depan</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 pl-2 border-l-2 border-slate-100 ml-3.5 my-1">
                            <a href="{{ route('admin.beranda.banner') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.banner') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-image text-[11px] {{ request()->routeIs('admin.beranda.banner') ? 'text-indigo-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Banner Utama</span>
                            </a>

                            <a href="{{ route('admin.navigation.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.navigation.*') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-compass text-[11px] {{ request()->routeIs('admin.navigation.*') ? 'text-indigo-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Menu Navigasi</span>
                            </a>

                            <a href="{{ route('admin.beranda.kontak') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.kontak') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-headset text-[11px] {{ request()->routeIs('admin.beranda.kontak') ? 'text-indigo-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Kontak Kantor</span>
                            </a>

                            <a href="{{ route('admin.beranda.lokasi') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.lokasi') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-location-dot text-[11px] {{ request()->routeIs('admin.beranda.lokasi') ? 'text-indigo-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Peta Lokasi</span>
                            </a>

                            <a href="{{ route('admin.beranda.footer') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.beranda.footer') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/60' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-share-nodes text-[11px] {{ request()->routeIs('admin.beranda.footer') ? 'text-indigo-600' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Footer & Sosmed</span>
                            </a>
                        </div>
                    </div>

                    <!-- GRUP 5: PENGATURAN & SISTEM -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.kategori.*') || request()->routeIs('admin.media.*') || request()->routeIs('admin.activity-log.*') || request()->routeIs('admin.operator.*') || request()->routeIs('admin.pengaturan.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" 
                                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-sliders text-[11px] text-slate-700"></i>
                                <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-600">Pengaturan Sistem</span>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 pl-2 border-l-2 border-slate-100 ml-3.5 my-1">
                            <a href="{{ route('admin.pengaturan.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.pengaturan.*') ? 'bg-slate-100 text-slate-900 font-bold border border-slate-300/80' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-gear text-[11px] {{ request()->routeIs('admin.pengaturan.*') ? 'text-slate-800' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Pengaturan Web</span>
                            </a>

                            <a href="{{ route('admin.operator.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.operator.*') ? 'bg-slate-100 text-slate-900 font-bold border border-slate-300/80' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-users-gear text-[11px] {{ request()->routeIs('admin.operator.*') ? 'text-slate-800' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Akun Pengguna</span>
                            </a>

                            <a href="{{ route('admin.kategori.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.kategori.*') ? 'bg-slate-100 text-slate-900 font-bold border border-slate-300/80' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-tags text-[11px] {{ request()->routeIs('admin.kategori.*') ? 'text-slate-800' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Kategori</span>
                            </a>

                            <a href="{{ route('admin.media.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.media.*') ? 'bg-slate-100 text-slate-900 font-bold border border-slate-300/80' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-photo-film text-[11px] {{ request()->routeIs('admin.media.*') ? 'text-slate-800' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>File & Media</span>
                            </a>

                            <a href="{{ route('admin.activity-log.index') }}" 
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-xs transition {{ request()->routeIs('admin.activity-log.*') ? 'bg-slate-100 text-slate-900 font-bold border border-slate-300/80' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="fas fa-clock-rotate-left text-[11px] {{ request()->routeIs('admin.activity-log.*') ? 'text-slate-800' : 'text-slate-400' }} w-4 text-center"></i>
                                <span>Log Aktivitas</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Footer Profile & Logout -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/80 shrink-0">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-600 text-white font-black flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 truncate">
                                {{ Auth::user()->name ?? 'Admin Kelurahan' }}
                            </div>
                            <div class="text-[10px] text-slate-500 truncate">
                                {{ Auth::user()->email ?? 'admin@kelurahan.go.id' }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                title="Keluar Akun"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition focus:outline-none">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- 2. MAIN CONTENT AREA                       -->
        <!-- ========================================== -->
        <div class="admin-main-wrapper lg:pl-72 flex flex-col min-h-screen">
            
            <!-- Topbar Sticky -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-xs">
                <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3 sm:gap-4">
                    
                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-sm sm:text-base md:text-lg font-bold text-slate-900 tracking-tight truncate">
                                @yield('header-title', 'Dashboard Panel Admin')
                            </h1>
                            <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden xs:block sm:block">
                                @yield('header-subtitle', 'Sistem Informasi Manajemen Pelayanan Kelurahan Semampir')
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 sm:gap-4 shrink-0">
                        <!-- Jam & Tanggal Realtime -->
                        <div class="hidden md:flex flex-col items-end text-right border-r border-slate-200 pr-3.5">
                            <div class="text-xs font-bold text-slate-900 whitespace-nowrap" id="live-date">
                                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                            </div>
                            <div class="text-[11px] font-mono text-slate-500 font-semibold" id="live-clock">
                                {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB
                            </div>
                        </div>

                        <!-- Tombol Pratinjau Website Publik -->
                        <a href="{{ route('home') }}" 
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg border border-slate-200 transition shadow-xs whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span class="hidden sm:inline">Lihat Website</span>
                            <span class="sm:hidden">Web</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Alert & Flash Messages -->
            <main class="flex-1 p-3.5 sm:p-6 lg:p-8">
                @if(session('status') || session('success'))
                    <div class="mb-5 bg-emerald-50/90 border border-emerald-200 text-emerald-950 p-3.5 sm:p-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs sm:text-sm font-bold text-emerald-900 block leading-tight">Data Berhasil Diperbarui</span>
                                <span class="text-[11px] sm:text-xs text-emerald-700 font-medium">{{ session('status') ?? session('success') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-900 p-3.5 sm:p-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-900 p-3.5 sm:p-4 rounded-2xl shadow-xs">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-bold">Terjadi kesalahan pada data yang dikirim:</span>
                        </div>
                        <ul class="list-disc list-inside text-[11px] sm:text-xs font-medium space-y-1 ml-10 sm:ml-11">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer Kelurahan -->
            <footer class="bg-white border-t border-slate-200 px-4 sm:px-6 py-3.5 text-center text-[11px] sm:text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <div>
                    &copy; {{ date('Y') }} <strong>{{ $villageProfile['village_name'] ?? 'Pemerintah Kelurahan Semampir' }}</strong>
                </div>
                <div class="text-[10px] sm:text-[11px] text-slate-400">
                    {{ $systemSettings['app_name'] ?? 'Portal Kelurahan Semampir' }} &bull; Panel Admin
                </div>
            </footer>

        </div>
    </div>

    <!-- Global Image Cropper Modal -->
    <div x-data="imageCropper()" 
         @open-cropper.window="openCropper($event.detail, $event.target)"
         x-show="isOpen" 
         x-cloak 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         role="dialog" aria-modal="true">
        
        <div class="flex items-center justify-center min-h-screen p-3 text-center sm:p-6">
            <div x-show="isOpen" @click="cancel()" class="fixed inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity"></div>

            <div x-show="isOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-3xl my-6 border border-slate-200">
                <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold flex items-center gap-2">
                            <i class="fas fa-crop-simple text-emerald-400"></i> Sesuaikan Gambar / Logo (Crop)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Atur kotak pemotong atau klik <strong>"Pilih Seluruh Logo"</strong> agar logo tampak utuh tanpa terpotong.</p>
                    </div>
                    <button type="button" @click="cancel()" class="text-slate-400 hover:text-white p-1 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 sm:p-5 space-y-3.5">
                    <!-- Control Bar: Ratios & Tools -->
                    <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-200 text-xs">
                        <div class="flex items-center flex-wrap gap-1.5">
                            <span class="text-slate-500 font-bold mr-1 text-[11px] uppercase tracking-wider">Bentuk:</span>
                            <button type="button" @click="setRatio(NaN)" class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5" :class="isNaN(currentRatio) ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                <i class="fas fa-expand text-[10px]"></i> Bebas (Sesuai Gambar)
                            </button>
                            <button type="button" @click="setRatio(1)" class="px-2.5 py-1.5 rounded-xl border text-xs font-bold transition" :class="currentRatio === 1 ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">1:1 Persegi</button>
                            <button type="button" @click="setRatio(3/4)" class="px-2.5 py-1.5 rounded-xl border text-xs font-bold transition" :class="currentRatio === 3/4 ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">3:4 Tegak</button>
                            <button type="button" @click="setRatio(16/9)" class="px-2.5 py-1.5 rounded-xl border text-xs font-bold transition" :class="currentRatio === 16/9 ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">16:9 Lebar</button>
                        </div>
                        
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="fitFull()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-sm flex items-center gap-1.5 transition text-xs" title="Tampilkan seluruh gambar/logo tanpa terpotong">
                                <i class="fas fa-check-double text-[11px]"></i> Pilih Seluruh Logo
                            </button>
                            <button type="button" @click="zoom(0.1)" class="w-8 h-8 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl flex items-center justify-center font-bold shadow-sm transition" title="Perbesar (Zoom In)"><i class="fas fa-search-plus"></i></button>
                            <button type="button" @click="zoom(-0.1)" class="w-8 h-8 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl flex items-center justify-center font-bold shadow-sm transition" title="Perkecil (Zoom Out)"><i class="fas fa-search-minus"></i></button>
                            <button type="button" @click="rotate(90)" class="w-8 h-8 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl flex items-center justify-center font-bold shadow-sm transition" title="Putar 90°"><i class="fas fa-rotate-right"></i></button>
                            <button type="button" @click="resetCrop()" class="w-8 h-8 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl flex items-center justify-center font-bold shadow-sm transition" title="Reset"><i class="fas fa-arrows-rotate"></i></button>
                        </div>
                    </div>

                    <!-- Cropper Canvas Container -->
                    <div class="w-full bg-slate-900 rounded-2xl overflow-hidden flex items-center justify-center relative p-1" style="height: 52vh; max-height: 480px;">
                        <img x-ref="image" src="" alt="Source Image" class="max-w-full max-h-full block">
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500 px-1">
                        <span class="flex items-center gap-1.5"><i class="fas fa-info-circle text-sky-500"></i>Geser titik sudut kotak pemotong untuk memperluas area.</span>
                        <button type="button" @click="fitFull()" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline flex items-center gap-1">
                            <i class="fas fa-arrows-alt text-[10px]"></i> Klik di sini agar logo kelihatan semua &rarr;
                        </button>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-200">
                    <button type="button" @click="useOriginal()" class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3.5 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5 w-full sm:w-auto justify-center">
                        <i class="fas fa-image text-emerald-600"></i> Gunakan Gambar Asli (Tanpa Potong)
                    </button>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <button type="button" @click="cancel()" class="px-4 py-2.5 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition text-xs">Batal</button>
                        <button type="button" @click="crop()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition text-xs flex items-center gap-1.5">
                            <i class="fas fa-check"></i> Terapkan Potongan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts: Live Clock & File Validator -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById('live-clock');
            if (clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);

        // Global Modern SweetAlert2 Rejected Modal matching website colors
        window.showFileRejectedAlert = function(fileName, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    iconColor: '#e11d48',
                    title: '<span class="text-xl font-black text-slate-800 tracking-tight">File Ditolak!</span>',
                    html: `
                        <div class="text-xs text-slate-600 mt-2 space-y-2.5">
                            <p class="font-semibold text-rose-600 leading-relaxed text-xs sm:text-sm">${message || 'Format atau ukuran file tidak sesuai dengan ketentuan sistem.'}</p>
                            ${fileName ? `<div class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-800 font-mono text-[11px] px-3 py-1.5 rounded-xl border border-rose-200 mt-1 max-w-full"><i class="fas fa-file-circle-xmark text-rose-500"></i><span class="truncate">File: <strong>${fileName}</strong></span></div>` : ''}
                        </div>
                    `,
                    showCloseButton: true,
                    confirmButtonText: '<i class="fas fa-redo-alt mr-1.5"></i> Pilih File Lain',
                    confirmButtonColor: '#0f172a',
                    customClass: {
                        popup: 'rounded-3xl border border-slate-100 shadow-2xl p-6 font-sans',
                        confirmButton: 'rounded-xl font-bold text-xs px-5 py-2.5 shadow-md hover:bg-slate-800 transition'
                    }
                });
            } else {
                alert('File Ditolak!\n' + (message || 'Format atau ukuran file tidak sesuai.') + (fileName ? '\nFile: ' + fileName : ''));
            }
        };

        // Universal Real-time File Validator across all Admin File Inputs
        document.addEventListener('change', function(e) {
            const input = e.target;
            if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;
            const files = input.files;
            if (!files || files.length === 0) return;

            const accept = (input.getAttribute('accept') || '').toLowerCase().trim();
            const isPdfOnly = accept.includes('pdf') && !accept.includes('image') && !accept.includes('video');
            const isVideoOnly = accept.includes('video') && !accept.includes('image') && !accept.includes('pdf');
            const isImageOnly = (accept.includes('image') || accept.includes('.jpg') || accept.includes('.png') || accept.includes('.webp')) && !accept.includes('pdf') && !accept.includes('video');

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                const mime = (file.type || '').toLowerCase();

                // 1. PDF Only Check
                if (isPdfOnly) {
                    const isPdf = mime === 'application/pdf' || ext === 'pdf';
                    if (!isPdf) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        window.showFileRejectedAlert(file.name, 'Format file tidak sesuai! Hanya dokumen berformat PDF (.pdf) yang diperbolehkan.');
                        return;
                    }
                    if (file.size > 10 * 1024 * 1024) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const actualMb = (file.size / (1024 * 1024)).toFixed(2);
                        window.showFileRejectedAlert(file.name, `Ukuran file PDF (${actualMb} MB) melebihi batas maksimal 10 MB.`);
                        return;
                    }
                }
                // 2. Image Only Check
                else if (isImageOnly) {
                    const allowedImgExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
                    const isImage = mime.startsWith('image/') || allowedImgExts.includes(ext);
                    if (!isImage) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        window.showFileRejectedAlert(file.name, 'Format file tidak sesuai! Hanya file gambar (JPG, JPEG, PNG, atau WEBP) yang diperbolehkan.');
                        return;
                    }
                    if (file.size > 5 * 1024 * 1024) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const actualMb = (file.size / (1024 * 1024)).toFixed(2);
                        window.showFileRejectedAlert(file.name, `Ukuran file gambar (${actualMb} MB) melebihi kapasitas maksimal 5 MB.`);
                        return;
                    }
                }
                // 3. Video Only Check
                else if (isVideoOnly) {
                    const allowedVideoExts = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
                    const isVideo = mime.startsWith('video/') || allowedVideoExts.includes(ext);
                    if (!isVideo) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        window.showFileRejectedAlert(file.name, 'Format file tidak sesuai! Hanya berkas video (.mp4, .webm, .ogg) yang diperbolehkan.');
                        return;
                    }
                    if (file.size > 50 * 1024 * 1024) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const actualMb = (file.size / (1024 * 1024)).toFixed(2);
                        window.showFileRejectedAlert(file.name, `Ukuran file video (${actualMb} MB) melebihi batas maksimal 50 MB.`);
                        return;
                    }
                }
                // 4. General / Custom Check
                else {
                    if (accept && accept.length > 0) {
                        const acceptedList = accept.split(',').map(s => s.trim());
                        let matches = false;
                        for (let rule of acceptedList) {
                            if (rule.startsWith('.')) {
                                if ('.' + ext === rule) { matches = true; break; }
                            } else if (rule.endsWith('/*')) {
                                const prefix = rule.slice(0, -1);
                                if (mime.startsWith(prefix)) { matches = true; break; }
                            } else if (rule === mime) {
                                matches = true; break;
                            }
                        }
                        if (!matches) {
                            input.value = '';
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            window.showFileRejectedAlert(file.name, `Format file tidak sesuai dengan yang diizinkan sistem.`);
                            return;
                        }
                    }
                    if (file.size > 10 * 1024 * 1024) {
                        input.value = '';
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const actualMb = (file.size / (1024 * 1024)).toFixed(2);
                        window.showFileRejectedAlert(file.name, `Ukuran file (${actualMb} MB) melebihi batas maksimal 10 MB.`);
                        return;
                    }
                }
            }
        }, true);

        function validateFileInput(input, maxMb = 3) {
            const file = input.files[0];
            const container = input.closest('div');
            if (!container) return;

            let feedback = container.querySelector('.js-file-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'js-file-feedback mt-1.5 text-xs font-semibold';
                container.appendChild(feedback);
            }

            if (!file) {
                feedback.innerHTML = '';
                input.classList.remove('border-rose-500', 'bg-rose-50', 'border-slate-500', 'bg-slate-50');
                return;
            }

            const fileName = file.name;
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const fileExt = fileName.split('.').pop().toLowerCase();
            const validExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            if (!validExts.includes(fileExt)) {
                input.value = '';
                input.classList.add('border-rose-500', 'bg-rose-50');
                feedback.className = 'js-file-feedback mt-1.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-2';
                feedback.innerHTML = `Format <strong>.${fileExt}</strong> tidak didukung! Gunakan gambar JPG, PNG, WEBP atau PDF.`;
                window.showFileRejectedAlert(fileName, `Format file tidak didukung! Hanya file gambar (JPG, PNG, WEBP) atau PDF yang diperbolehkan.`);
                return false;
            }

            if (file.size > maxMb * 1024 * 1024) {
                input.value = '';
                input.classList.add('border-rose-500', 'bg-rose-50');
                feedback.className = 'js-file-feedback mt-1.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-2';
                feedback.innerHTML = `Ukuran file terlalu besar (<strong>${fileSizeMB} MB</strong>). Maksimal ${maxMb} MB.`;
                window.showFileRejectedAlert(fileName, `Ukuran file (${fileSizeMB} MB) melebihi kapasitas maksimal ${maxMb} MB.`);
                return false;
            }

            input.classList.remove('border-rose-500', 'bg-rose-50');
            input.classList.add('border-slate-500', 'bg-slate-50/50');
            feedback.className = 'js-file-feedback mt-1.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-[11px] font-semibold flex items-center gap-2';
            feedback.innerHTML = `File siap diunggah (${fileSizeMB} MB): <strong>${fileName}</strong>`;
            return true;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('imageCropper', () => ({
                isOpen: false,
                cropper: null,
                file: null,
                onCropCallback: null,
                currentRatio: NaN,

                openCropper(detail, target) {
                    this.file = detail.file;
                    
                    if (!this.file || !this.file.type.startsWith('image/')) {
                        const fileName = this.file ? this.file.name : '';
                        window.showFileRejectedAlert(fileName, 'Format file tidak sesuai! Mohon pilih file gambar (JPG, PNG, atau WEBP).');
                        if (target && target.tagName === 'INPUT' && target.type === 'file') {
                            target.value = '';
                        }
                        return;
                    }

                    this.onCropCallback = detail.onCrop;
                    const initialRatio = (typeof detail.aspectRatio !== 'undefined' && detail.aspectRatio !== null && !isNaN(detail.aspectRatio)) ? detail.aspectRatio : NaN;
                    this.currentRatio = initialRatio;
                    
                    const url = URL.createObjectURL(this.file);
                    this.$refs.image.src = url;
                    
                    this.isOpen = true;
                    
                    this.$nextTick(() => {
                        if(this.cropper) {
                            this.cropper.destroy();
                        }
                        this.cropper = new Cropper(this.$refs.image, {
                            aspectRatio: this.currentRatio,
                            viewMode: 1,
                            autoCropArea: 1,
                            background: false,
                            responsive: true,
                            restore: false,
                            checkCrossOrigin: false,
                            ready: () => {
                                if (isNaN(this.currentRatio)) {
                                    this.fitFull();
                                }
                            }
                        });
                    });
                },

                setRatio(ratio) {
                    this.currentRatio = ratio;
                    if(this.cropper) {
                        this.cropper.setAspectRatio(ratio);
                        if(isNaN(ratio)) {
                            this.fitFull();
                        }
                    }
                },

                fitFull() {
                    if(!this.cropper) return;
                    this.currentRatio = NaN;
                    this.cropper.setAspectRatio(NaN);
                    this.cropper.clear();
                    this.cropper.crop();
                    const canvasData = this.cropper.getCanvasData();
                    this.cropper.setCropBoxData({
                        left: canvasData.left,
                        top: canvasData.top,
                        width: canvasData.width,
                        height: canvasData.height
                    });
                },

                zoom(ratio) {
                    if(this.cropper) this.cropper.zoom(ratio);
                },

                rotate(degree) {
                    if(this.cropper) this.cropper.rotate(degree);
                },

                resetCrop() {
                    if(this.cropper) {
                        this.cropper.reset();
                        if(isNaN(this.currentRatio)) {
                            this.fitFull();
                        }
                    }
                },

                useOriginal() {
                    if(this.onCropCallback && this.file) {
                        const originalUrl = URL.createObjectURL(this.file);
                        this.onCropCallback(this.file, originalUrl);
                    }
                    this.cancel();
                },

                cancel() {
                    this.isOpen = false;
                    if(this.cropper) {
                        this.cropper.destroy();
                        this.cropper = null;
                    }
                    this.$refs.image.src = '';
                },

                crop() {
                    if(!this.cropper) return;
                    
                    this.cropper.getCroppedCanvas({
                        maxWidth: 1920,
                        maxHeight: 1920,
                        imageSmoothingQuality: 'high'
                    }).toBlob((blob) => {
                        const croppedUrl = URL.createObjectURL(blob);
                        if(this.onCropCallback) {
                            this.onCropCallback(blob, croppedUrl);
                        }
                        this.cancel();
                    }, this.file.type || 'image/jpeg', 0.92);
                }
            }));
        });
    </script>

    <!-- Custom Modern Modal Styling & Confirm Interceptor matching Website Brand -->
    <style>
        /* Modern Emerald Cropper Styling */
        .cropper-view-box {
            outline: 2.5px solid #10b981 !important;
            outline-color: rgba(16, 185, 129, 0.95) !important;
            border-radius: 4px !important;
        }
        .cropper-line {
            background-color: #10b981 !important;
        }
        .cropper-point {
            background-color: #10b981 !important;
            width: 9px !important;
            height: 9px !important;
            border-radius: 3px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        }
        .cropper-point.point-se {
            width: 12px !important;
            height: 12px !important;
        }

        div:where(.swal2-container) {
            z-index: 99999 !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            background: rgba(15, 23, 42, 0.65) !important;
        }
        div:where(.swal2-container) {
            z-index: 99999 !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            background: rgba(15, 23, 42, 0.55) !important;
        }
        div:where(.swal2-popup) {
            border-radius: 24px !important;
            overflow: hidden !important;
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
            padding: 28px 24px 24px 24px !important;
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
            max-width: 360px !important;
            width: 90% !important;
            position: relative !important;
        }
        div:where(.swal2-popup)::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3.5px;
            background: linear-gradient(90deg, #10b981 0%, #34d399 50%, #059669 100%);
            border-radius: 24px 24px 0 0;
        }

        /* Modern Refined Centered Alert Modal */
        @keyframes swalPulseRing {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4), 0 4px 16px rgba(16, 185, 129, 0.2);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 7px rgba(16, 185, 129, 0), 0 8px 24px rgba(16, 185, 129, 0.28);
                transform: scale(1.03);
            }
        }
        @keyframes swalCheckIn {
            0% {
                opacity: 0;
                transform: scale(0.5) rotate(-15deg);
            }
            60% {
                transform: scale(1.2) rotate(4deg);
            }
            100% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .swal-modal-icon-wrap {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
            position: relative;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .swal-modal-icon-wrap.success {
            background: #ecfdf5;
            border: 4px solid #d1fae5;
            animation: swalPulseRing 2.6s ease-in-out infinite;
        }
        .swal-modal-icon-wrap.warning {
            background: #fffbeb;
            border: 4px solid #fef3c7;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.2);
        }
        .swal-modal-icon-wrap.error {
            background: #fff1f2;
            border: 4px solid #ffe4e6;
            box-shadow: 0 4px 16px rgba(244, 63, 94, 0.2);
        }
        .swal-modal-icon-wrap.info {
            background: #f0f9ff;
            border: 4px solid #e0f2fe;
            box-shadow: 0 4px 16px rgba(14, 165, 233, 0.2);
        }
        .swal-modal-icon-inner {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.2rem;
        }
        .swal-modal-icon-inner i {
            display: inline-block;
            animation: swalCheckIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
        }
        .swal-modal-icon-wrap.success .swal-modal-icon-inner {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.38);
        }
        .swal-modal-icon-wrap.warning .swal-modal-icon-inner {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.38);
        }
        .swal-modal-icon-wrap.error .swal-modal-icon-inner {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.38);
        }
        .swal-modal-icon-wrap.info .swal-modal-icon-inner {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.38);
        }

        /* Micro Kicker Badge */
        .swal-kicker-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 11px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0 auto 6px auto;
        }
        .swal-kicker-badge.success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }
        .swal-kicker-badge.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }
        .swal-kicker-badge.error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
        }
        .swal-kicker-badge.info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
        }
        .swal-kicker-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        .swal-kicker-badge.success .swal-kicker-dot {
            background: #10b981;
            box-shadow: 0 0 6px #10b981;
        }
        .swal-kicker-badge.warning .swal-kicker-dot {
            background: #f59e0b;
            box-shadow: 0 0 6px #f59e0b;
        }
        .swal-kicker-badge.error .swal-kicker-dot {
            background: #f43f5e;
            box-shadow: 0 0 6px #f43f5e;
        }
        .swal-kicker-badge.info .swal-kicker-dot {
            background: #0ea5e9;
            box-shadow: 0 0 6px #0ea5e9;
        }

        .swal-simple-title {
            font-size: 1.2rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            text-align: center !important;
            margin-bottom: 8px !important;
            letter-spacing: -0.025em !important;
        }
        .swal-simple-desc {
            font-size: 0.85rem !important;
            color: #475569 !important;
            line-height: 1.55 !important;
            text-align: center !important;
            margin: 0 auto !important;
            max-width: 300px !important;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            padding: 8px 14px;
            border-radius: 12px;
        }
        .swal-simple-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            padding: 10px 32px !important;
            border-radius: 12px !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            min-width: 130px !important;
            margin-top: 18px !important;
            letter-spacing: 0.01em !important;
        }
        .swal-simple-btn:hover {
            transform: translateY(-1.5px) !important;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
            filter: brightness(1.05) !important;
        }
        .swal-simple-btn:active {
            transform: translateY(0) scale(0.97) !important;
        }
        .swal-simple-btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35) !important;
        }
        .swal-simple-btn-error {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important;
            box-shadow: 0 4px 14px rgba(244, 63, 94, 0.35) !important;
        }
        .swal-simple-btn-info {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.35) !important;
        }
        .swal-brand-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: #0f172a;
            color: #f43f5e;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px auto;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .swal-brand-icon-wrap svg {
            width: 28px;
            height: 28px;
        }
        .swal-brand-title {
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 4px !important;
            text-align: center !important;
        }
        .swal-brand-desc {
            font-size: 0.85rem !important;
            color: #64748b !important;
            line-height: 1.5 !important;
            text-align: center !important;
            margin-bottom: 8px !important;
        }
        .swal-brand-item-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #0f172a;
            font-weight: 800;
            font-size: 0.875rem;
            margin: 12px auto 8px auto;
            max-width: 360px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .swal-brand-item-pill svg {
            width: 16px;
            height: 16px;
            color: #e11d48;
            flex-shrink: 0;
        }
        .swal-brand-warning {
            font-size: 0.725rem;
            font-weight: 600;
            color: #e11d48;
            text-align: center;
            margin-top: 8px;
        }
        div:where(.swal2-actions) {
            width: 100% !important;
            display: flex !important;
            flex-direction: row-reverse !important;
            justify-content: center !important;
            gap: 12px !important;
            margin-top: 14px !important;
            padding-top: 0 !important;
            border-top: none !important;
        }
        .swal-delete-actions {
            margin-top: 18px !important;
            padding-top: 14px !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        .swal-brand-btn-confirm {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
            color: #ffffff !important;
            font-size: 0.825rem !important;
            font-weight: 700 !important;
            padding: 9px 20px !important;
            border-radius: 10px !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35) !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            min-width: 120px !important;
        }
        .swal-brand-btn-confirm:hover {
            transform: translateY(-1px) !important;
            filter: brightness(1.06) !important;
        }
        .swal-brand-btn-confirm:active {
            transform: scale(0.97) !important;
        }
        .swal-brand-btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            padding: 11px 32px !important;
            border-radius: 14px !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            text-decoration: none !important;
            min-width: 140px !important;
        }
        .swal-brand-btn-success:hover {
            transform: translateY(-1px) scale(1.02) !important;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
            filter: brightness(1.06) !important;
        }
        .swal-brand-btn-success:active {
            transform: translateY(0) scale(0.97) !important;
        }
        .swal-brand-btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: #ffffff !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            padding: 11px 32px !important;
            border-radius: 14px !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.4) !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            min-width: 140px !important;
        }
        .swal-brand-btn-warning:hover {
            transform: translateY(-1px) scale(1.02) !important;
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.5) !important;
        }
        .swal-brand-btn-cancel {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 0.825rem !important;
            font-weight: 700 !important;
            padding: 10px 20px !important;
            border-radius: 12px !important;
            border: 1px solid #cbd5e1 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
            min-width: 100px !important;
        }
        .swal-brand-btn-cancel:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
        .swal-brand-btn-cancel:active {
            transform: scale(0.97) !important;
        }
    </style>

    <script>
        // Modern Confirmation Dialog for Admin
        window.confirmDelete = function(target, itemName, customText) {
            var form = null;
            if (target instanceof HTMLFormElement) {
                form = target;
            } else if (target && target.target) {
                target.preventDefault();
                form = target.target.closest('form') || target.target;
            } else if (typeof target === 'string') {
                form = document.querySelector(target);
            }

            var text = customText || 'Tindakan ini tidak dapat dibatalkan. Seluruh data terkait akan dihapus secara permanen dari sistem dan website.';
            var itemBadge = itemName 
                ? '<div class="swal-brand-item-pill"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg><span class="truncate">' + itemName + '</span></div>'
                : '';

            Swal.fire({
                title: '<div class="swal-brand-icon-wrap"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div><div class="swal-brand-title">Konfirmasi Penghapusan</div>',
                html: '<div class="swal-brand-desc">' + text + '</div>' + itemBadge + '<div class="swal-brand-warning">⚠️ Data yang dihapus tidak dapat dipulihkan</div>',
                showCancelButton: true,
                confirmButtonText: '<svg class="w-4 h-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Ya, Hapus Sekarang',
                cancelButtonText: 'Batalkan',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-brand-btn-confirm',
                    cancelButton: 'swal-brand-btn-cancel',
                    actions: 'swal-delete-actions'
                },
                reverseButtons: false,
                focusCancel: true
            }).then(function(result) {
                if (result.isConfirmed && form) {
                    try {
                        HTMLFormElement.prototype.submit.call(form);
                    } catch(err) {
                        form.submit();
                    }
                }
            });
            return false;
        };

        // Global Auto-interceptor for native confirm() dialogs in admin forms
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('button[type="submit"], input[type="submit"]');
            if (!btn) return;
            var form = btn.closest('form');
            if (!form) return;

            var onsubmitStr = form.getAttribute('onsubmit');
            if (onsubmitStr && onsubmitStr.includes('confirm(')) {
                e.preventDefault();
                e.stopPropagation();

                var match = onsubmitStr.match(/confirm\(['"](.*?)['"]\)/);
                var customMsg = match ? match[1] : 'Apakah Anda yakin ingin menghapus data ini?';

                // Try to infer item name from data attribute, parent row, or card
                var itemName = form.getAttribute('data-title') || btn.getAttribute('data-title') || '';
                if (!itemName) {
                    var row = form.closest('tr');
                    if (row) {
                        var nameEl = row.querySelector('.font-bold, .font-semibold, h4, h5');
                        if (nameEl) itemName = nameEl.innerText.trim();
                    }
                }
                if (!itemName) {
                    var card = form.closest('.group') || form.closest('.rounded-2xl') || form.closest('.card');
                    if (card) {
                        var cardTitle = card.querySelector('h3, h4, h5, .font-bold');
                        if (cardTitle) itemName = cardTitle.innerText.trim();
                    }
                }

                window.confirmDelete(form, itemName, customMsg);
            }
        }, true);

        // Global Simple & Refined Centered Modal Alert System
        window.showSuccessAlert = function(message, title) {
            var finalTitle = title || 'Berhasil Disimpan!';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `
                        <div class="swal-modal-icon-wrap success">
                            <div class="swal-modal-icon-inner">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="swal-kicker-badge success">
                            <span class="swal-kicker-dot"></span>
                            <span>Pembaruan Berhasil</span>
                        </div>
                        <div class="swal-simple-title">${finalTitle}</div>
                    `,
                    html: `<div class="swal-simple-desc">${message}</div>`,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check text-xs mr-1.5"></i> Selesai',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-simple-btn'
                    },
                    timer: 2800,
                    timerProgressBar: false
                });
            }
        };

        window.showWarningAlert = function(message, title) {
            var finalTitle = title || 'Perhatian!';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `
                        <div class="swal-modal-icon-wrap warning">
                            <div class="swal-modal-icon-inner">
                                <i class="fas fa-exclamation"></i>
                            </div>
                        </div>
                        <div class="swal-kicker-badge warning">
                            <span class="swal-kicker-dot"></span>
                            <span>Perhatian</span>
                        </div>
                        <div class="swal-simple-title">${finalTitle}</div>
                    `,
                    html: `<div class="swal-simple-desc">${message}</div>`,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check text-xs mr-1.5"></i> Mengerti',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-simple-btn swal-simple-btn-warning'
                    },
                    timer: 3500,
                    timerProgressBar: false
                });
            }
        };

        window.showErrorAlert = function(message, title) {
            var finalTitle = title || 'Gagal Memperbarui';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `
                        <div class="swal-modal-icon-wrap error">
                            <div class="swal-modal-icon-inner">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="swal-kicker-badge error">
                            <span class="swal-kicker-dot"></span>
                            <span>Gagal Memproses</span>
                        </div>
                        <div class="swal-simple-title">${finalTitle}</div>
                    `,
                    html: `<div class="swal-simple-desc" style="color: #be123c; background: #fff1f2; border-color: #ffe4e6;">${message}</div>`,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-times text-xs mr-1.5"></i> Tutup',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-simple-btn swal-simple-btn-error'
                    }
                });
            }
        };

        window.showInfoAlert = function(message, title) {
            var finalTitle = title || 'Informasi';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `
                        <div class="swal-modal-icon-wrap info">
                            <div class="swal-modal-icon-inner">
                                <i class="fas fa-info"></i>
                            </div>
                        </div>
                        <div class="swal-kicker-badge info">
                            <span class="swal-kicker-dot"></span>
                            <span>Informasi</span>
                        </div>
                        <div class="swal-simple-title">${finalTitle}</div>
                    `,
                    html: `<div class="swal-simple-desc" style="color: #0369a1; background: #f0f9ff; border-color: #e0f2fe;">${message}</div>`,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check text-xs mr-1.5"></i> Mengerti',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-simple-btn swal-simple-btn-info'
                    },
                    timer: 3500,
                    timerProgressBar: false
                });
            }
        };

        window.showToast = window.showSuccessAlert;

        // Auto trigger centered popup on session flash dari SEMUA menu admin
        @php
            $flashSuccess = session('status') ?? session('success') ?? session('message') ?? session('pesan');
            $flashWarning = session('warning');
            $flashError = session('error');
            $flashInfo = session('info');
        @endphp

        @if($flashSuccess)
            document.addEventListener('DOMContentLoaded', function() {
                window.showSuccessAlert(@json($flashSuccess), 'Berhasil Disimpan!');
            });
        @endif

        @if($flashWarning)
            document.addEventListener('DOMContentLoaded', function() {
                window.showWarningAlert(@json($flashWarning), 'Perhatian');
            });
        @endif

        @if($flashError)
            document.addEventListener('DOMContentLoaded', function() {
                window.showErrorAlert(@json($flashError), 'Gagal Memperbarui');
            });
        @endif

        @if($flashInfo)
            document.addEventListener('DOMContentLoaded', function() {
                window.showInfoAlert(@json($flashInfo), 'Informasi');
            });
        @endif

        // Global visual indicator on form submission
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.classList.contains('no-spin')) return;
            var btn = form.querySelector('button[type="submit"]:not([data-no-spinner])');
            if (btn && form.checkValidity()) {
                setTimeout(function() {
                    btn.classList.add('opacity-80', 'pointer-events-none');
                    var origText = btn.innerHTML;
                    btn.setAttribute('data-orig-text', origText);
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Menyimpan...';
                }, 20);
            }
        });
    </script>
</body>
</html>