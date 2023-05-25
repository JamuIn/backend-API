<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class UserSeeder extends Seeder
{
    use HasRoles;

    public function run()
    {
        // Create roles
        $customerRole = Role::create(['name' => 'customer']);
        $adminRole = Role::create(['name' => 'admin']);
        $sellerRole = Role::create(['name' => 'seller']);

        // Create users
        $customerUser = User::create([
            'full_name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'phone_number' => '1234567890',
            'password' => Hash::make('password'),
            'address' => '123 Main St',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $adminUser = User::create([
            'full_name' => 'Admin',
            'username' => 'administrator',
            'email' => 'admin@jamuin.com',
            'phone_number' => '0987654321',
            'password' => Hash::make('admin111'),
            'address' => '456 Elm St',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $sellerUser = User::create([
            'full_name' => 'Bob Johnson',
            'username' => 'bobjohnson',
            'email' => 'bobjohnson@example.com',
            'phone_number' => '5555555555',
            'password' => Hash::make('password'),
            'address' => '789 Oak St',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // Assign roles to users
        $customerUser->assignRole($customerRole);
        $adminUser->assignRole($adminRole);
        $sellerUser->assignRole($sellerRole);
    }
}
