<x-layouts.public title="Cek Kehadiran Tamu Sidang - PN Natuna">
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
        <div class="datetime-card" aria-label="Tanggal dan jam sekarang">
            <span id="current-date-text">Hari, Tanggal</span>
            <b id="current-time">00:00:00 WIB</b>
        </div>
    </div>

    <div class="cek-container">
        @php
            $selectedDate = \Carbon\Carbon::parse($dateStr)->locale('id');
            $isToday = $selectedDate->isToday();
            $formattedDate = $selectedDate->translatedFormat('l, d F Y');
        @endphp
        
        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
            <a href="{{ route('home') }}" class="back-link">← Kembali ke Beranda</a>
            
            <form method="GET" action="{{ route('cek') }}" class="date-filter-form" style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
                <label for="tanggal" style="color: rgba(255, 255, 255, 0.9); font-weight: 600; font-size: 0.95rem; white-space: nowrap;">Pilih Tanggal Sidang:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $dateStr }}" onchange="this.form.submit()" class="date-picker-input">
            </form>
        </div>
        
        <h1 class="cek-title">
            Monitoring Kehadiran Tamu Sidang<br>
            <span style="font-size: 1.1rem; color: #a7f3d0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">
                {{ $isToday ? 'Hari Ini' : $formattedDate }}
            </span>
        </h1>

        @if($jadwalSidangs->isEmpty())
            <div class="empty-sidang-card">
                <div class="empty-icon">📅</div>
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px;">Tidak Ada Jadwal Sidang</h3>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem;">
                    Belum ada data jadwal sidang SIPP yang tersinkronisasi untuk {{ $isToday ? 'hari ini' : $formattedDate }}.
                </p>
            </div>
        @else
            @foreach($jadwalSidangs as $sidang)
                @php
                    $ruang = $sidang->ruang_sidang ?? 'Ruang Sidang';
                    if (preg_match('/cakra/i', $ruang)) {
                        $ruang = 'Ruang Sidang Cakra';
                    } else {
                        $ruang = ucwords(strtolower($ruang));
                    }
                @endphp
                <div class="sidang-card">
                    <div class="sidang-header">
                        <div>
                            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px;">
                                <span class="sidang-no-perkara">{{ $sidang->nomor_perkara }}</span>
                                @if($sidang->jenis_perkara)
                                    <span class="sidang-jenis-perkara" style="font-weight: 700; color: #f59e0b; background: rgba(245, 158, 11, 0.15); padding: 3px 10px; border-radius: 999px; font-size: 0.8rem; border: 1px solid rgba(245, 158, 11, 0.25); white-space: nowrap;">
                                        {{ $sidang->jenis_perkara }}
                                    </span>
                                @endif
                                @if($sidang->para_pihak)
                                    <span class="sidang-pihak" style="font-weight: 500; color: #e2e8f0; font-size: 0.85rem; background: rgba(255, 255, 255, 0.08); padding: 3px 10px; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.1);">
                                        {{ $sidang->para_pihak }}
                                    </span>
                                @endif
                            </div>
                            @if($sidang->agenda_sidang)
                                <div class="sidang-agenda">Agenda: {{ $sidang->agenda_sidang }}</div>
                            @endif
                        </div>
                        <div class="sidang-meta">
                            <span class="sidang-badge ruang">🏛️ {{ $ruang }}</span>
                        </div>
                    </div>

                    @php
                        $roles = ['Para Pihak', 'Saksi', 'Ahli', 'Pengunjung'];
                    @endphp

                    <div class="tamu-grid">
                        @foreach($roles as $role)
                            @php
                                $roleGuests = $sidang->guests->where('peran_sidang', $role);
                            @endphp
                            <div class="tamu-role-column">
                                <div class="tamu-role-header">
                                    @if($role === 'Para Pihak')
                                        👤 Para Pihak
                                    @elseif($role === 'Saksi')
                                        👥 Saksi
                                    @elseif($role === 'Ahli')
                                        🎓 Ahli
                                    @else
                                        🎒 Pengunjung
                                    @endif
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
                                        <span class="badge-status absent">✗</span>
                                        <span>
                                            @if($role === 'Ahli')
                                                Belum Hadir / Tidak Ada
                                            @else
                                                Belum Hadir
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        // Realtime Clock Functionality
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
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</x-layouts.public>
