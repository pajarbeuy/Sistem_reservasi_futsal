# Quick Test Commands Reference

## Prerequisites Check

```bash
# 1. Navigate to project
cd "c:\laragon\www\New folder\Sistem_reservasi_futsal"

# 2. Check if server is running
curl http://localhost:8000

# 3. Check .env configuration
grep MIDTRANS .env | grep -v COMMENT

# 4. Check database status
php artisan migrate:status
```

## Start Fresh

```bash
# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Start server if not running
php artisan serve --host=localhost --port=8000

# In another terminal, start Vite dev server
npm run dev
```

## Database Verification Commands

```bash
# Using tinker REPL
php artisan tinker

# Verify fields
Field::all();

# Verify prices (should be 34 = 17 slots × 2 fields)
Price::count();

# Check if a booking exists
Booking::latest()->first();

# Check if payment was created
Payment::latest()->first();

# Check booking-payment relationship
$booking = Booking::latest()->first();
$booking->payment;

# Check callback payload
$payment = Payment::latest()->first();
$payment->callback_payload;

exit
```

## Database Cleanup (if needed)

```bash
# DANGER: Resets everything - use only if needed
php artisan migrate:fresh --seed

# This will:
# 1. Drop all tables
# 2. Re-run all migrations
# 3. Seed with initial data (fields, prices)
```

## Log Monitoring During Test

```bash
# PowerShell - Watch logs in real-time
Get-Content storage/logs/laravel-*.log -Wait -Tail 50

# Or search for specific messages
Get-Content storage/logs/laravel-*.log | Select-String "Midtrans"
Get-Content storage/logs/laravel-*.log | Select-String "payment"
Get-Content storage/logs/laravel-*.log | Select-String -Pattern "error|Error|ERROR"
```

## API Testing with curl

```bash
# 1. Get your auth token (after login)
# From browser, check localStorage for token
# Or from /auth/login endpoint

# 2. Test booking creation
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "field_id": 1,
    "start_time": "2026-06-10 06:00:00",
    "end_time": "2026-06-10 07:00:00",
    "phone_number": "081234567890"
  }'

# Expected response: 
# {
#   "error": false,
#   "message": "Booking created successfully. Please proceed to payment.",
#   "data": { "id": 123, ... }
# }

# 3. Test Midtrans token creation (use booking_id from response)
curl -X POST http://localhost:8000/api/payments/create-midtrans-token \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "booking_id": 123,
    "amount": 200000,
    "customer_phone": "081234567890",
    "customer_name": "Test User",
    "customer_email": "test@example.com"
  }'

# Expected response:
# {
#   "error": false,
#   "token": "2e5c8b7a...",
#   "redirect_url": "..."
# }
```

## Browser Testing Steps

```
1. Go to http://localhost:8000
2. Register/Login
3. Click "Buat Booking Baru"
4. Select field and date → Lanjut
5. Select 1-hour time slot (e.g., 06:00-07:00) → Lanjut
6. Fill in:
   - Nomor Telepon: 081234567890 (required)
   - Nama: Test User
   - Email: test@example.com
7. Click "Buat Booking"
8. Snap payment page should open in same window
9. Choose payment method (use QRIS)
10. Click "Simulate another QRIS" (sandbox feature)
11. Should redirect to dashboard with confirmation
```

## Troubleshooting Commands

```bash
# Check if Midtrans keys are loaded
php artisan tinker
config('midtrans.server_key')
config('midtrans.client_key')
exit

# Check if VITE variables are in build
cat public/build/manifest.json | grep MIDTRANS

# Rebuild assets
npm run build

# Clear Laravel cache (sometimes helps)
php artisan cache:forget '*'

# Check recent errors
tail -50 storage/logs/laravel-*.log

# Reset specific table
php artisan tinker
Booking::truncate();
Payment::truncate();
Price::truncate();
Field::truncate();
exit
# Then: php artisan db:seed --class=PriceSeeder
```

## Payment Test Credentials (Midtrans Sandbox)

```
QRIS Payment:
- Choose QRIS method
- QR code displays  
- Click "Simulate another QRIS" button
- Auto-completes in sandbox

Credit Card Test:
- Number: 4811111111111114
- Exp: 12/25
- CVV: 123
- Name: TEST USER
- Status: Always succeeds in sandbox

Bank Transfer (Permata):
- Account auto-generated
- 15 minutes to complete (or use Test button)

E-Wallet (GCash, OVO, DANA):
- Phone number: any valid number
- OTP: 123456 in sandbox
```

## Production Deployment Checklist

```bash
# 1. Update .env with production keys
MIDTRANS_SERVER_KEY=Mid-server-prod-xxxxx
MIDTRANS_CLIENT_KEY=Mid-client-prod-xxxxx
MIDTRANS_IS_PRODUCTION=true
VITE_MIDTRANS_CLIENT_KEY=Mid-client-prod-xxxxx

# 2. Build production assets
npm run build

# 3. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Update Midtrans webhook URL in dashboard:
# https://your-domain.com/payments/midtrans-callback

# 5. Deploy to server
# (use your deployment script)

# 6. Verify production
curl https://your-domain.com/api/schedule/available-slots?field_id=1&date=2026-06-10
```

## Emergency Fixes

```bash
# If payment button not working
npm run build
php artisan cache:clear

# If Snap not loading
# Check browser console for errors
# Verify VITE_MIDTRANS_CLIENT_KEY in .env
# Hard refresh: Ctrl+Shift+Delete cache

# If booking not creating
# Check Laravel error logs
# Verify user is authenticated
# Check if phone_number field filled

# If webhook not received
# Check Midtrans dashboard for failed webhooks
# Verify callback URL in dashboard
# Check IP whitelist if applicable
```

---

**Pro Tips**:
- Use browser DevTools Network tab to see API requests
- Check browser Console for JavaScript errors  
- Monitor Laravel logs in real-time during payment
- Save successful payment transaction_id for reference
- Test all payment methods before going live

