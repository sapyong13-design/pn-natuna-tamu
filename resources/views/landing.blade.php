<x-layouts.public title="Portal Tamu PN Natuna">
    <!-- Corner Badges on Page Background -->
    <div class="form-corner-brand form-corner-left">
        <img src="{{ asset('images/logo-pn-natuna-emblem.png') }}" alt="Logo Pengadilan Negeri Natuna Kelas II">
        <div>
            <strong>Pengadilan Negeri Natuna Kelas II</strong>
            <span>Portal Buku Tamu Digital</span>
        </div>
    </div>

    <div class="landing-corner-right">
        <div class="form-corner-berakhlak">
            <img src="{{ asset('images/berakhlak-bangga.png') }}" alt="BerAKHLAK Bangga Melayani Bangsa">
        </div>
        <div class="datetime-card monitor-clock-text" aria-label="Tanggal dan jam sekarang">
            <span id="current-date-text">Hari, Tanggal</span>
            <b id="current-time">00:00:00 WIB</b>
        </div>
    </div>

    <section class="hero-card hero-clean-layout">
        <div class="hero-content welcome-content">
            <p class="hero-kicker">Portal Layanan Tamu</p>
            <h1 class="hero-title">
                Selamat Datang di<br>
                Portal Layanan Digital
            </h1>
            <p class="hero-description">
                Pilih layanan yang Anda butuhkan
            </p>
        </div>
    </section>

    <section class="service-grid" aria-label="Pilihan layanan">
        <div class="service-card blue">
            <div class="service-icon">📝</div>
            <h2 class="service-title">Pengisian Buku Tamu</h2>
            <p class="service-text">Catat kunjungan Anda dengan formulir sederhana atau periksa kehadiran sidang hari ini.</p>
            <div class="service-actions">
                <a class="btn-action primary-btn" href="{{ route('buku-tamu.create') }}">
                    Isi Buku Tamu <span>→</span>
                </a>
                <a class="btn-action secondary-btn" href="{{ route('cek') }}">
                    🔍 Cek Tamu
                </a>
            </div>
        </div>

        <a class="service-card green" href="{{ $links['Survey Kepuasan Masyarakat'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
            <div class="service-icon">😊</div>
            <h2 class="service-title">Survey Kepuasan Masyarakat</h2>
            <p class="service-text">Berikan penilaian Anda untuk membantu kami meningkatkan mutu pelayanan publik.</p>
            <span class="service-cta">Isi Survey <span>→</span></span>
        </a>

        <a class="service-card purple" href="{{ $links['Survey IKM dan SPAK'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
            <div class="service-icon service-icon-image"><img src="{{ asset('images/logo-spak-sisuper.png') }}" alt="Logo Survey IKM dan SPAK"></div>
            <h2 class="service-title">Survey IKM dan SPAK</h2>
            <p class="service-text">Sampaikan pengalaman layanan Anda untuk mendukung pelayanan yang bersih dan profesional.</p>
            <span class="service-cta">Isi Survey <span>→</span></span>
        </a>
    </section>

    <section class="info-card">
        <strong>Pengadilan Negeri Natuna Kelas II</strong> berkomitmen memberikan pelayanan yang transparan,
        akuntabel, ramah, dan mudah diakses oleh seluruh masyarakat.
    </section>

    <script>
        function updateDateTime() {
            const now = new Date();
            const locale = 'id-ID';
            const day = new Intl.DateTimeFormat(locale, { weekday: 'long' }).format(now);
            const date = new Intl.DateTimeFormat(locale, { day: '2-digit', month: 'long', year: 'numeric' }).format(now);
            document.getElementById('current-date-text').textContent = `${day}, ${date}`;
            
            const time = new Intl.DateTimeFormat(locale, { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(now).replace(/\./g, ':');
            document.getElementById('current-time').textContent = `${time} WIB`;
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</x-layouts.public>
