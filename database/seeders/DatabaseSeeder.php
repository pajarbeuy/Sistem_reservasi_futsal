<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles and permissions first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@futsal.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create regular user
        $user = User::create([
            'name' => 'User Demo',
            'email' => 'user@futsal.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        // Seed fields first, then prices
        $this->call([
            FieldSeeder::class,
            PriceSeeder::class,
        ]);
    }
}
