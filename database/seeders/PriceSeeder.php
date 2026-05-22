<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'time_period' => 'Pagi',
                'start_time' => '06:00:00',
                'end_time' => '12:00:00',
                'price_per_hour' => 150000,
                'description' => 'Harga khusus untuk waktu pagi',
                'is_active' => true,
            ],
            [
                'time_period' => 'Siang',
                'start_time' => '12:00:00',
                'end_time' => '17:00:00',
                'price_per_hour' => 200000,
                'description' => 'Harga standar untuk waktu siang',
                'is_active' => true,
            ],
            [
                'time_period' => 'Malam',
                'start_time' => '17:00:00',
                'end_time' => '23:59:59',
                'price_per_hour' => 180000,
                'description' => 'Harga khusus untuk waktu malam',
                'is_active' => true,
            ],
        ];

        foreach ($prices as $price) {
            Price::create($price);
        }
    }
}
