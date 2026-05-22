<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'name' => 'Lapangan A',
                'type' => 'Vinyl',
                'price_per_hour' => 150000,
                'is_available' => true,
            ],
            [
                'name' => 'Lapangan B',
                'type' => 'Synthetic',
                'price_per_hour' => 180000,
                'is_available' => true,
            ],
            [
                'name' => 'Lapangan C',
                'type' => 'Synthetic',
                'price_per_hour' => 200000,
                'is_available' => true,
            ],
        ];

        foreach ($fields as $field) {
            Field::create($field);
        }
    }
}
