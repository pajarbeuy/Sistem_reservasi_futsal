<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Add a test booking
$request60 = \Illuminate\Http\Request::create('/api/schedule/available-slots', 'GET', [
    'field_id' => 1,
    'date' => '2026-06-23',
    'duration_minutes' => 60
]);

$request30 = \Illuminate\Http\Request::create('/api/schedule/available-slots', 'GET', [
    'field_id' => 1,
    'date' => '2026-06-23',
    'duration_minutes' => 30
]);

$controller = new \App\Http\Controllers\Api\ScheduleController();
$res60 = $controller->getAvailableSlots($request60)->getData(true);
$res30 = $controller->getAvailableSlots($request30)->getData(true);

echo "60 MIN STATUS:\n";
foreach ($res60['slots'] as $slot) {
    if ($slot['status'] !== 'tersedia') {
        echo "{$slot['start_time']} - {$slot['end_time']}: {$slot['status']}\n";
    }
}
echo "\n30 MIN STATUS:\n";
foreach ($res30['slots'] as $slot) {
    if ($slot['status'] !== 'tersedia') {
        echo "{$slot['start_time']} - {$slot['end_time']}: {$slot['status']}\n";
    }
}
