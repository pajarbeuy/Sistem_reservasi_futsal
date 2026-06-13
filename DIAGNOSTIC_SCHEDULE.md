```
# Diagnostic Checklist - Schedule Issues

## 1. Check Database Status
Run in terminal:
```bash
php artisan tinker
>>> DB::table('prices')->count();  // Should return > 0
>>> DB::table('fields')->count();  // Should return > 0
>>> DB::table('prices')->get();
```

## 2. If Prices Table is Empty
You need to seed the prices. Create a seeder:
```bash
php artisan make:seeder PriceSeeder
```

Then edit `database/seeders/PriceSeeder.php` and add:
```php
<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        Price::create([
            'time_period' => 'Morning',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        Price::create([
            'time_period' => 'Afternoon',
            'start_time' => '12:00:00',
            'end_time' => '17:00:00',
            'price_per_hour' => 125000,
            'is_active' => true,
        ]);

        Price::create([
            'time_period' => 'Evening',
            'start_time' => '17:00:00',
            'end_time' => '22:00:00',
            'price_per_hour' => 150000,
            'is_active' => true,
        ]);
    }
}
```

Then run:
```bash
php artisan db:seed --class=PriceSeeder
```

## 3. If Fields Table is Empty
Create a FieldSeeder:
```bash
php artisan make:seeder FieldSeeder
```

Edit `database/seeders/FieldSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        Field::create([
            'name' => 'Lapangan A',
            'description' => 'Lapangan futsal standar',
            'location' => 'Blok A',
            'capacity' => 10,
            'is_available' => true,
        ]);

        Field::create([
            'name' => 'Lapangan B',
            'description' => 'Lapangan futsal standar',
            'location' => 'Blok B',
            'capacity' => 10,
            'is_available' => true,
        ]);
    }
}
```

Then run:
```bash
php artisan db:seed --class=FieldSeeder
```

## 4. Clear Cache and Reload
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
npm run build
```

## 5. Debug Schedule API
Open browser console (F12) and:
1. Go to `/lapangan/1/booking` (assuming field ID 1)
2. Check Console tab for logs
3. Check Network tab - click on the `/api/schedule/day-schedule?...` request
4. Look at Response - should show schedule array

## Expected API Response Format:
```json
{
  "success": true,
  "field_id": 1,
  "date": "2026-06-07",
  "schedule": [
    {
      "time_period": "Morning",
      "start_time": "08:00:00",
      "end_time": "12:00:00",
      "price_per_hour": 100000,
      "available_count": 8,
      "booked_count": 0,
      "status": "available"
    }
  ]
}
```

## Common Issues & Fixes:

**Issue:** Empty schedule array  
**Cause:** No prices defined in database  
**Fix:** Run PriceSeeder

**Issue:** Field not found error  
**Cause:** Invalid field ID  
**Fix:** Check /api/fields to see available fields

**Issue:** All slots show "Terbooking"  
**Cause:** Bookings overlap the time periods  
**Fix:** Check bookings table, cancel test bookings
```
