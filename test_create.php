<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Add a test booking
\App\Models\Booking::create([
    'user_id' => 1,
    'booking_code' => 'TEST-001',
    'field_id' => 1,
    'start_time' => '2026-06-23 09:00:00',
    'end_time' => '2026-06-23 10:00:00',
    'total_price' => 100000,
    'phone_number' => '123',
    'status' => 'pending',
    'payment_status' => 'pending',
    'payment_method' => 'qris'
]);
echo "Test booking created.\n";
