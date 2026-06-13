# Midtrans Payment System - Fix & Setup Guide

## Status: ✅ FIXED & READY FOR TESTING

### What Was Fixed

#### 1. **Frontend Environment Variable**
- **Problem**: `VITE_MIDTRANS_CLIENT_KEY` was empty
- **Solution**: Added to `.env` file
  ```env
  VITE_MIDTRANS_CLIENT_KEY=your-client-key-here
  ```

#### 2. **BookingForm.vue - Added Payment Integration**
- **Before**: Form only created booking, then redirected to dashboard without payment
- **After**: Form now:
  1. Collects customer phone number, name, and email
  2. Creates booking
  3. Creates Midtrans payment token
  4. Opens Midtrans Snap payment page
  5. Handles payment callbacks

- **New Fields Added**:
  - `phone_number` (required) - for payment confirmation
  - `customer_name` (optional) - for receipt
  - `customer_email` (optional) - for receipt

#### 3. **Updated submitBooking Method**
```javascript
// Step 1: Create booking
// Step 2: Create Midtrans token
// Step 3: Load Midtrans Snap script
// Step 4: Open payment page
// Step 5: Handle callbacks (success/error/pending)
```

#### 4. **Database Schema - Payment Columns**
Migration `2026_06_08_080000` added required columns:
- `paid_at` (timestamp)
- `failed_at` (timestamp)
- `callback_payload` (json)

### Configuration Checklist

#### ✅ Environment Variables (.env)
```env
# Midtrans Sandbox Keys (from Midtrans Dashboard)
MIDTRANS_SERVER_KEY=your-server-key-here
MIDTRANS_CLIENT_KEY=your-client-key-here
MIDTRANS_IS_PRODUCTION=false

# Frontend Access
VITE_MIDTRANS_CLIENT_KEY=your-client-key-here
```

#### ✅ Database State
- All migrations applied: `php artisan migrate:status` ✓
- Prices seeded with hourly slots: 17 slots × 2 fields ✓
- Payment table ready with all columns ✓

#### ✅ API Routes
- `POST /api/bookings` - Create booking with phone_number
- `POST /api/payments/create-midtrans-token` - Get Snap token
- `POST /payments/midtrans-callback` - Webhook from Midtrans
- `GET /payment/finish`, `/payment/error`, `/payment/pending` - Redirect URLs

#### ✅ Frontend Assets
- Vite build completed
- Midtrans Snap script loads from CDN: `https://app.sandbox.midtrans.com/snap/snap.js`
- Vue components updated

### Testing Workflow

#### Step 1: Login as User
1. Go to `http://localhost:8000`
2. Register or login with test account

#### Step 2: Create Booking
1. Go to "Buat Booking Baru" or navigate to booking form
2. **Step 1**: Select field (Lapangan A or B) and date
3. **Step 2**: Select hourly time slot (e.g., 06:00-07:00)
4. **Step 3**: Enter required info:
   - **Nomor Telepon**: `081234567890` (or any valid format)
   - **Nama**: Your name
   - **Email**: Your email

#### Step 3: Submit for Payment
1. Click "Buat Booking" button
2. Should see: "Booking berhasil dibuat! Memproses pembayaran..."

#### Step 4: Midtrans Snap Payment Page
1. Snap payment page should open
2. Select payment method (QRIS, Transfer, e-wallet)

#### Step 5: Test Sandbox Payment
For Sandbox testing, use these test credentials:

**Credit Card Payment**:
- Card Number: `4811 1111 1111 1114`
- Exp: `12/25`
- CVV: `123`
- Status: Success

**QRIS Payment**:
- QR code displays in Snap
- In sandbox, click "Simulate another QRIS" to test

**Bank Transfer (Permata)**:
- Account number auto-generated
- Shows in Snap page

#### Step 6: Payment Success
Expected flow:
1. Payment processes ✓
2. Midtrans webhook calls: `POST /payments/midtrans-callback` ✓
3. Payment status updates to "success"
4. Booking status updates to "confirmed"
5. Redirects to `/dashboard`
6. Booking shows in history with "Paid" status

### Database Query - Verify Payment Flow

```sql
-- Check booking created
SELECT id, booking_code, user_id, field_id, status, payment_status 
FROM bookings 
ORDER BY created_at DESC LIMIT 5;

-- Check payment created
SELECT id, booking_id, amount, payment_method, payment_status, transaction_id 
FROM payments 
ORDER BY created_at DESC LIMIT 5;

-- Check payment callback received
SELECT id, booking_id, callback_payload, payment_status 
FROM payments 
WHERE callback_payload IS NOT NULL;
```

### Log Files to Monitor

During testing, watch these logs:

```bash
# Real-time log viewer
tail -f storage/logs/laravel-*.log

# Look for these entries:
# ✓ "Booking created successfully"
# ✓ "Midtrans token created"
# ✓ "Midtrans notification processed"
# ✗ "Midtrans notification payment not found" (BAD)
```

### Troubleshooting

#### Issue: "Midtrans client key tidak dikonfigurasi"
**Solution**: Make sure `VITE_MIDTRANS_CLIENT_KEY` is in `.env` file

#### Issue: "Gagal membuat token pembayaran"
**Solution**: Check if booking exists and `MIDTRANS_SERVER_KEY` is valid

#### Issue: Payment created but webhook not received
**Solution**: 
- Midtrans sandbox may not reach localhost
- For testing, use Midtrans Snap "Simulate" feature
- Check logs for "Midtrans notification payment not found"

#### Issue: "Nomor telepon harus diisi untuk pembayaran"
**Solution**: Phone number field is required - fill it before clicking "Buat Booking"

### File Changes Summary

| File | Changes | Status |
|------|---------|--------|
| `.env` | Added `VITE_MIDTRANS_CLIENT_KEY` | ✅ |
| `resources/js/Pages/BookingForm.vue` | Added phone fields + payment flow | ✅ |
| `app/Http/Controllers/MidtransPaymentController.php` | Already implemented | ✅ |
| `app/Http/Controllers/Api/BookingController.php` | Already saves phone_number | ✅ |
| `app/Models/Booking.php` | phone_number in fillable | ✅ |
| `app/Models/Payment.php` | All required columns | ✅ |
| Database Migrations | All columns exist | ✅ |

### Next Steps

1. ✅ **All fixes applied**
2. 🔄 **Test booking flow end-to-end**
3. 🔄 **Verify payment processing**
4. 🔄 **Check webhook callbacks**
5. 📊 **Monitor logs for errors**
6. 🚀 **Deploy to production** (update keys, set `MIDTRANS_IS_PRODUCTION=true`)

### Production Deployment

When ready for production:

1. Get production keys from Midtrans dashboard
2. Update `.env`:
   ```env
   MIDTRANS_SERVER_KEY=Mid-server-xxxxx  # Production key
   MIDTRANS_CLIENT_KEY=Mid-client-xxxxx  # Production key
   MIDTRANS_IS_PRODUCTION=true
   VITE_MIDTRANS_CLIENT_KEY=Mid-client-xxxxx  # Production key
   ```
3. Build assets: `npm run build`
4. Deploy: `php artisan deploy` (or your deployment script)
5. Update Midtrans callback URL in dashboard to your production URL

### Support

- Midtrans Documentation: https://docs.midtrans.com
- Sandbox Credentials: Check Midtrans Dashboard
- Test Transactions: Use sandbox payment credentials above

---
**Last Updated**: 2026-06-09
**Status**: Ready for Testing
