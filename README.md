# ⚽ Sistem Informasi Reservasi Lapangan Futsal Berbasis Web
### Futsal 35 — Bandung

![Version](https://img.shields.io/badge/version-1.0-green)
![Status](https://img.shields.io/badge/status-in%20development-yellow)
![Platform](https://img.shields.io/badge/platform-Web-blue)
![Metodologi](https://img.shields.io/badge/metodologi-Agile--Scrum-orange)
![Sprint](https://img.shields.io/badge/sprint-18%20minggu-purple)

Sistem informasi berbasis **web** untuk mendukung operasional penyewaan lapangan futsal di **Futsal 35, Bandung**. Sistem ini menggantikan proses reservasi manual (WhatsApp DM & kunjungan langsung) dengan platform digital yang memungkinkan pelanggan memesan lapangan secara online, real-time, dan terstruktur.

> 📄 Referensi: Usulan Teknis (USTEK) v1.0 & KAK v1.0 — Maret 2026  
> 🏢 Klien: **Futsal 35** | PPKom: **Agus Mulyana**

---

## 👥 Tim Pengembang — Kelompok 4

| No | Nama | NIM | Peran |
|----|------|-----|-------|
| 1 | M. Fauzan Alfikri S Putra | 20241320009 | 📋 Project Manager |
| 2 | Pajar | 20241320026 | 🔍 System Analyst |
| 3 | Lulu Aeni Salsabila | 20241320008 | 🎨 Frontend Developer |
| 4 | Sobur | 20241320046 | ⚙️ Backend Developer |
| 5 | Sona Mardiana | 20241320029 | 🧪 QA & Technical Writer |

> Program Studi Sistem Informasi — Fakultas Ilmu Komputer dan Sistem Informasi  
> Universitas Kebangsaan Republik Indonesia, 2026

---

## 📖 Latar Belakang

Futsal 35 saat ini mengandalkan **WhatsApp DM** dan **kunjungan langsung** untuk proses reservasi. Keterbatasan metode ini antara lain:

- ❌ Tidak ada sistem pencatatan terpusat
- ❌ Jadwal tidak dapat dipantau secara real-time
- ❌ Rentan terhadap human error & double booking
- ❌ Proses lambat dan tidak transparan bagi pelanggan

Sistem ini hadir untuk menyelesaikan masalah tersebut dengan digitalisasi penuh proses reservasi.

---

## 🚀 Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🔐 Autentikasi | Login/register pengguna, role-based access (Admin & User) |
| 📅 Sistem Reservasi | Booking lapangan secara online dengan validasi jadwal real-time |
| 🗓️ Manajemen Jadwal | Kelola ketersediaan lapangan, jam operasional |
| 💳 Transaksi | Pencatatan pembayaran dan riwayat reservasi |
| 📊 Dashboard Admin | Monitoring booking, laporan penjualan, statistik penggunaan |
| 🔒 Anti Double Booking | Validasi otomatis agar tidak terjadi bentrok jadwal |

---

## 🛠️ Tech Stack

```
Frontend     : HTML, CSS, JavaScript
Backend      : PHP — Laravel
Database     : MySQL / Oracle
Web Server   : Apache / Nginx
Cache        : Redis
Version Ctrl : Git
```

### Arsitektur Sistem (Three-Tier)

```
┌─────────────────────────────────────────┐
│        Presentation Layer               │
│   HTML + CSS + JS (Browser)             │
└─────────────────┬───────────────────────┘
                  │ HTTP/HTTPS
┌─────────────────▼───────────────────────┐
│        Application Layer                │
│   Laravel — Logika Bisnis, API,         │
│   Proses Booking, Validasi              │
└─────────────────┬───────────────────────┘
                  │ Eloquent ORM
┌─────────────────▼───────────────────────┐
│           Data Layer                    │
│      MySQL — Penyimpanan Data           │
└─────────────────────────────────────────┘
```

---

## 🗄️ Struktur Database

| Tabel | Kolom Utama | Fungsi |
|-------|-------------|--------|
| `users` | id, nama, email, password, role | Data pengguna sistem |
| `lapangan` | id, nama, harga, deskripsi | Data lapangan futsal |
| `jadwal` | id, lapangan_id, waktu, status | Ketersediaan jadwal |
| `booking` | id, user_id, jadwal_id, status | Data reservasi |
| `transaksi` | id, booking_id, total, metode_bayar | Data pembayaran |

---

## 🔒 Keamanan Sistem

| Aspek | Implementasi |
|-------|-------------|
| Autentikasi Admin | Login email & password dengan Session/JWT |
| Enkripsi Password | Hashing menggunakan Bcrypt |
| Validasi Input | Server-side validation pada semua form |
| Protokol | HTTPS dengan SSL/TLS |
| Proteksi API | Endpoint dilindungi token autentikasi |
| Role Access | Role-Based Access Control (Admin & User) |
| Backup | Backup otomatis harian dan mingguan |

---

## 🔄 Metodologi: Agile-Scrum

Proyek dikembangkan menggunakan **Agile-Scrum** dengan durasi **±18 minggu** (9 sprint × 2 minggu).

### Rencana Sprint

| Sprint | Minggu | Fokus Pekerjaan | PIC |
|--------|--------|-----------------|-----|
| Sprint 0 | 1–2 | Analisis kebutuhan, observasi lapangan, product backlog | PM + Analyst |
| Sprint 1 | 3–4 | Perancangan sistem, ERD, desain UI/UX | Analyst + Dev |
| Sprint 2 | 5–6 | Fitur autentikasi (login, register) & manajemen user | Backend + Frontend |
| Sprint 3 | 7–8 | Sistem reservasi lapangan (booking system) | Backend + Frontend |
| Sprint 4 | 9–10 | Manajemen jadwal & ketersediaan lapangan | Backend |
| Sprint 5 | 11–12 | Dashboard admin & laporan transaksi | Backend + Frontend |
| Sprint 6 | 13–14 | Pengujian sistem, debugging, optimasi performa | QA + Tim |
| Sprint 7 | 15–16 | Deployment ke server produksi & pelatihan pengguna | PM + Tim |
| Sprint 8 | 17–18 | Monitoring, evaluasi, pengembangan fitur lanjutan | PM + QA |

### Sprint Ceremonies

- **Sprint Planning** — awal tiap sprint, menyusun sprint backlog
- **Daily Standup** — 3× seminggu via WhatsApp/Google Meet
- **Sprint Review** — akhir sprint, demo hasil ke stakeholder
- **Sprint Retrospective** — evaluasi internal tim

---

## 🧪 Rencana Pengujian

| Jenis Pengujian | Pelaksana | Waktu | Metode |
|-----------------|-----------|-------|--------|
| Unit Testing | Backend Developer | Tiap sprint | Fungsi login, booking, transaksi |
| Integration Testing | Backend + Frontend | Sprint 5–6 | Integrasi fitur booking & jadwal |
| UI Testing | Frontend | Sprint 5–6 | Tampilan & responsivitas website |
| UAT | QA + User | Sprint 6 | Simulasi booking oleh pengguna |
| Security Testing | QA | Sprint 6 | Keamanan login & perlindungan data |
| Performance Testing | QA | Sprint 6 | Uji akses banyak pengguna serentak |

### Kriteria Penerimaan (Acceptance Criteria)
- ✅ Pelanggan dapat reservasi lapangan online tanpa kendala
- ✅ Jadwal tampil secara real-time dan akurat
- ✅ Tidak terjadi double booking pada jadwal yang sama
- ✅ Sistem dapat diakses melalui smartphone dan desktop
- ✅ Data reservasi dan transaksi tersimpan dengan benar
- ✅ Waktu respons sistem **≤ 3 detik**

---

## 🖥️ Infrastruktur & Hosting

| Komponen | Spesifikasi |
|----------|-------------|
| Cloud Server | 8 vCPU, 32 GB RAM, 1 TB SSD |
| OS | Linux (Ubuntu Server) |
| Web Server | Nginx |
| Database | MySQL / Oracle |
| Cache | Redis |
| Estimasi Biaya | Rp 15.000.000 – 30.000.000 / bulan |

---

## 📦 Instalasi & Setup

### Prasyarat
- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM
- Git

### Langkah Instalasi

```bash
# Clone repositori
git clone https://github.com/username/futsal35-reservation.git
cd futsal35-reservation

# Install dependensi PHP
composer install

# Salin dan konfigurasi environment
cp .env.example .env
php artisan key:generate

# Sesuaikan konfigurasi database di file .env
# DB_DATABASE=futsal35
# DB_USERNAME=root
# DB_PASSWORD=yourpassword

# Jalankan migrasi & seeding database
php artisan migrate --seed

# Install dependensi frontend
npm install && npm run build

# Jalankan server lokal
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 📁 Struktur Repositori

```
futsal35-reservation/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php       # Login & Register
│   │   ├── BookingController.php    # Sistem Reservasi
│   │   ├── JadwalController.php     # Manajemen Jadwal
│   │   ├── TransaksiController.php  # Pembayaran
│   │   └── AdminController.php      # Dashboard Admin
│   ├── Models/
│   └── Services/
├── resources/
│   ├── views/                       # Template HTML (Blade)
│   └── js/                          # Komponen Frontend
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── web.php
├── tests/                           # Unit & Feature Tests (Sona)
├── docs/                            # Dokumentasi Teknis
│   ├── USTEK_v1.0.pdf
│   ├── SRS.md
│   └── user_manual.md
└── README.md
```

---

## 💰 Rincian Biaya Proyek

| No | Komponen | Total (Rp) |
|----|----------|-----------|
| 1 | Analisis & Perancangan Sistem | 10.000.000 |
| 2 | Pengembangan Aplikasi | 35.000.000 |
| 3 | Infrastruktur & Hosting (1 tahun) | 30.000.000 |
| 4 | Pelatihan Pengguna | 10.000.000 |
| 5 | Dokumentasi Sistem | 10.000.000 |
| 6 | Pengujian & Implementasi | 15.000.000 |
| 7 | Pemeliharaan Tahun Pertama | 10.000.000 |
| | **TOTAL INVESTASI** | **Rp 120.000.000** |

---

## 🤝 Koordinasi Tim

| Kegiatan | Frekuensi | Media |
|----------|-----------|-------|
| Sprint Planning | Awal setiap sprint | Google Meet |
| Daily Standup | 3× seminggu | WhatsApp / Google Meet |
| Code Review | Setiap update kode | GitHub |
| Sprint Review | Akhir setiap sprint | Demo sistem |
| Retrospective | Akhir setiap sprint | Evaluasi tim |

---

## 📝 Dokumentasi

- 📄 [Usulan Teknis (USTEK) v1.0](docs/USTEK_v1.0.pdf)
- 📘 User Manual — *coming soon*
- 📋 SRS — *coming soon*

---

*© 2026 Kelompok 4 — Sistem Informasi UKRI | Proyek: Futsal 35, Bandung*