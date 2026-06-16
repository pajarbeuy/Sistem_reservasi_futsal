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
                'type' => 'Interlock',
                'description' => 'Lapangan futsal besar dengan standar internasional. Ideal untuk turnamen dan acara berskala besar.',
                'price_per_hour' => 200000,
                'is_available' => true,
            ],
            [
                'name' => 'Lapangan B',
                'type' => 'Interlock',
                'description' => 'Lapangan futsal kecil dengan standar nasional. Cocok untuk latihan, training, dan pertandingan reguler.',
                'price_per_hour' => 120000,
                'is_available' => true,
            ],
        ];

        foreach ($fields as $field) {
            Field::create($field);
        }
    }
}
