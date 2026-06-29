<x-layouts.public title="Buku Tamu PN Natuna">
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

    <section class="form-hero">
        <a href="{{ route('home') }}" class="back-link">← Kembali ke Beranda</a>
        <p class="form-kicker">Buku Tamu Digital</p>
        <h1>Pengisian Buku Tamu</h1>
        <p>
            Silakan lengkapi data kunjungan Anda. Kolom bertanda <strong>*</strong> wajib diisi,
            kecuali keperluan.
        </p>
    </section>

    <section class="form-card">
        @if($errors->any())
            <div class="alert-error">
                <strong>Data belum lengkap.</strong>
                <span>Mohon periksa kembali kolom yang wajib diisi.</span>
            </div>
        @endif

        <form method="post" action="{{ route('buku-tamu.store') }}" class="guest-form">
            @csrf

            <div class="form-section-title">
                <span>1</span>
                <div>
                    <h2>Identitas Tamu</h2>
                    <p>Data dasar pengunjung untuk pencatatan kunjungan.</p>
                </div>
            </div>

            <div class="form-grid">
                <label class="field field-full">
                    <span>Nama Tamu <b>*</b></span>
                    <input name="nama_tamu" value="{{ old('nama_tamu') }}" required placeholder="Contoh: Budi Santoso">
                    @error('nama_tamu')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field">
                    <span>Pekerjaan <b>*</b></span>
                    <div class="job-autocomplete">
                        <input id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" required autocomplete="off" placeholder="Ketik contoh: Wiraswasta / Petani / PNS atau pilih Lainnya">
                        <button type="button" id="toggle-pekerjaan" class="job-toggle" aria-label="Tampilkan daftar pekerjaan">⌄</button>
                        <div id="pekerjaan_suggestions" class="job-suggestions hidden" role="listbox"></div>
                    </div>
                    @error('pekerjaan')<small>{{ $message }}</small>@enderror
                </label>

                <label id="pekerjaan_lainnya_box" class="field hidden">
                    <span>Jelaskan Pekerjaan Lainnya <b>*</b></span>
                    <input name="pekerjaan_lainnya" value="{{ old('pekerjaan_lainnya') }}" placeholder="Tuliskan pekerjaan Anda">
                    @error('pekerjaan_lainnya')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field">
                    <span>No HP <b>*</b></span>
                    <input name="no_hp" value="{{ old('no_hp') }}" required inputmode="tel" placeholder="Contoh: 0812xxxxxxx">
                    @error('no_hp')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field field-full">
                    <span>Alamat / Instansi <b>*</b></span>
                    <input name="alamat_instansi" value="{{ old('alamat_instansi') }}" required placeholder="Contoh: Ranai / PT ... / Dinas ...">
                    @error('alamat_instansi')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <input type="hidden" name="jenis_layanan" value="Menghadiri Sidang">

            <div class="form-section-title">
                <span>2</span>
                <div>
                    <h2>Tujuan Kunjungan</h2>
                    <p>Menghadiri Sidang</p>
                </div>
            </div>

            <div class="sidang-panel">
                <div class="sidang-info">
                    <strong>Informasi Jadwal Sidang</strong>
                    <span>Pilih jadwal sidang hari ini yang telah tersinkron dari SIPP PN Natuna.</span>
                </div>

                <div class="form-grid">
                    <label class="field field-full">
                        <span>Pilih Jadwal Sidang Hari Ini <b>*</b></span>
                        <select name="jadwal_sidang_id">
                            <option value="">Pilih jadwal sidang</option>
                            @foreach($jadwalSidangs as $j)
                                <option value="{{ $j->id }}" @selected(old('jadwal_sidang_id')==$j->id)>{{ $j->label }}</option>
                            @endforeach
                        </select>
                        @error('jadwal_sidang_id')<small>{{ $message }}</small>@enderror
                    </label>

                    <label class="field field-full">
                        <span>Hadir Sebagai <b>*</b></span>
                        <select name="peran_sidang">
                            <option value="">Pilih peran</option>
                            @foreach(\App\Models\Guest::PERAN_SIDANG as $p)
                                <option @selected(old('peran_sidang')===$p)>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('peran_sidang')<small>{{ $message }}</small>@enderror
                    </label>
                </div>
            </div>

            <div class="form-section-title">
                <span>3</span>
                <div>
                    <h2>Informasi Tambahan</h2>
                    <p>Bagian ini opsional, isi bila diperlukan.</p>
                </div>
            </div>

            <div class="form-grid">
                <label class="field field-full">
                    <span>Keperluan</span>
                    <textarea name="keperluan" rows="3" placeholder="Tuliskan keperluan kunjungan bila ada">{{ old('keperluan') }}</textarea>
                    @error('keperluan')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field captcha-field">
                    <span>Verifikasi <b>*</b></span>
                    <div class="captcha-box">
                        <strong>{{ $captchaLeft }} + {{ $captchaRight }} =</strong>
                        <input name="captcha" value="{{ old('captcha') }}" required inputmode="numeric" autocomplete="off" placeholder="Jawaban">
                    </div>
                    @error('captcha')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <button class="submit-button" type="submit">
                Simpan Data Kunjungan
                <span>→</span>
            </button>
        </form>
    </section>

    <script>
        const pekerjaanOptions = [
            'Buruh', 'Dokter/Tenaga Kesehatan', 'Dosen', 'Guru', 'Honorer', 'Ibu Rumah Tangga',
            'Mahasiswa/Pelajar', 'Nelayan', 'Notaris', 'Pegawai Swasta', 'Pedagang',
            'Pengacara/Advokat', 'Pensiunan', 'Petani', 'PNS', 'PPPK', 'Sopir', 'TNI/Polri',
            'Wiraswasta', 'Lainnya'
        ];
        const pekerjaanInput = document.getElementById('pekerjaan');
        const pekerjaanLainnyaBox = document.getElementById('pekerjaan_lainnya_box');
        const suggestionsBox = document.getElementById('pekerjaan_suggestions');
        const togglePekerjaan = document.getElementById('toggle-pekerjaan');

        function togglePekerjaanLainnya() {
            const isOther = pekerjaanInput.value.trim().toLowerCase() === 'lainnya';
            pekerjaanLainnyaBox.classList.toggle('hidden', !isOther);
            const otherInput = pekerjaanLainnyaBox.querySelector('input');
            if (otherInput) { otherInput.required = isOther; if (!isOther) otherInput.value = ''; }
        }

        function renderPekerjaanSuggestions(showAll = false) {
            const keyword = pekerjaanInput.value.trim().toLowerCase();
            const filtered = pekerjaanOptions.filter(item => showAll || item.toLowerCase().includes(keyword));
            suggestionsBox.innerHTML = '';

            if (!filtered.length) {
                suggestionsBox.innerHTML = '<div class="job-empty">Tidak ada di daftar. Ketik manual atau pilih Lainnya.</div>';
            } else {
                filtered.forEach(item => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'job-option' + (item === 'Lainnya' ? ' job-option-other' : '');
                    button.textContent = item;
                    button.addEventListener('click', () => {
                        pekerjaanInput.value = item;
                        suggestionsBox.classList.add('hidden');
                        togglePekerjaanLainnya();
                    });
                    suggestionsBox.appendChild(button);
                });
            }
            suggestionsBox.classList.remove('hidden');
        }

        pekerjaanInput.addEventListener('input', () => { renderPekerjaanSuggestions(false); togglePekerjaanLainnya(); });
        pekerjaanInput.addEventListener('focus', () => renderPekerjaanSuggestions(true));
        togglePekerjaan.addEventListener('click', () => renderPekerjaanSuggestions(true));
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.job-autocomplete')) suggestionsBox.classList.add('hidden');
        });
        togglePekerjaanLainnya();

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
