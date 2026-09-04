# Absensi PKL — Laravel

Versi Laravel dari project "Absensi PKL" (aslinya HTML/CSS/JS + localStorage).
Sekarang datanya tersimpan di database MySQL, dan ada login terpisah untuk **siswa** dan **admin**.

## Fitur

- Login & registrasi siswa (akun admin dibuat lewat seeder, tidak lewat form publik).
- Siswa: absen Masuk/Pulang otomatis (tombol berubah sesuai status hari itu), rekam titik lokasi (geolocation), lihat riwayat pribadi.
- Admin: rekap semua absensi seluruh siswa, dengan filter nama & tanggal, plus link "Lihat" lokasi di Google Maps.
- Tampilan & animasi (glassmorphism, dark/light mode, jam real-time) di-port dari desain aslinya.

## Struktur Database

- `users` — kolom `role` (`admin` / `siswa`).
- `absensis` — `user_id`, `type` (`Masuk`/`Pulang`), `tanggal`, `jam`, `latitude`, `longitude`.

## Cara Menjalankan

1. **Install dependency** (butuh PHP 8.2+ & Composer):
   ```bash
   composer install
   ```

2. **Salin file environment**:
   ```bash
   cp .env.example .env
   ```

3. **Buat database MySQL** kosong, misalnya `absensi_pkl`, lalu sesuaikan kredensial di `.env`:
   ```
   DB_DATABASE=absensi_pkl
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi & seed** (seed akan membuat 1 akun admin default):
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan server**:
   ```bash
   php artisan serve
   ```
   Buka `http://localhost:8000`.

## Akun Default

Setelah `migrate --seed`, akun admin default:

- Email: `admin@pkl.test`
- Password: `password`

Akun siswa dibuat sendiri lewat halaman **Daftar** (`/register`).

> Disarankan ganti password admin default setelah login pertama kali (belum ada fitur ganti password di versi ini — bisa ditambah lewat `php artisan tinker` atau membuat halaman profil).

## Catatan

- Tidak ada langkah build frontend (Vite/npm) — CSS & JS disajikan langsung dari folder `public/css` dan `public/js`, jadi tidak perlu `npm install`.
- Folder `vendor/` sengaja tidak disertakan (di-generate oleh `composer install`).
- Kalau mau pakai SQLite saat development, ganti `.env`:
  ```
  DB_CONNECTION=sqlite
  DB_DATABASE=/path/absolut/ke/database.sqlite
  ```
  lalu buat file kosongnya: `touch database/database.sqlite`.
