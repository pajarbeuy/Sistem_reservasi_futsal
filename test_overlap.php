<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startTime = \Carbon\Carbon::parse("2026-06-23 09:00:00");
$endTime = \Carbon\Carbon::parse("2026-06-23 10:00:00");

$overlapping = \App\Models\Booking::where('field_id', 1)
    ->whereIn('status', ['pending', 'confirmed'])
    ->where(function ($query) use ($startTime, $endTime) {
        $query->where(function ($q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime);
        });
    })
    ->get();

echo "Overlapping count: " . $overlapping->count() . "\n";
print_r($overlapping->toArray());
