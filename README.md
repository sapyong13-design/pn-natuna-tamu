# pn-natuna-tamu

Aplikasi portal tamu Pengadilan Negeri Natuna untuk subdomain `tamu.pn-natuna.go.id`.

## Stack
Laravel 12, PHP minimal 8.2, MySQL/MariaDB, Filament 3, Laravel Excel/PhpSpreadsheet, Blade + Tailwind/Vite. Node.js hanya dipakai saat build asset, bukan runtime server.

## Fitur
- Landing page: “Selamat Datang di Pengadilan Negeri Natuna”.
- Buku tamu publik mobile friendly.
- Link survey dikelola admin dari tabel `survey_links`.
- Jadwal sidang lokal/cache, input manual dari admin. Aplikasi tidak menulis/mengubah database SIPP.
- Filament Admin: Guests, Jadwal Sidang, Survey Links, Sync Logs, dashboard statistik.
- Export Excel data tamu dengan filter tanggal dan jenis layanan.

## 1. Cara build lokal
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```
Admin: `/mimin`.
Seeder membuat admin awal dengan username `admin` dan password `123456`.

## 2. Cara upload ke cPanel
Upload seluruh isi project ke:

```text
/home/USERNAME/pn-natuna-tamu
```

Jangan upload seluruh project ke `public_html`. Hanya folder `public` yang boleh menjadi document root publik.

## 3. Cara membuat database MySQL
Di cPanel:
1. Buka **MySQL Database Wizard**.
2. Buat database, contoh `USERNAME_pn_natuna_tamu`.
3. Buat user database.
4. Beri hak akses **All Privileges** ke user tersebut.

## 4. Cara isi `.env` production
Buat file `.env` dari `.env.example`, lalu sesuaikan:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tamu.pn-natuna.go.id
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=USERNAME_pn_natuna_tamu
DB_USERNAME=USERNAME_dbuser
DB_PASSWORD=password_database
ADMIN_EMAIL=admin@pn-natuna.go.id
ADMIN_PASSWORD=password_awal_yang_kuat
```

Jangan hardcode credential di kode aplikasi.

## 5. Set document root subdomain
Di cPanel > Domains/Subdomains, arahkan document root `tamu.pn-natuna.go.id` ke:

```text
/home/USERNAME/pn-natuna-tamu/public
```

Bukan ke `/home/USERNAME/pn-natuna-tamu` dan bukan ke `public_html`.

## 6. Composer production
Jalankan dari folder project:

```bash
cd /home/USERNAME/pn-natuna-tamu
composer install --no-dev --optimize-autoloader
```

Jika cPanel menyediakan menu **Terminal**, gunakan terminal. Jika tidak, jalankan lokal lalu upload folder `vendor`.

## 7. Generate APP_KEY
```bash
php artisan key:generate
```

## 8. Migrasi dan seeder
```bash
php artisan migrate --seed
```

Seeder mengisi link survey default, dummy jadwal sidang hari ini, dan admin awal.

## 9. Storage link
Jika memakai file publik di storage:

```bash
php artisan storage:link
```

Untuk aplikasi ini tidak wajib kecuali Anda menambah fitur upload.

## 10. Cache production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Setelah mengubah `.env`, jalankan ulang `php artisan config:cache`.

## 11. Cron cPanel Laravel Scheduler
Tambahkan Cron Job cPanel:

```bash
* * * * * cd /home/USERNAME/pn-natuna-tamu && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Sesuaikan path PHP jika berbeda. Aplikasi saat ini tidak bergantung pada scheduler, tetapi cron siap untuk pengembangan sinkronisasi/cache mendatang.

## 12. Troubleshoot

### Error 500
- Pastikan `.env` ada.
- Pastikan `APP_KEY` sudah dibuat.
- Set `APP_DEBUG=true` sementara untuk diagnosis, lalu kembalikan `false`.
- Cek `storage/logs/laravel.log`.

### Permission storage/cache
Pastikan folder berikut bisa ditulis oleh PHP/cPanel:

```bash
chmod -R 775 storage bootstrap/cache
```

Jika shared hosting tidak mengizinkan `775`, gunakan File Manager cPanel untuk mengatur permission sesuai konfigurasi hosting.

### APP_KEY kosong
Jalankan:

```bash
php artisan key:generate
php artisan config:cache
```

### Asset/CSS tidak tampil
Jalankan lokal:

```bash
npm install
npm run build
```

Pastikan folder `public/build` ikut terupload.

### Database gagal konek
- Cek `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- Di cPanel biasanya nama database/user memakai prefix `USERNAME_`.
- `DB_HOST` biasanya `localhost`.


## Sinkron jadwal sidang SIPP

Aplikasi membaca halaman publik jadwal sidang SIPP PN Natuna dari:

```text
https://sipp.pn-natuna.go.id/list_jadwal_sidang
```

Data disimpan ke tabel lokal `jadwal_sidangs` sebagai cache. Aplikasi tidak menulis/mengubah database SIPP.

Jalankan manual:

```bash
php artisan sipp:sync-jadwal
```

Scheduler menjalankan sinkronisasi setiap hari pukul 06:00 jika cron cPanel Laravel scheduler aktif.

## Route publik
- `GET /` (Halaman Utama)
- `GET /buku-tamu` (Form Buku Tamu)
- `POST /buku-tamu` (Simpan Buku Tamu)
- `GET /buku-tamu/selesai/{kode_kunjungan}` (Halaman Sukses)
- `GET /cek` (Monitoring Jadwal & Kehadiran Sidang Hari Ini)

## Catatan keamanan
- Form memakai CSRF dan Form Request validation.
- Admin wajib login via Filament.
- Data tamu tidak ditampilkan publik.
- Link survey disimpan di database, bukan hardcode.
