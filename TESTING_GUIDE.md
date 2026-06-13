# 🧪 TESTING GUIDE - BookingController Fixes

## ✅ Pre-Testing Setup

Jalankan perintah ini sebelum testing:

```bash
# 1. Reset database dengan test data
php artisan migrate:fresh --seed

# 2. Clear semua cache
php artisan config:cache
php artisan cache:clear
php artisan route:cache

# 3. Pastikan Laravel server running
php artisan serve

# 4. Di terminal baru, jalankan Vite
npm run dev
```

---

## 🧪 Test 1: Admin Booking (Scenario Utama)

### Step 1: Login as Admin
- URL: `http://localhost/login`
- Email: `admin@futsal.com`
- Password: `password123`
- Click "Login"

**Expected**: Masuk ke dashboard, bisa lihat profile admin

### Step 2: Navigate to Booking
- Klik menu "Lapangan" atau buka `http://localhost/lapangan`
- Harus ada 2 lapangan: "Lapangan A" & "Lapangan B"

**Expected**: Halaman booking dengan list lapangan

### Step 3: Initiate Booking
- Klik salah satu lapangan
- Pilih tanggal hari ini atau besok (date picker)
- Sistem akan load available time slots

**Observe**: 
- ✅ Tidak ada error "Invalid response type"
- ✅ Time slots muncul dengan normal
- ✅ Tidak ada console error

### Step 4: Select Time & Book
- Pilih 1 time slot (contoh: 10:00 - 12:00)
- Click "Pesan Sekarang" / "Book Now"

**Expected Response**: 
```json
{
  "error": false,
  "message": "Booking created successfully. Please proceed to payment.",
  "data": {
    "id": 1,
    "user_id": 1,
    "field_id": 1,
    "start_time": "2026-06-10T10:00:00.000Z",
    "end_time": "2026-06-10T12:00:00.000Z",
    "total_price": 100000,
    "status": "pending"
  }
}
```

**In Browser**: Redirect ke payment page dengan booking details

### Step 5: Verify Payment Page
- Booking total price tampil
- Midtrans payment button muncul
- Bisa klik untuk testing payment

**Expected**: Tidak ada error, payment form ready

---

## 🧪 Test 2: Regular User Booking

### Step 1: Logout & Login as User
- Logout dari admin
- Login dengan:
  - Email: `user@futsal.com`
  - Password: `password123`

### Step 2-5: Repeat Same Steps
- Navigate to lapangan
- Select field, date, time
- Book

**Expected**: Sama seperti admin, booking berjalan lancar

---

## 🧪 Test 3: Admin Dashboard (View All Bookings)

### Step 1: Login as Admin
- Email: `admin@futsal.com`
- Password: `password123`

### Step 2: Go to Admin Dashboard
- Click menu "Admin" atau buka `http://localhost/admin/dashboard`

### Step 3: View All Bookings
- Buka section "Bookings"
- Harus melihat SEMUA bookings (dari admin & user)

**API Call**: 
```
GET /api/bookings
Authorization: Bearer {jwt_token}
```

**Expected Response**:
```json
{
  "error": false,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "field_id": 1,
      "user": {...},
      "field": {...},
      "payment": {...}
    }
  ]
}
```

---

## 🧪 Test 4: User Dashboard (View Own Bookings)

### Step 1: Login as Regular User
- Email: `user@futsal.com`
- Password: `password123`

### Step 2: Go to User Dashboard
- Click menu "Dashboard" atau buka `http://localhost/dashboard`

### Step 3: View Own Bookings
- Harus melihat HANYA booking milik user tersebut

**API Call**: 
```
GET /api/bookings
Authorization: Bearer {jwt_token}
```

**Expected**: Hanya bookings dari user@futsal.com

---

## 🧪 Test 5: Cancel Booking

### Step 1: From Bookings List
- Find booking dengan status "pending"
- Click "Cancel" button

**API Call**:
```
POST /api/bookings/{id}/cancel
Authorization: Bearer {jwt_token}
```

**Expected Response**:
```json
{
  "error": false,
  "message": "Booking cancelled successfully.",
  "data": {
    "id": 1,
    "status": "cancelled"
  }
}
```

**Expected**: Status berubah dari "pending" → "cancelled"

---

## 🧪 Test 6: Error Scenarios

### Test 6a: Invalid Date Format
**API Call**:
```
POST /api/bookings
{
  "field_id": 1,
  "start_time": "2026-06-10",
  "end_time": "2026-06-10"
}
```

**Expected Response (422)**:
```json
{
  "error": true,
  "message": "Validation failed",
  "errors": {
    "start_time": ["The start time field must be a valid date."],
    "end_time": ["The end time field must be a valid date."]
  }
}
```

### Test 6b: Time Conflict
**API Call**:
```
POST /api/bookings
{
  "field_id": 1,
  "start_time": "2026-06-10T10:00:00.000Z",
  "end_time": "2026-06-10T12:00:00.000Z"
}
```
(Booking this time slot again)

**Expected Response (409)**:
```json
{
  "error": true,
  "message": "The field is already booked for this time slot."
}
```

### Test 6c: Non-existent Field
**API Call**:
```
POST /api/bookings
{
  "field_id": 999,
  "start_time": "2026-06-10T10:00:00.000Z",
  "end_time": "2026-06-10T12:00:00.000Z"
}
```

**Expected Response (422 or 404)**:
```json
{
  "error": true,
  "message": "Field not found"
}
```

---

## 🔍 Debugging Checklist

### Browser DevTools
1. Open F12 → Network tab
2. Go through booking flow
3. Check each API call:
   - ✅ Request sent with JWT token in Authorization header
   - ✅ Response status is 2xx for success, proper code for errors
   - ✅ Response is valid JSON (tidak HTML error page)
   - ✅ Response includes 'error' field dan proper message

### Laravel Logs
```bash
# Tail logs in real-time
tail -f storage/logs/laravel.log

# Look for any exceptions atau errors
```

### PHP Tinker (Debug Database)
```bash
php artisan tinker

# Check bookings
>>> Booking::all()
>>> Booking::where('user_id', 1)->get()

# Check users
>>> User::all()
>>> User::find(1)->roles

# Check if admin role exists
>>> User::find(1)->hasRole('admin')
```

---

## ✅ Expected Results Summary

| Test | Admin | User | Status |
|------|-------|------|--------|
| Login | ✅ | ✅ | Pass |
| Book Lapangan | ✅ | ✅ | Pass |
| View All Bookings | ✅ | ❌ (403 unauthorized) | Pass |
| View Own Bookings | ✅ | ✅ | Pass |
| Cancel Own Booking | ✅ | ✅ | Pass |
| Cancel Other Booking | ✅ (admin) | ❌ (403 forbidden) | Pass |
| Invalid Date Format | ❌ (422 error) | ❌ (422 error) | Pass |
| Time Conflict | ❌ (409 conflict) | ❌ (409 conflict) | Pass |
| Nonexistent Field | ❌ (422/404 error) | ❌ (422/404 error) | Pass |

---

## 📝 Issue Reporting

Jika error masih muncul:

1. **Screenshot error message**
2. **Check Network tab response** (right-click → Copy as cURL)
3. **Check browser console** (F12 → Console tab)
4. **Check Laravel logs**: `tail storage/logs/laravel.log`
5. **Report with**:
   - Error message
   - API endpoint being called
   - Request payload
   - Response from server
   - Browser/Laravel logs

---

**Ready to test! 🚀**
