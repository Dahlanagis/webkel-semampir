<!-- FOOTER (Gaya Resmi Portal DLH Kabupaten Probolinggo) -->
<footer id="kontak" class="dlh-footer">
    <div class="dlh-container">
        <div class="dlh-row">
            <!-- Kolom 1: Profil & Media Sosial -->
            <div class="footer-info-card">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if(!empty($systemSettings['app_logo']))
                            <div class="dlh-logo-wrap">
                                <img src="{{ asset('storage/' . $systemSettings['app_logo']) }}" alt="Logo" class="dlh-logo-img">
                            </div>
                        @else
                            <div class="dlh-logo-fallback">
                                <i class="bi bi-award"></i>
                            </div>
                        @endif
                        <div>
                            <span class="dlh-instansi-badge">PEMERINTAH KAB. PROBOLINGGO</span>
                            <h5 class="dlh-site-name">{{ $systemSettings['app_name'] ?? $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}</h5>
                        </div>
                    </div>

                    <div class="dlh-footer-desc">
                        {!! $villageProfile['footer_description'] ?? 'Website Resmi Kelurahan Semampir, Kecamatan Kraksaan, Kabupaten Probolinggo - Portal Informasi Publik, Pelayanan Administrasi Kependudukan, Surat Keterangan Online, Berita, dan Pembangunan Kemasyarakatan.' !!}
                    </div>
                </div>

                <div class="dlh-social-section">
                    <span class="dlh-social-title">Media Sosial Resmi</span>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $fb = !empty($villageProfile['social_facebook']) && $villageProfile['social_facebook'] !== '#' ? $villageProfile['social_facebook'] : 'https://facebook.com/kelurahansemampir';
                            $ig = !empty($villageProfile['social_instagram']) && $villageProfile['social_instagram'] !== '#' ? $villageProfile['social_instagram'] : 'https://instagram.com/kelurahansemampir';
                            $yt = !empty($villageProfile['social_youtube']) && $villageProfile['social_youtube'] !== '#' ? $villageProfile['social_youtube'] : 'https://youtube.com/@kelurahansemampir';
                            $tt = !empty($villageProfile['social_tiktok']) && $villageProfile['social_tiktok'] !== '#' ? $villageProfile['social_tiktok'] : 'https://tiktok.com/@kelurahansemampir';
                            
                            $rawWa = !empty($villageProfile['whatsapp']) ? $villageProfile['whatsapp'] : (!empty($villageProfile['social_whatsapp']) && $villageProfile['social_whatsapp'] !== '#' ? $villageProfile['social_whatsapp'] : '0812-3456-7890');
                            $waNum = preg_replace('/[^0-9]/', '', $rawWa);
                            if (str_starts_with($waNum, '0')) {
                                $waNum = '62' . substr($waNum, 1);
                            }
                            $waUrl = 'https://wa.me/' . $waNum;
                        @endphp

                        <a href="{{ $fb }}" target="_blank" rel="noopener noreferrer" class="dlh-social-btn dlh-social-fb" title="Facebook Resmi Kelurahan Semampir">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer" class="dlh-social-btn dlh-social-ig" title="Instagram Resmi @kelurahansemampir">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="{{ $yt }}" target="_blank" rel="noopener noreferrer" class="dlh-social-btn dlh-social-yt" title="YouTube Channel Kelurahan Semampir">
                            <i class="bi bi-youtube"></i>
                        </a>
                        <a href="{{ $tt }}" target="_blank" rel="noopener noreferrer" class="dlh-social-btn dlh-social-tt" title="TikTok Resmi Kelurahan Semampir">
                            <i class="bi bi-tiktok"></i>
                        </a>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="dlh-social-btn dlh-social-wa" title="WhatsApp Layanan Resmi">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: QR Card Footer (Animasi Border Berputar khas DLH) -->
            <div>
                <div class="qr-card-footer h-100">
                    <div class="qr-card-title">SCAN KODE QR</div>
                    <div>
                        <div class="qr-card-image-wrap">
                            @if(!empty($villageProfile['qr_code_image']))
                                <img src="{{ asset('storage/' . $villageProfile['qr_code_image']) }}" alt="QR Code Pelayanan" class="qr-card-img">
                            @elseif(file_exists(public_path('storage/settings/qrcode.png')))
                                <img src="{{ asset('storage/settings/qrcode.png') }}" alt="QR Code Pelayanan" class="qr-card-img">
                            @else
                                <div class="qr-card-img d-flex flex-column align-items-center justify-content-center bg-white text-dark">
                                    <i class="bi bi-qr-code" style="font-size: 3.5rem; color: #162033;"></i>
                                    <span style="font-size: 10px; font-weight: 700; color: #64748b; margin-top: 4px;">QR PELAYANAN</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="qr-card-subtitle mt-auto">
                        <i class="bi bi-hand-index-thumb me-1"></i>Scan QR Portal Pelayanan
                    </div>
                    <div class="qr-card-info">{{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }} - {{ $villageProfile['subdistrict'] ?? 'Kec. Kraksaan' }}</div>
                </div>
            </div>

            <!-- Kolom 3: Alamat & Kontak Resmi -->
            <div class="footer-info-card">
                <div>
                    <h5 class="dlh-card-heading">Alamat Kantor</h5>
                    
                    <div class="d-flex gap-3 mb-4 align-items-start">
                        <div class="text-warning mt-1 fs-5"><i class="bi bi-geo-alt-fill"></i></div>
                        <div class="text-light opacity-75 small" style="line-height: 1.6;">
                            {{ $villageProfile['address'] ?? 'Jl. Pahlawan No. 01, Kelurahan Semampir, Kecamatan Kraksaan, Kabupaten Probolinggo' }}
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 mb-4 align-items-start">
                        <div class="text-warning mt-1 fs-5"><i class="bi bi-telephone-fill"></i></div>
                        <div class="text-light opacity-75 small">
                            Phone: {{ $villageProfile['phone'] ?? '(0335) 841-209' }}
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 align-items-start">
                        <div class="text-warning mt-1 fs-5"><i class="bi bi-envelope-fill"></i></div>
                        <div class="text-light opacity-75 small">
                            Email: {{ $villageProfile['email'] ?? 'kelurahansemampir@probolinggokab.go.id' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary opacity-25">

        <div class="d-flex justify-content-center align-items-center pt-3 pb-2 small text-light opacity-75 text-center">
            <div>
                {{ $systemSettings['app_name'] ?? $villageProfile['village_name'] ?? 'Kelurahan Semampir' }} - Kabupaten Probolinggo &copy; {{ date('Y') }}. All Rights Reserved
            </div>
        </div>
    </div>
</footer>

<!-- Admin Floating Button Keren khas DLH -->
<a href="{{ route('login') }}" class="admin-floating-btn" title="Portal Admin">
    <i class="bi bi-shield-lock-fill"></i>
    <span class="admin-floating-text">Portal Admin</span>
</a>

<style>
    /* 🏢 FOOTER STYLES (ELEGANT ABU GELAP / DARK CHARCOAL) 🏢 */
    footer.dlh-footer {
        background: linear-gradient(180deg, #181b20 0%, #0f1115 100%);
        color: #cbd5e1;
        padding-top: 60px;
        padding-bottom: 30px;
        margin-top: 60px;
        position: relative;
    }

    .dlh-container {
        max-width: 1240px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 20px;
        padding-right: 20px;
    }

    .dlh-row {
        display: grid;
        grid-template-columns: 1.25fr 0.85fr 1.05fr;
        gap: 24px;
        margin-bottom: 40px;
        align-items: stretch;
    }

    @media (max-width: 991px) {
        .dlh-row {
            grid-template-columns: 1fr;
        }
    }

    /* 🏢 FOOTER INFO CARD 🏢 */
    .footer-info-card {
        background: linear-gradient(145deg, #232730 0%, #1a1d24 50%, #14171d 100%);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        transition: all 0.4s ease;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .footer-info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        border-color: rgba(232, 163, 23, 0.2);
    }

    .dlh-card-heading {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 24px;
        position: relative;
        padding-bottom: 12px;
        display: block;
        width: fit-content;
    }

    .dlh-card-heading::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 45px;
        height: 3px;
        background: linear-gradient(90deg, #e8a317, #ffc107);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .footer-info-card:hover .dlh-card-heading::after {
        width: 100%;
    }

    .dlh-logo-wrap {
        background: #ffffff;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
    }

    .dlh-logo-img {
        height: 38px;
        width: 38px;
        object-fit: contain;
    }

    .dlh-logo-fallback {
        background: #e8a317;
        color: #0f1923;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .dlh-instansi-badge {
        font-size: 0.65rem;
        font-weight: 800;
        color: #e8a317;
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 2px;
    }

    .dlh-site-name {
        margin: 0;
        color: #ffffff;
        font-weight: 700;
        font-size: 1.1rem;
        line-height: 1.3;
    }

    .dlh-footer-desc {
        color: #cbd5e1;
        opacity: 0.85;
        font-size: 0.85rem;
        line-height: 1.8;
        margin-bottom: 20px;
    }

    .dlh-footer-desc p {
        color: inherit;
        font-size: inherit;
        line-height: inherit;
        margin-bottom: 0;
    }

    .dlh-social-section {
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .dlh-social-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        display: block;
        margin-bottom: 12px;
    }

    .dlh-social-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.16);
        color: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .dlh-social-btn:hover {
        transform: translateY(-3px) scale(1.08);
        color: #ffffff;
    }

    .dlh-social-fb:hover {
        background-color: #1877f2;
        border-color: #1877f2;
        box-shadow: 0 6px 18px rgba(24, 119, 242, 0.45);
    }

    .dlh-social-ig:hover {
        background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
        border-color: transparent;
        box-shadow: 0 6px 18px rgba(214, 36, 159, 0.45);
    }

    .dlh-social-yt:hover {
        background-color: #ff0000;
        border-color: #ff0000;
        box-shadow: 0 6px 18px rgba(255, 0, 0, 0.45);
    }

    .dlh-social-tt:hover {
        background-color: #010101;
        border-color: #25f4ee;
        box-shadow: 0 6px 18px rgba(37, 244, 238, 0.35);
    }

    .dlh-social-wa:hover {
        background-color: #25d366;
        border-color: #25d366;
        box-shadow: 0 6px 18px rgba(37, 211, 102, 0.45);
    }

    /* 📱 QR CARD FOOTER 📱 */
    .qr-card-footer {
        background: linear-gradient(145deg, #232730 0%, #1a1d24 50%, #14171d 100%);
        border-radius: 20px;
        padding: 24px 20px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .qr-card-footer::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, transparent, rgba(232, 163, 23, 0.06), transparent, rgba(255, 255, 255, 0.04), transparent);
        animation: qr-rotate 10s linear infinite;
    }

    @keyframes qr-rotate {
        to { transform: rotate(360deg); }
    }

    .qr-card-footer:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        border-color: rgba(232, 163, 23, 0.2);
    }

    .qr-card-title {
        position: relative;
        z-index: 1;
        font-size: 0.82rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: 2.5px;
        margin-bottom: 16px;
        text-transform: uppercase;
    }

    .qr-card-image-wrap {
        position: relative;
        z-index: 1;
        display: inline-block;
        padding: 10px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        margin-bottom: 16px;
        transition: all 0.3s ease;
    }

    .qr-card-footer:hover .qr-card-image-wrap {
        box-shadow: 0 8px 30px rgba(232, 163, 23, 0.15), 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .qr-card-img {
        width: 140px;
        height: 140px;
        border-radius: 8px;
        display: block;
        object-fit: contain;
        margin: 0 auto;
    }

    .qr-card-subtitle {
        position: relative;
        z-index: 1;
        font-size: 0.82rem;
        font-weight: 600;
        color: #e8a317;
        margin-bottom: 4px;
    }

    .qr-card-info {
        position: relative;
        z-index: 1;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.45);
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Bootstrap Helper Utilities */
    .d-flex { display: flex; }
    .flex-column { flex-direction: column; }
    .flex-wrap { flex-wrap: wrap; }
    .align-items-center { align-items: center; }
    .align-items-start { align-items: flex-start; }
    .justify-content-center { justify-content: center; }
    .justify-content-between { justify-content: space-between; }
    .h-100 { height: 100%; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 0.75rem; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-auto { margin-top: auto; }
    .me-1 { margin-right: 0.25rem; }
    .pt-3 { padding-top: 1rem; }
    .pb-2 { padding-bottom: 0.5rem; }
    .small { font-size: 0.85rem; }
    .fw-medium { font-weight: 500; }
    .fw-semibold { font-weight: 600; }
    .fw-bold { font-weight: 700; }
    .text-center { text-align: center; }
    .text-white { color: #ffffff !important; }
    .text-light { color: #f8fafc !important; }
    .text-warning { color: #e8a317 !important; }
    .opacity-75 { opacity: 0.75; }
    .fs-5 { font-size: 1.25rem; }
    .border-secondary { border-color: rgba(255, 255, 255, 0.12) !important; }

    /* Admin Floating Button */
    .admin-floating-btn {
        position: fixed;
        bottom: 30px;
        left: 30px;
        background: rgba(26, 29, 36, 0.88);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 50px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        z-index: 9999;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
    }

    .admin-floating-btn i {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .admin-floating-text {
        font-weight: 600;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        white-space: nowrap;
        max-width: 0;
        opacity: 0;
        transition: all 0.4s ease;
    }

    .admin-floating-btn:hover {
        background: linear-gradient(135deg, #2f3440, #191c22);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.45);
        transform: translateY(-5px);
        color: white;
        border-color: rgba(255, 255, 255, 0.35);
    }

    .admin-floating-btn:hover i {
        transform: scale(1.1);
    }

    .admin-floating-btn:hover .admin-floating-text {
        max-width: 150px;
        opacity: 1;
    }

    @media (max-width: 768px) {
        .admin-floating-btn {
            bottom: 20px;
            left: 20px;
            padding: 10px 15px;
        }
    }
</style>
