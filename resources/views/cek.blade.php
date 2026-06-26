<x-layouts.public title="Cek Kehadiran Tamu Sidang - PN Natuna">
    <div class="monitor-refresh-bar" aria-hidden="true"></div>

    <div class="form-corner-brand form-corner-left monitor-brand">
        <img src="{{ asset('images/logo-pn-natuna-emblem.png') }}" alt="Logo Pengadilan Negeri Natuna Kelas II">
        <div>
            <strong>Pengadilan Negeri Natuna Kelas II</strong>
            <span>Monitoring Kehadiran Sidang</span>
        </div>
    </div>

    <div class="landing-corner-right monitor-clock-wrap">
        <div class="form-corner-berakhlak">
            <img src="{{ asset('images/berakhlak-bangga.png') }}" alt="BerAKHLAK Bangga Melayani Bangsa">
        </div>
        <div class="monitor-clock-text" aria-label="Tanggal dan jam sekarang">
            <span id="current-date-text">Hari, Tanggal</span>
            <b id="current-time">00:00:00 WIB</b>
            <em id="connection-status"><i class="online-pulse" aria-hidden="true"></i>Online • diperbarui {{ now()->format('H:i') }} WIB</em>
        </div>
    </div>

    <div class="cek-container monitor-container">
        @php
            $selectedDate = \Carbon\Carbon::parse($dateStr)->locale('id');
            $isToday = $selectedDate->isToday();
            $formattedDate = $selectedDate->translatedFormat('l, d F Y');
            $roles = ['Para Pihak', 'Saksi', 'Ahli', 'Pengunjung'];
            $jadwalCount = $jadwalSidangs->count();
        @endphp

        <header class="monitor-hero">
            <div>
                <p class="monitor-kicker">Dashboard Persidangan</p>
                <h1 class="cek-title monitor-title">Monitoring Kehadiran Tamu Sidang</h1>
                <p class="monitor-date">{{ $isToday ? 'Hari Ini' : $formattedDate }}</p>
            </div>
            <div class="monitor-summary" aria-label="Ringkasan jadwal">
                <strong>{{ $jadwalCount }}</strong>
                <span>{{ $jadwalCount === 1 ? 'Perkara' : 'Perkara' }}</span>
            </div>
        </header>

        <form method="GET" action="{{ route('cek') }}" class="date-filter-form monitor-date-filter">
            <label for="tanggal">Tanggal Sidang</label>
            <input type="date" id="tanggal" name="tanggal" value="{{ $dateStr }}" onchange="this.form.submit()" class="date-picker-input">
        </form>

        @if($jadwalSidangs->isEmpty())
            <div class="empty-sidang-card monitor-empty">
                <div class="empty-icon">📅</div>
                <h3>Tidak Ada Jadwal Sidang</h3>
                <p>Belum ada data jadwal sidang SIPP yang tersinkronisasi untuk {{ $isToday ? 'hari ini' : $formattedDate }}.</p>
            </div>
        @else
            <section class="sidang-monitor-grid {{ $jadwalCount === 1 ? 'single' : 'multi' }}" aria-label="Daftar monitoring sidang">
                @foreach($jadwalSidangs as $sidang)
                    @php
                        $ruang = $sidang->ruang_sidang ?? 'Ruang Sidang';
                        if (preg_match('/cakra/i', $ruang)) {
                            $ruang = 'Ruang Sidang Cakra';
                        } else {
                            $ruang = ucwords(strtolower($ruang));
                        }

                        $paraPihak = collect();
                        if ($sidang->para_pihak) {
                            $normalizedPihak = preg_replace('/\s+/', ' ', trim($sidang->para_pihak));
                            $normalizedPihak = preg_replace('/\s+(?=\d+\.)/', "\n", $normalizedPihak);

                            $paraPihak = collect(preg_split('/\R+/', $normalizedPihak))
                                ->map(fn ($pihak) => trim($pihak))
                                ->filter()
                                ->values();
                        }

                    @endphp

                    <article class="sidang-card monitor-sidang-card">
                        <div class="sidang-header monitor-sidang-header">
                            <div class="sidang-info">
                                <div class="sidang-title-row">
                                    <span class="sidang-no-perkara">{{ $sidang->nomor_perkara }}</span>
                                    @if($sidang->jenis_perkara)
                                        <span class="sidang-jenis-perkara">{{ $sidang->jenis_perkara }}</span>
                                    @endif
                                    <span class="sidang-badge ruang">🏛️ {{ $ruang }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sidang-main-info">
                            @if($paraPihak->isNotEmpty())
                                <section class="sidang-pihak-panel" aria-label="Para pihak perkara">
                                    <div class="sidang-section-label">Para Pihak</div>
                                    <div class="sidang-pihak-list">
                                        @foreach($paraPihak as $pihak)
                                            <div class="sidang-pihak-row">{{ $pihak }}</div>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if($sidang->agenda_sidang)
                                <section class="sidang-agenda-panel" aria-label="Agenda sidang">
                                    <div class="sidang-section-label">Agenda Sidang</div>
                                    <p>{{ $sidang->agenda_sidang }}</p>
                                </section>
                            @endif
                        </div>

                        <div class="tamu-grid monitor-tamu-grid">
                            @foreach($roles as $role)
                                @php
                                    $roleGuests = $sidang->guests->where('peran_sidang', $role);
                                    $roleCount = $roleGuests->count();
                                @endphp
                                <div class="tamu-role-column role-{{ \Illuminate\Support\Str::slug($role) }} {{ $roleCount > 0 ? 'is-present' : 'is-absent' }}">
                                    <div class="tamu-role-header">
                                        <span>
                                            @if($role === 'Para Pihak')
                                                ⚖️ Para Pihak
                                            @elseif($role === 'Saksi')
                                                🗣️ Saksi
                                            @elseif($role === 'Ahli')
                                                🎓 Ahli
                                            @else
                                                👥 Pengunjung
                                            @endif
                                        </span>
                                    </div>

                                    @if($roleGuests->isNotEmpty())
                                        @foreach($roleGuests as $guest)
                                            <div class="guest-present-item" title="Hadir pada {{ $guest->created_at->format('H:i') }} WIB">
                                                <span class="badge-status present">✓</span>
                                                <div>
                                                    {{ $guest->nama_tamu }}
                                                    <small>{{ $guest->waktu_kedatangan ? $guest->waktu_kedatangan->format('H:i') : $guest->created_at->format('H:i') }} WIB</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="guest-absent-item">
                                            <span class="badge-status absent">–</span>
                                            <span>{{ $role === 'Ahli' ? 'Belum Hadir / Tidak Ada' : 'Belum Hadir' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>

    <script>
        const refreshInterval = 300000;
        const loadedAt = new Date();

        function updateDateTime() {
            const now = new Date();
            const locale = 'id-ID';
            const day = new Intl.DateTimeFormat(locale, { weekday: 'long' }).format(now);
            const date = new Intl.DateTimeFormat(locale, { day: '2-digit', month: 'long', year: 'numeric' }).format(now);
            const dateEl = document.getElementById('current-date-text');
            if (dateEl) dateEl.textContent = `${day}, ${date}`;

            const time = new Intl.DateTimeFormat(locale, { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(now).replace(/\./g, ':');
            const timeEl = document.getElementById('current-time');
            if (timeEl) timeEl.textContent = `${time} WIB`;

            updateConnectionStatus();
        }

        function updateConnectionStatus() {
            const statusEl = document.getElementById('connection-status');
            if (!statusEl) return;

            const updated = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).format(loadedAt).replace(/\./g, ':');
            statusEl.textContent = `${navigator.onLine ? 'Online' : 'Offline'} • diperbarui ${updated} WIB`;
            statusEl.classList.toggle('is-offline', !navigator.onLine);
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);

        setTimeout(() => {
            if (navigator.onLine) {
                window.location.reload();
            } else {
                updateConnectionStatus();
            }
        }, refreshInterval);
    </script>
</x-layouts.public>
