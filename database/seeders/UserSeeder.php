<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class UserSeeder extends Seeder
{
    use HasRoles;

    public function run()
    {
        // Create roles
        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'admin']);
        $sellerRole = Role::create(['guard_name' => 'api', 'name' => 'seller']);
        $customerRole = Role::create(['guard_name' => 'api', 'name' => 'customer']);

        // Create admin user
        $adminUser = User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
        ]);
        $adminUser->assignRole($adminRole->name);

        // Create seller user
        $sellerUser = User::create([
            'username' => 'seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('seller123'),
        ]);
        $sellerUser->assignRole($sellerRole->name);

        // Create customer user
        $customerUser = User::create([
            'username' => 'customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('customer123'),
        ]);
        $customerUser->assignRole($customerRole->name);
    }
}
