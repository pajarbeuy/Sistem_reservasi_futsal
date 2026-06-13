<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        // Generate hourly time slots from 06:00 to 23:00
        $timePeriods = [];
        for ($hour = 6; $hour < 23; $hour++) {
            $startHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $endHour = str_pad($hour + 1, 2, '0', STR_PAD_LEFT);
            
            $timePeriods[] = [
                'time_period' => "{$startHour}:00 - {$endHour}:00",
                'start_time' => "{$startHour}:00:00",
                'end_time' => "{$endHour}:00:00",
            ];
        }

        $fields = [
            ['id' => 1, 'price' => 200000], // Lapangan A - 200rb per jam
            ['id' => 2, 'price' => 120000], // Lapangan B - 120rb per jam
        ];

        // Delete existing prices
        Price::query()->delete();

        foreach ($fields as $field) {
            foreach ($timePeriods as $period) {
                Price::create([
                    'field_id' => $field['id'],
                    'time_period' => $period['time_period'],
                    'start_time' => $period['start_time'],
                    'end_time' => $period['end_time'],
                    'price_per_hour' => $field['price'],
                    'description' => 'Slot ' . $period['time_period'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
