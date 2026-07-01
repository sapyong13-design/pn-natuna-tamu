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
        <div class="datetime-card monitor-clock-text" aria-label="Tanggal dan jam sekarang">
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
                    <select name="pekerjaan" id="pekerjaan" required>
                        <option value="">Pilih Pekerjaan</option>
                        <option value="Buruh" {{ old('pekerjaan') == 'Buruh' ? 'selected' : '' }}>Buruh</option>
                        <option value="Dokter/Tenaga Kesehatan" {{ old('pekerjaan') == 'Dokter/Tenaga Kesehatan' ? 'selected' : '' }}>Dokter/Tenaga Kesehatan</option>
                        <option value="Dosen" {{ old('pekerjaan') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="Guru" {{ old('pekerjaan') == 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Honorer" {{ old('pekerjaan') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        <option value="Ibu Rumah Tangga" {{ old('pekerjaan') == 'Ibu Rumah Tangga' ? 'selected' : '' }}>Ibu Rumah Tangga</option>
                        <option value="Mahasiswa/Pelajar" {{ old('pekerjaan') == 'Mahasiswa/Pelajar' ? 'selected' : '' }}>Mahasiswa/Pelajar</option>
                        <option value="Nelayan" {{ old('pekerjaan') == 'Nelayan' ? 'selected' : '' }}>Nelayan</option>
                        <option value="Notaris" {{ old('pekerjaan') == 'Notaris' ? 'selected' : '' }}>Notaris</option>
                        <option value="Pegawai Swasta" {{ old('pekerjaan') == 'Pegawai Swasta' ? 'selected' : '' }}>Pegawai Swasta</option>
                        <option value="Pedagang" {{ old('pekerjaan') == 'Pedagang' ? 'selected' : '' }}>Pedagang</option>
                        <option value="Pengacara/Advokat" {{ old('pekerjaan') == 'Pengacara/Advokat' ? 'selected' : '' }}>Pengacara/Advokat</option>
                        <option value="Pensiunan" {{ old('pekerjaan') == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                        <option value="Petani" {{ old('pekerjaan') == 'Petani' ? 'selected' : '' }}>Petani</option>
                        <option value="PNS" {{ old('pekerjaan') == 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK" {{ old('pekerjaan') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="Sopir" {{ old('pekerjaan') == 'Sopir' ? 'selected' : '' }}>Sopir</option>
                        <option value="TNI/Polri" {{ old('pekerjaan') == 'TNI/Polri' ? 'selected' : '' }}>TNI/Polri</option>
                        <option value="Wiraswasta" {{ old('pekerjaan') == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                        <option value="Lainnya" {{ old('pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('pekerjaan')<small>{{ $message }}</small>@enderror
                </label>

                <label id="pekerjaan_lainnya_box" class="field hidden">
                    <span>Jelaskan Pekerjaan Lainnya <b>*</b></span>
                    <input name="pekerjaan_lainnya" value="{{ old('pekerjaan_lainnya') }}" placeholder="Tuliskan pekerjaan Anda">
                    @error('pekerjaan_lainnya')<small>{{ $message }}</small>@enderror
                </label>
                <label class="field">
                    <span>Tanggal Lahir <b>*</b></span>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                    @error('tanggal_lahir')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field">
                    <span>Umur Saat Ini</span>
                    <input type="text" id="umur_saat_ini" readonly disabled placeholder="Otomatis terisi" style="background: #f3f4f6; cursor: not-allowed;">
                </label>

                <label class="field">
                    <span>Jenis Kelamin <b>*</b></span>
                    <select name="jenis_kelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field">
                    <span>Pendidikan Terakhir <b>*</b></span>
                    <select name="pendidikan_terakhir" required>
                        <option value="">Pilih Pendidikan</option>
                        <option value="Tidak Sekolah" {{ old('pendidikan_terakhir') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                        <option value="SD" {{ old('pendidikan_terakhir') == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ old('pendidikan_terakhir') == 'SMA' ? 'selected' : '' }}>SMA / Sederajat</option>
                        <option value="D3" {{ old('pendidikan_terakhir') == 'D3' ? 'selected' : '' }}>Diploma (D1/D2/D3)</option>
                        <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>Sarjana (S1)</option>
                        <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                        <option value="S3" {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>Doktor (S3)</option>
                    </select>
                    @error('pendidikan_terakhir')<small>{{ $message }}</small>@enderror
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
        const pekerjaanInput = document.getElementById('pekerjaan');
        const pekerjaanLainnyaBox = document.getElementById('pekerjaan_lainnya_box');
        const birthDateInput = document.getElementById('tanggal_lahir');
        const ageInput = document.getElementById('umur_saat_ini');

        function calculateAge() {
            if (!birthDateInput.value) {
                ageInput.value = '';
                return;
            }
            const birthDate = new Date(birthDateInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageInput.value = age >= 0 ? age + ' Tahun' : '0 Tahun';
        }

        birthDateInput.addEventListener('change', calculateAge);
        birthDateInput.addEventListener('input', calculateAge);
        if (birthDateInput.value) {
            calculateAge();
        }

        function togglePekerjaanLainnya() {
            const isOther = pekerjaanInput.value === 'Lainnya';
            pekerjaanLainnyaBox.classList.toggle('hidden', !isOther);
            const otherInput = pekerjaanLainnyaBox.querySelector('input');
            if (otherInput) { 
                otherInput.required = isOther; 
                if (!isOther) otherInput.value = ''; 
            }
        }

        pekerjaanInput.addEventListener('change', togglePekerjaanLainnya);
        togglePekerjaanLainnya();

        // Realtime Clock Functionality
        function updateDateTime() {
            const now = new Date();
            const locale = 'id-ID';
            const day = new Intl.DateTimeFormat(locale, { weekday: 'long' }).format(now);
            const date = new Intl.DateTimeFormat(locale, { day: '2-digit', month: 'long', year: 'numeric' }).format(now);
            const dateEl = document.getElementById('current-date-text');
            if (dateEl) dateEl.textContent = day + ', ' + date;
            
            const time = new Intl.DateTimeFormat(locale, { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(now).replace(/\./g, ':');
            const timeEl = document.getElementById('current-time');
            if (timeEl) timeEl.textContent = time + ' WIB';
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</x-layouts.public>
