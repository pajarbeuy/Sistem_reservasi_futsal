<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'manage fields']);
        Permission::create(['name' => 'view fields']);

        // Create roles and assign created permissions
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        $roleUser = Role::create(['name' => 'user']);
        $roleUser->givePermissionTo('view fields');

        // Create admin user
        $admin = User::create([
            'name' => 'Admin Futsal',
            'email' => 'admin@futsal.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($roleAdmin);

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@futsal.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole($roleUser);
    }
}
