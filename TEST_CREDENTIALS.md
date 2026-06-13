# 🔐 Test Credentials - Updated

Database sudah ter-seed dengan 2 user account:

## 👤 Admin Account
```
Email: admin@futsal.com
Password: password123
Role: admin
```
**Akses**: Admin dashboard (/admin/dashboard), lihat semua booking, kelola lapangan & harga

## 👤 Regular User Account
```
Email: user@futsal.com
Password: password123
Role: user
```
**Akses**: User dashboard (/dashboard), booking pribadi, lihat history

## 🧪 Testing Scenarios

### Test 1: Regular User Booking
1. Login dengan: user@futsal.com / password123
2. Buka http://localhost/lapangan
3. Klik lapangan untuk booking
4. Select tanggal & waktu
5. Click "Pesan Sekarang"

### Test 2: Admin User Booking
1. Login dengan: admin@futsal.com / password123
2. Buka http://localhost/lapangan
3. Klik lapangan untuk booking
4. Select tanggal & waktu
5. Click "Pesan Sekarang"

### Test 3: Admin Dashboard
1. Login dengan: admin@futsal.com / password123
2. Buka http://localhost/admin/dashboard
3. Lihat semua bookings dari semua users
4. Manage lapangan & harga

### Test 4: User Dashboard
1. Login dengan: user@futsal.com / password123
2. Buka http://localhost/dashboard
3. Lihat history booking pribadi
4. Lihat history transaksi

---

## ✅ Perubahan Terbaru (June 8, 2026)

Semua API endpoints diperbaiki untuk:
- ✅ Consistent JSON responses
- ✅ Better error handling (try-catch)
- ✅ Logging untuk debugging
- ✅ Support untuk admin & regular users

Perbaikan di BookingController:
- ✅ index() - Better error handling
- ✅ store() - Improved validation & error messages
- ✅ show() - Try-catch dengan proper error codes
- ✅ cancel() - Better error handling

---

## 🚀 Quick Test Command

```bash
# Reset database dengan seeded users
php artisan migrate:fresh --seed

# Clear caches
php artisan config:cache

# Build frontend
npm run dev

# Then login dengan akun di atas
```
