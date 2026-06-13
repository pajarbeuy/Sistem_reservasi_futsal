# ✅ Midtrans Setup - Installation Complete

## What's Already Done ✅

### 1. Midtrans PHP SDK Installed
```bash
✅ midtrans/midtrans-php v2.6.2 installed
✅ Location: vendor/midtrans/midtrans-php/
✅ Classes available: Snap, Config, CoreApi, Transaction
```

### 2. Configuration Files Updated
```
✅ .env - Midtrans variables added (empty keys)
✅ vite.config.js - Standard Vite config (auto-exposes VITE_* vars)
```

### 3. Backend Code Ready
```
✅ MidtransPaymentController.php - Payment token & callback logic
✅ config/midtrans.php - Midtrans configuration
✅ routes/api.php - POST /api/payments/create-midtrans-token
✅ routes/api.php - POST /api/payments/callback
✅ routes/web.php - Payment callback routes
```

### 4. Frontend Code Ready
```
✅ LapanganDetail.vue - Complete booking form with Midtrans integration
✅ Loads Snap script dynamically
✅ Creates booking → Gets token → Shows payment modal
```

---

## What's Needed Next - Action Items

### 1️⃣ Get Midtrans API Keys (5 minutes)

**Go to:** https://dashboard.midtrans.com

**Steps:**
1. Create free account or login
2. Navigate to: **Settings > Access Keys**
3. Copy **Server Key** (looks like: `SB-Mid-...` for sandbox)
4. Copy **Client Key** (looks like: `SB-Mid-...` for sandbox)

**⚠️ Keep keys SAFE:**
- Server Key = Backend only (never share)
- Client Key = Frontend (public, safe to expose)

---

### 2️⃣ Update .env File

**File:** `.env` (in project root)

**Find these lines:**
```env
# Midtrans Payment Gateway (Sandbox)
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Midtrans Frontend
VITE_MIDTRANS_CLIENT_KEY=
```

**Replace with your actual keys:**
```env
# Midtrans Payment Gateway (Sandbox)
MIDTRANS_SERVER_KEY=SB-Mid-YourServerKeyHere
MIDTRANS_CLIENT_KEY=SB-Mid-YourClientKeyHere
MIDTRANS_IS_PRODUCTION=false

# Midtrans Frontend
VITE_MIDTRANS_CLIENT_KEY=SB-Mid-YourClientKeyHere
```

⚠️ **Remember:** Only `VITE_MIDTRANS_CLIENT_KEY` needs the Client Key. This one is exposed to frontend.

---

### 3️⃣ Verify Payment Table Has Required Fields

**Check:** `database/migrations/` for payments table migration

**Required fields:**
```php
$table->id();
$table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
$table->decimal('amount', 15, 2);
$table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
$table->string('payment_method')->nullable();
$table->string('transaction_id')->nullable();  // Midtrans order ID
$table->string('snap_token')->nullable();       // Midtrans token
$table->timestamps();
```

If these fields don't exist, create a migration:
```bash
php artisan make:migration add_midtrans_fields_to_payments_table
```

Then add:
```php
Schema::table('payments', function (Blueprint $table) {
    $table->string('snap_token')->nullable();
    $table->string('transaction_id')->nullable();
});
```

---

### 4️⃣ Verify Payment Model

**File:** `app/Models/Payment.php`

**Required:**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_status',
        'payment_method',
        'transaction_id',
        'snap_token',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
```

---

### 5️⃣ Verify BookingController Has API Endpoint

**File:** `app/Http/Controllers/BookingController.php`

**Verify this endpoint exists:**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'field_id' => 'required|exists:fields,id',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'phone_number' => 'required|string',
        'notes' => 'nullable|string',
        'total_price' => 'required|numeric',
    ]);

    $booking = Booking::create([
        'user_id' => auth()->id(),
        ...$validated,
        'status' => 'pending',
    ]);

    return response()->json($booking, 201);
}
```

---

### 6️⃣ Test the Complete Flow

**Checklist:**
- [ ] `.env` has Midtrans keys filled
- [ ] Database migrations run (`php artisan migrate`)
- [ ] API endpoint `/api/bookings` returns POST correctly
- [ ] Frontend build runs (`npm run build`)

**Test Steps:**

1. **Start dev server:**
   ```bash
   npm run dev
   ```

2. **Navigate to:** http://localhost:3000/lapangan
   - Should show list of fields
   - Click "Pesan Sekarang" button

3. **On booking page:** http://localhost:3000/lapangan/1/booking
   - Select date from date picker
   - Select time slot from grid
   - Enter phone number
   - Click "Lanjut ke Pembayaran"

4. **Midtrans Snap Modal Should Appear**
   - Use test card: `4111 1111 1111 1111`
   - Expiry: `12/25`
   - CVV: `123`

5. **After Payment:**
   - Should redirect to `/dashboard`
   - Payment should show in database as 'completed'
   - Booking should show in database as 'confirmed'

---

## Sandbox Test Cards

**Success (3D Secure):**
- Number: `4111 1111 1111 1111`
- Expiry: `12/25` (any future date)
- CVV: `123`
- OTP: `123456`

**Failed:**
- Number: `4111 1111 1111 1112`
- Everything else same as above

---

## Troubleshooting

### "Snap is not defined"
- .env missing VITE_MIDTRANS_CLIENT_KEY
- Dev server not restarted after .env change
- Script tag not loading properly

**Fix:**
```bash
npm run build
php artisan serve
```

### "Payment callback not working"
- Webhook URL not registered in Midtrans dashboard
- For localhost, use ngrok: https://ngrok.com
- Callback signature verification failing

### "Invalid server key"
- Copied wrong key from Midtrans dashboard
- Using Production key in Sandbox (must use SB-Mid-...)
- Key has extra spaces

---

## Next Steps

1. Get API keys from Midtrans dashboard ⬅️ **START HERE**
2. Fill .env with keys
3. Verify database migrations
4. Test full booking flow
5. Move to production when ready (change keys + MIDTRANS_IS_PRODUCTION=true)

---

## Production Checklist

When ready for live payments:

- [ ] Update .env with Production API keys (not SB-*)
- [ ] Set MIDTRANS_IS_PRODUCTION=true
- [ ] Change Snap script URL to: `https://app.midtrans.com/snap/snap.js`
- [ ] Setup proper webhook URL (not localhost)
- [ ] Test payment flow in production
- [ ] Setup payment receipt emails
- [ ] Enable 3D Secure for fraud protection

---

**Need help?** See MIDTRANS_SETUP.md for detailed guide.
