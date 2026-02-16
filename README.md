# Sistem Kepegawaian (SIPEG)

Aplikasi web untuk mengelola kepegawaian, absensi, lembur, cuti/izin, dan penggajian (TPP). Dibangun dengan Laravel dan mendukung verifikasi absensi via **face recognition**.

---

## Persyaratan

- **PHP** ≥ 8.2  
- **Composer**  
- **Node.js** & **npm** (untuk asset frontend)  
- **Database**: SQLite (default) atau MySQL  

---

## Instalasi

```bash
# Install dependency PHP
composer install

# set environment
php artisan key:generate
php artisan migrate:fresh --seed

# Install dependency frontend & build
npm install
npm run build

# Jalankan server
php artisan serve
```

Buka **http://localhost:8000** di browser.

---

## Fitur

### Umum
- **Login / Logout** — Autentikasi dengan role: **Admin** dan **Pegawai**.

### Role: Pegawai
- **Profil** — Lihat dan ubah profil, ubah password.
- **Absensi harian** — Absen masuk, pulang, dan lembur (dengan konfirmasi admin).
- **Verifikasi wajah** — Enroll wajah dan absen dengan face recognition.
- **Konfirmasi absen** — Ajukan konfirmasi jika absen belum terekam/diubah.
- **Cuti / Izin** — Pengajuan cuti dan izin (sakit / tidak masuk).
- **Laporan** — Absen bulanan, laporan TPP bulanan, cetak slip gaji.

### Role: Admin
- **Master Jabatan** — CRUD jabatan.
- **Master Pegawai** — CRUD data pegawai.
- **Master Kantor** — CRUD kantor/lokasi; set kantor aktif untuk absensi.
- **Lembur** — Input, edit, hapus data lembur pegawai.
- **Konfirmasi absen** — Setujui/tolak absen masuk, pulang, lembur, izin sakit, izin tidak masuk.
- **Akun pegawai** — Reset password pegawai.
- **Laporan** — Absen bulanan, lembur bulanan, TPP bulanan; cetak per pegawai.
- **Payroll / Gaji** — Akumulasi gaji, simpan, edit, hapus data gaji.

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2  
- **Frontend:** Blade, Tailwind CSS 4, Vite  
- **Database:** SQLite / MySQL  
- **Fitur tambahan:** Face recognition (enrollment & verifikasi) untuk absensi  

---

## Konfigurasi

- **Database:** Atur di `.env` (`DB_CONNECTION`, `DB_DATABASE`, dll). Default: SQLite.
- **APP_URL:** Sesuaikan dengan URL aplikasi (penting untuk link dan session).

---

## Lisensi

MIT (atau sesuai kebijakan project).
