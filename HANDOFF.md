# HANDOFF.md - Portal Tamu PN Natuna Kelas II

Dokumen ini adalah handoff resmi untuk developer/AI agent berikutnya agar bisa langsung memahami status aplikasi `pn-natuna-tamu` tanpa membaca seluruh riwayat percakapan.

---

## 1. Identitas Project

- **Nama aplikasi:** Portal Layanan Tamu Pengadilan Negeri Natuna Kelas II
- **Folder lokal:** `C:\Users\faris\pn-natuna-tamu`
- **Target production:** `https://tamu.pn-natuna.go.id`
- **Stack:** Laravel 12, Filament 3, Livewire, Tailwind CSS/Vite
- **Database lokal:** SQLite (`database/database.sqlite`)
- **Database production:** MySQL cPanel/IDwebhost
- **PHP production:** gunakan PHP minimal 8.3; di IDwebhost/cPanel saat ini diarahkan dengan PHP 8.4:
  ```bash
  /opt/cpanel/ea-php84/root/usr/bin/php
  ```

---

## 2. URL dan Akun Admin

- **URL admin:** `/mimin`
- **Contoh production:** `https://tamu.pn-natuna.go.id/mimin`
- **Username/email default:** `admin`
- **Password default:** `123456`
- Login Filament sudah dikustom agar menerima username biasa (`admin`), bukan wajib format email.
- User diminta mengganti password sendiri lewat panel admin setelah login.

---

## 3. Route Publik Penting

- `GET /` - halaman utama/landing
- `GET /buku-tamu` - form buku tamu
- `POST /buku-tamu` - simpan data tamu
- `GET /buku-tamu/selesai/{kode_kunjungan}` - halaman sukses
- `GET /cek` - monitoring jadwal sidang dan kehadiran tamu
- `GET /cek?tanggal=YYYY-MM-DD` - monitoring berdasarkan tanggal tertentu

Catatan: URL monitoring sudah dipindah dari `/buku-tamu/cek` menjadi `/cek`. View fisiknya juga sudah dipindah menjadi `resources/views/cek.blade.php`.

---

## 4. Fitur yang Sudah Selesai

### Landing Page
- Tema hijau pengadilan dengan background gedung PN Natuna.
- Logo PN Natuna di kiri atas, logo BerAKHLAK di kanan atas.
- Logo dibuat transparan/containerless di atas background gelap.
- Widget jam WIB real-time dengan warna amber/emas.
- Tombol layanan untuk isi buku tamu, cek tamu, dan survei.

### Buku Tamu
- Tujuan kunjungan dikunci ke `Menghadiri Sidang`.
- Semua input wajib kecuali `keperluan`.
- Peran sidang:
  - Para Pihak
  - Saksi
  - Pengunjung
  - Ahli
- Input pekerjaan memakai autocomplete kustom.
- Jika memilih `Lainnya`, muncul field pekerjaan lainnya.

### Monitoring `/cek`
- Bisa memilih tanggal sidang dengan date picker.
- Default menampilkan hari ini.
- Jika memilih tanggal lain, subtitle tanggal tampil dalam Bahasa Indonesia, contoh `JUMAT, 26 JUNI 2026`.
- Menampilkan kartu sidang berisi:
  - Nomor perkara
  - Jenis perkara
  - Para pihak
  - Agenda
  - Ruang sidang
  - Status kehadiran Para Pihak, Saksi, Ahli, Pengunjung
- Jika tidak ada ahli, status khusus: `Belum Hadir / Tidak Ada`.
- Jika tidak ada jadwal pada tanggal terpilih, tampil empty state.

---

## 5. Sinkronisasi SIPP

Command utama:

```bash
php artisan sipp:sync-jadwal
```

Sync tanggal tertentu:

```bash
php artisan sipp:sync-jadwal --date=2026-06-26
```

Di cPanel/IDwebhost gunakan PHP 8.4 eksplisit:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan sipp:sync-jadwal
/opt/cpanel/ea-php84/root/usr/bin/php artisan sipp:sync-jadwal --date=2026-06-26
```

Data yang diambil dari SIPP:

- Tanggal sidang
- Nomor perkara
- Agenda sidang
- Ruang sidang
- Jenis perkara
- Para pihak/terdakwa/penggugat/tergugat/pemohon/termohon jika tersedia

Detail perkara diambil dari endpoint SIPP `detil_jadwal_sidang/{kode}` yang ditemukan dari tombol `Detil` pada halaman jadwal sidang.

### Format Para Pihak

- Jika SIPP menulis `Tidak dipublikasikan`, nilai itu tetap ditampilkan, bukan disembunyikan.
- Jika pihak lebih dari satu, format dibuat rapi agar nomor 2 berada di bawah nomor 1 di tampilan.
- Contoh data 26 Juni 2026:

```text
Nomor Perkara : 5/Pdt.G/2026/PN Ntn
Jenis Perkara : Perbuatan Melawan Hukum
Pihak         : 1. PT. Bank Perekonomian Rakyat Natuna
                2. Hardianto
