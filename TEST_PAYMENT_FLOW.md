# Payment Integration - Quick Test Checklist

## Pre-Test Verification

### 1. Server Status ✓
- [ ] Laravel server running on `http://localhost:8000`
- [ ] Terminal shows: "INFO Server running on [http://localhost:8000]"

### 2. Frontend Assets ✓
- [ ] `public/build/manifest.json` exists
- [ ] `public/build/assets/` directory has JS/CSS files
- [ ] Build completed successfully

### 3. Environment Variables ✓
- [ ] `.env` has `VITE_MIDTRANS_CLIENT_KEY=your-client-key-here`
- [ ] `.env` has `MIDTRANS_SERVER_KEY=your-server-key-here`
- [ ] `.env` has `MIDTRANS_CLIENT_KEY=your-client-key-here`
- [ ] `.env` has `MIDTRANS_IS_PRODUCTION=false`

### 4. Database Check ✓
```bash
# Run these commands to verify:
php artisan tinker

# Check fields exist
Field::count()  # Should return 2

# Check prices exist  
Price::count()  # Should return 34 (17 slots × 2 fields)

# Check bookings table ready
Schema::hasColumn('bookings', 'phone_number')  # Should be true
Schema::hasColumn('bookings', 'payment_status')  # Should be true

# Check payments table ready
Schema::hasColumn('payments', 'paid_at')  # Should be true
Schema::hasColumn('payments', 'callback_payload')  # Should be true

exit
```

## Booking & Payment Test Flow

### Test Scenario: User Books Court and Pays via QRIS

**Preconditions**:
- [ ] User logged in or ready to register
- [ ] Both fields visible in booking form
- [ ] Current date or future date selectable

**Step 1: Select Field & Date**
```
Expected:
- [x] Field selection shows "Lapangan A" (200,000/jam)
- [x] Field selection shows "Lapangan B" (120,000/jam)
- [x] Date picker allows selecting today or future dates
- [x] Click "Lanjut" to proceed to Step 2
```

**Step 2: Select Time Slot**
```
Expected:
- [x] Display shows "Jadwal Reservasi" with selected field & date
- [x] Show 17 hourly slots: 06:00-07:00 through 22:00-23:00
- [x] Each slot shows time, period name, and price
- [x] All slots show "tersedia" (available)
- [x] Click slot to select it
- [x] Click "Lanjut" to proceed to Step 3
```

**Step 3: Confirm Booking & Enter Contact Info**
```
Expected:
- [x] Display booking summary:
  - Field name
  - Date
  - Time slot
  - Price per hour
  - Total price calculation
- [x] Show 3 input fields:
  - Nomor Telepon (required) *
  - Nama (optional)
  - Email (optional)
- [x] "Catatan" section explains payment flow
- [x] Click "Buat Booking" button
```

**Step 4: Payment Processing**
```
Expected on form submission:
- [x] Show: "Booking berhasil dibuat! Memproses pembayaran..."
- [x] Snap payment widget opens automatically
- [x] Snap shows: "Pilih Metode Pembayaran"
- [x] Available payment methods visible (QRIS, Transfer, etc)
```

**Step 5: QRIS Payment Test**
```
In Snap payment page:
- [x] Click "QRIS" or show QRIS option
- [x] QR code displays
- [x] Click "Simulate another QRIS" (sandbox feature)
- [x] Simulate payment completion
```

**Step 6: Payment Success**
```
Expected after payment:
- [x] Snap closes
- [x] Page redirects to dashboard
- [x] Show: "Pembayaran berhasil! Mengarahkan ke dashboard..."
- [x] Booking appears in history with status "Confirmed"
- [x] Payment status shows "Paid"
```

## API Testing (Optional - Use Postman/curl)

### Test Booking Creation
```bash
POST http://localhost:8000/api/bookings
Headers: 
  - Content-Type: application/json
  - Authorization: Bearer {token}
Body:
{
  "field_id": 1,
  "start_time": "2026-06-10 06:00:00",
  "end_time": "2026-06-10 07:00:00",
  "phone_number": "081234567890"
}

Expected: 201 Created with booking_id
```

### Test Midtrans Token Creation
```bash
POST http://localhost:8000/api/payments/create-midtrans-token
Headers:
  - Content-Type: application/json
  - Authorization: Bearer {token}
Body:
{
  "booking_id": 1,
  "amount": 200000,
  "customer_phone": "081234567890",
  "customer_name": "Test User",
  "customer_email": "test@example.com"
}

Expected: 200 OK with snap_token
```

## Log Monitoring During Test

Open new terminal and run:
```bash
cd "c:\laragon\www\New folder\Sistem_reservasi_futsal"
Get-Content storage/logs/laravel-*.log -Wait -Tail 20
```

**Look for these messages**:
```
✓ "Booking created successfully"  
✓ "Midtrans token created"        
✓ "Snap token generated"          
✓ "Midtrans notification processed"
✓ "Booking status updated to confirmed"

✗ "Midtrans notification payment not found" (ERROR)
✗ "Gagal membuat token pembayaran" (ERROR)
```

## Success Indicators

After complete flow:
- [ ] New booking record in database with `payment_status = 'paid'`
- [ ] Payment record created with correct `transaction_id` and `amount`
- [ ] Booking status changed from `'pending'` to `'confirmed'`
- [ ] `paid_at` timestamp populated
- [ ] `callback_payload` contains Midtrans response
- [ ] User sees booking in dashboard
- [ ] Email confirmation sent (if configured)

## Failure Recovery

**If payment fails at any step**:

1. Check logs in `storage/logs/laravel-*.log`
2. Verify Midtrans keys in `.env` file
3. Ensure booking record exists before payment attempt
4. Check if phone_number was provided in form
5. Verify VITE_MIDTRANS_CLIENT_KEY environment variable loaded
6. Restart Laravel server: `php artisan serve`
7. Clear app cache: `php artisan cache:clear`

## Database Verification After Test

```bash
php artisan tinker

# Check new booking
Booking::latest()->first();

# Check payment created
Payment::latest()->first();

# Check if payment linked to booking
$payment = Payment::latest()->first();
$payment->booking;

# Check callback received
$payment->callback_payload;

exit
```

---

**Time to Run Full Test**: ~5 minutes
**Test Account Required**: Yes (login or register first)
**Network Required**: Yes (connects to Midtrans sandbox)
**Cleanup After Test**: Optional (leave data for verification)
