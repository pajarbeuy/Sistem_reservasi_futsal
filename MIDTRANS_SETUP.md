# Setup Midtrans Payment Gateway

Panduan lengkap untuk mengintegrasikan Midtrans payment gateway ke sistem reservasi futsal.

## 1. Instalasi Package Midtrans

```bash
composer require midtrans/midtrans-php
```

## 2. Setup Environment Variables

Tambahkan ke file `.env`:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false
```

Dan ke `.env.js` atau `.vite.config.js` untuk frontend:

```env
VITE_MIDTRANS_CLIENT_KEY=your_client_key_here
```

## 3. Dapatkan API Keys dari Midtrans

1. Buka https://dashboard.midtrans.com
2. Login dengan akun Anda
3. Masuk ke menu **Settings > Access Keys**
4. Copy **Server Key** dan **Client Key**
5. Gunakan untuk **Sandbox** (testing) atau **Production**

### Sandbox (Testing)
- Server Key: Mulai dengan `SB-Mid-`
- Client Key: Mulai dengan `SB-Mid-`
- URL Script: `https://app.sandbox.midtrans.com/snap/snap.js`

### Production
- Server Key: Mulai dengan `Mid-`
- Client Key: Mulai dengan `Mid-`
- URL Script: `https://app.midtrans.com/snap/snap.js`

## 4. Database Setup (jika belum ada)

Payment table harus memiliki field:
- `booking_id` - Foreign key ke bookings
- `amount` - Jumlah pembayaran
- `payment_method` - Metode pembayaran (midtrans, etc)
- `payment_status` - Status (pending, completed, failed)
- `transaction_id` - ID transaksi Midtrans
- `snap_token` - Token Snap dari Midtrans

## 5. Routes

Berikut routes yang sudah ditambahkan:

### API Routes
```
POST /api/payments/create-midtrans-token
POST /api/payments/callback
```

### Web Routes
```
GET /lapangan - Halaman daftar lapangan
GET /lapangan/{id}/booking - Halaman booking detail
POST /payments/midtrans-callback - Callback Midtrans
GET /payment/finish - Redirect sukses
GET /payment/error - Redirect gagal
GET /payment/pending - Redirect pending
```

## 6. Flow Pembayaran

```
1. User klik "Pesan Sekarang" di halaman Lapangan
   ↓
2. Redirect ke /lapangan/{id}/booking
   ↓
3. Isi form: tanggal, jam, nomor telepon, catatan
   ↓
4. Klik "Lanjut ke Pembayaran"
   ↓
5. Create booking via API
   ↓
6. Create Midtrans payment token
   ↓
7. Tampilkan Midtrans Snap modal
   ↓
8. User melakukan pembayaran
   ↓
9. Midtrans kirim callback
   ↓
10. Update payment status & booking status
   ↓
11. Redirect ke dashboard
```

## 7. Testing Pembayaran

### Sandbox Mode (Testing)
Gunakan kartu credit dummy dari Midtrans:

**Success Transaction (3D Secure)**
- Card Number: `4111 1111 1111 1111`
- Expiry: `12/25`
- CVV: `123`
- OTP: `123456`

**Failed Transaction**
- Card Number: `4111 1111 1111 1112`
- Expiry: `12/25`
- CVV: `123`

Lihat dokumentasi lengkap: https://docs.midtrans.com/en/snap/integration-guide

## 8. Troubleshooting

### "Payment gateway tidak tersedia"
- Pastikan Midtrans Snap script sudah loaded
- Check browser console untuk error
- Verify VITE_MIDTRANS_CLIENT_KEY sudah benar

### Callback tidak ter-update
- Check URL callback di Midtrans dashboard
- Pastikan server public (tidak localhost)
- Verify signature key calculation

### Token tidak valid
- Verify MIDTRANS_SERVER_KEY benar di backend
- Check amount formatnya (integer, bukan string)

## 9. Keamanan

- Jangan share SERVER_KEY di frontend
- Selalu verify signature pada callback
- Gunakan HTTPS di production
- Aktifkan 3D Secure untuk transaksi
- Set transaction timeout yang sesuai

## 10. Mengubah ke Production

1. Login ke https://dashboard.midtrans.com
2. Pergi ke Settings > Snap Preferences
3. Activate production account
4. Copy Production API Keys
5. Update .env dengan production keys
6. Ubah MIDTRANS_IS_PRODUCTION=true
7. Ubah URL Snap script ke production URL
8. Testing di production environment
