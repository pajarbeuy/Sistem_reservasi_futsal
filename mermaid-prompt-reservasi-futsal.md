# Prompt Mermaid untuk Diagram Sistem Reservasi Futsal

```text
Buat diagram Mermaid untuk aplikasi reservasi futsal berbasis Laravel 13 + Inertia.js + Vue 3 + Tailwind CSS.

Konteks proyek:
- Backend: Laravel 13, PHP 8.3
- Frontend: Vue 3 + Inertia.js + Vite + Tailwind CSS
- Pola arsitektur: Route -> Middleware -> Controller -> Service -> Model
- Fitur utama:
  1. Auth: register, login, logout, refresh token, user profile, email verification, reset password
  2. Field/Lapangan: list, detail, create, update, delete
  3. Schedule/Jadwal: lihat jadwal lapangan dan ketersediaan slot
  4. Booking: buat booking, lihat booking, batalkan booking
  5. Payment: buat pembayaran, integrasi Midtrans, update status pembayaran
  6. Profile: edit profil, update password, delete account
  7. Admin: role/permission, middleware is_admin, dashboard admin
- Entitas domain utama:
  - User
  - Field/Lapangan
  - Booking
  - Payment
  - Price
  - Schedule/Jadwal
  - Role/Permission
- Frontend pages utama:
  - LandingPage, Welcome, Tentang, Harga, Lapangan, LapanganDetail, Jadwal, BookingForm, Bookings, UserDashboard, Dashboard, Profile pages, Auth pages
- Komponen utama:
  - LapanganCard, JadwalGrid, HargaCard, BookingModal, Navbar/layout, footer, form inputs, buttons
- Sistem pembayaran:
  - Midtrans snap/token callback atau proses payment controller
- Gunakan nama kelas/controller/komponen yang sesuai domain proyek ini.

Buat output dalam 5 bagian Mermaid berikut:

1) UML Class Diagram
- Tampilkan class utama backend:
  - app/Models/User
  - app/Models/Field
  - app/Models/Booking
  - app/Models/Payment
  - app/Models/Price
  - app/Http/Controllers/Api/AuthController
  - app/Http/Controllers/Api/FieldController
  - app/Http/Controllers/Api/BookingController
  - app/Http/Controllers/Api/PaymentController
  - app/Http/Controllers/Api/ScheduleController
  - app/Http/Controllers/Api/PriceController
  - app/Http/Controllers/Api/VerificationController
  - app/Http/Controllers/MidtransPaymentController
  - middleware IsAdmin
- Tampilkan relasi antar model:
  - User hasMany Bookings
  - User hasMany Payments
  - Field hasMany Prices
  - Field hasMany Bookings
  - Booking belongsTo User
  - Booking belongsTo Field
  - Booking hasOne Payment
  - Payment belongsTo Booking
  - Price belongsTo Field
- Sertakan atribut inti minimal untuk tiap model:
  - User: id, name, email, password, role
  - Field: id, name, slug, description, image, status
  - Booking: id, user_id, field_id, booking_date, start_time, end_time, status, total_price
  - Payment: id, booking_id, amount, method, status, midtrans_order_id, snap_token, paid_at
  - Price: id, field_id, duration, price, is_active
- Tambahkan dependensi controller ke model/service jika relevan.
- Output harus valid Mermaid `classDiagram`.

2) Sequence Diagram per Fitur
Buat beberapa `sequenceDiagram` terpisah untuk fitur berikut:
- Login/Register/Auth flow
- Browse lapangan dan lihat detail lapangan
- Cek jadwal ketersediaan
- Buat booking
- Proses pembayaran Midtrans
- Batalkan booking
- Admin kelola lapangan
- Admin kelola harga
- Update profil user
- Reset password / verifikasi email

Untuk setiap sequence:
- Tampilkan actor `User`, `Admin`, `Frontend Vue/Inertia`, `Laravel Controller`, `Service` jika ada, `Model`, dan `Midtrans` jika terkait.
- Gunakan alur request/response yang realistis sesuai aplikasi Laravel.
- Tampilkan validasi, pengecekan ketersediaan, penyimpanan database, dan redirect/response.
- Untuk payment flow, masukkan pembuatan order, pembuatan snap token, dan callback/update status.
- Untuk booking flow, masukkan pengecekan slot jadwal agar tidak double booking.
- Output harus valid Mermaid `sequenceDiagram`.
- Jika terlalu panjang, pisahkan menjadi beberapa blok sequence diagram dengan judul fitur.

3) Component Diagram
Buat `flowchart LR` atau `graph LR` yang memetakan komponen aplikasi:
- Browser/User
- Vue Pages
- Vue Components
- Inertia bridge
- Laravel routes
- Middleware
- Controllers
- Services
- Models
- Database
- Midtrans API
- Auth/JWT/Sanctum jika digunakan
- Role/Permission layer
- Storage/public assets
Tampilkan hubungan antar komponen dari UI sampai database dan payment gateway.
Gunakan cluster/subgraph untuk:
- Frontend
- Backend
- External services
- Data layer
Output harus valid Mermaid dan mudah dibaca.

4) Arsitektur Diagram
Buat diagram arsitektur aplikasi dari level tinggi:
- Client layer
- Presentation layer
- Application/API layer
- Domain layer
- Infrastructure layer
- External payment gateway
- Database
- File storage
Tampilkan jalur utama:
- User -> Vue/Inertia -> Laravel Routes -> Middleware -> Controllers -> Services -> Models -> Database
- Booking/payment flow -> Midtrans
- Admin flow -> IsAdmin middleware -> CRUD controllers
Gunakan Mermaid `flowchart TB` atau diagram yang setara.
Tampilkan juga area frontend dan backend sebagai dua domain besar.

5) Arsitektur Sistem
Buat diagram sistem end-to-end yang menjelaskan:
- User dan Admin sebagai actor
- Web app frontend
- Laravel backend
- Auth/Session/JWT
- Database MySQL
- Storage
- Midtrans
- Email service untuk verifikasi/reset password
- Dashboard admin
- Public assets/landing page
- API endpoints untuk booking, payment, schedule, field, price
Gunakan level detail yang cukup untuk menjelaskan sistem produksi.
Tambahkan label pada koneksi yang menjelaskan data yang mengalir, misalnya:
- login credentials
- booking request
- schedule data
- payment token
- payment callback
- profile update
- verification email
- CRUD field/price
Output harus valid Mermaid `flowchart TB`.

Aturan tambahan:
- Gunakan nama feature dan class yang konsisten dengan domain reservasi futsal.
- Jangan membuat diagram abstrak yang terlalu generik.
- Jangan menggabungkan semua diagram menjadi satu; buat 5 blok output terpisah.
- Pastikan syntax Mermaid valid.
- Jika ada asumsi yang perlu dipakai, gunakan istilah umum Laravel/Vue yang paling dekat dengan project ini.
```
