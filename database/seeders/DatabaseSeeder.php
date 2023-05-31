<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\RekomendasiJamu\JamuCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create one jamu category
        $jamu_category = JamuCategory::create(['name' => 'all']);
        $this->call(UserSeeder::class);
    }
}