Agenda        : Pengajuan Duplik dari Penggugat secara Elektronik
Ruang Sidang  : Ruang Sidang Cakra
```

### Pencegahan Duplikasi

Command sync sudah memakai `whereDate` saat mencari jadwal, supaya tidak membuat duplikasi akibat perbedaan format tanggal SQLite/MySQL seperti `2026-06-25` vs `2026-06-25 00:00:00`.

---

## 6. Database dan Migration

Migration penting:

```text
database/migrations/2026_06_25_000000_add_jenis_perkara_to_jadwal_sidangs_table.php
```

Migration tersebut menambahkan kolom:

```text
jenis_perkara
```

Kolom `para_pihak` sudah ada dan digunakan untuk menyimpan pihak dari SIPP.

Di production, jalankan:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --seed --force
```

Jangan pernah menjalankan ini di production:

```bash
php artisan migrate:fresh
```

Karena akan menghapus semua data.

---

## 7. Deployment cPanel/IDwebhost

### Struktur Folder Aman

Letakkan project di luar `public_html`:

```text
/home/pnnatuna/pn-natuna-tamu
```

Document root subdomain `tamu.pn-natuna.go.id` harus diarahkan ke:

```text
/home/pnnatuna/pn-natuna-tamu/public
```

### Setup `.env`

Pastikan file `.env` ada dan memiliki baris:

```env
APP_NAME="Portal Tamu PN Natuna"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tamu.pn-natuna.go.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pnnatuna_nama_database
DB_USERNAME=pnnatuna_nama_user
DB_PASSWORD=password_database
```

Jika `APP_KEY` belum ada:

```bash
cp .env.example .env
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan key:generate
```

Jika error database `root@localhost`, berarti `.env` masih memakai default Laravel. Isi DB harus diganti sesuai MySQL cPanel.

### Setup Production via SSH

```bash
cd /home/pnnatuna/pn-natuna-tamu

/opt/cpanel/ea-php84/root/usr/bin/php $(which composer) install --no-dev --optimize-autoloader

/opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan key:generate
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --seed --force
/opt/cpanel/ea-php84/root/usr/bin/php artisan sipp:sync-jadwal

/opt/cpanel/ea-php84/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:cache
```

Jika SSH command `php` default masih PHP 7.4, jangan pakai `php artisan`; selalu gunakan:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan ...
```

---

## 8. Cron Job SIPP

Laravel scheduler sudah mengatur sync otomatis setiap hari pukul 06.00.

Di cPanel > Cron Jobs, buat cron setiap menit:

```text
* * * * *
```

Command:

```bash
cd /home/pnnatuna/pn-natuna-tamu && /opt/cpanel/ea-php84/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Cron cPanel berjalan setiap menit, tetapi sync SIPP hanya dijalankan Laravel sesuai jadwal internal.

---

## 9. Update/Fix di Masa Depan Tanpa Hilang Data

Jika hanya mengubah file PHP/Blade:
- Upload file yang berubah saja ke cPanel.
- Jangan upload ulang database.

Jika mengubah CSS/JS:

```bash
npm run build
```

Lalu upload folder:

```text
public/build/
```

Setelah update file, jalankan:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan cache:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear

/opt/cpanel/ea-php84/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:cache
```

Jika ada migration baru:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force
```

Data aman selama tidak menjalankan `migrate:fresh` atau menghapus database MySQL.

---

## 10. Catatan Visual/UX Penting

- Logo PN Natuna dan BerAKHLAK harus tetap transparan/containerless di atas background.
- Jangan pakai container putih untuk logo.
- Favicon memakai `public/images/logo-pn-natuna-emblem.png`.
- Background memakai `body::before` dengan akselerasi GPU.
- Hindari `backdrop-filter` berat pada kontainer besar agar scroll tetap ringan.
- Ruang Sidang Cakra tetap berada di pojok kanan atas kartu monitoring, jangan dipindahkan ke area para pihak.
- Para pihak lebih dari satu harus tampil bertingkat: nomor 2 di bawah nomor 1.

---

## 11. Status Terakhir

- Aplikasi lokal berjalan di `http://127.0.0.1:8000`.
- Monitoring `/cek` sudah mendukung filter tanggal.
- Sync SIPP hari ini dan tanggal tertentu sudah bisa.
- Admin login sudah di `/mimin` dengan username `admin`.
- Production cPanel/IDwebhost membutuhkan PHP 8.3+; gunakan PHP 8.4 path eksplisit.
- Handoff ini dibuat untuk memudahkan perpindahan aplikasi/agent/developer.
