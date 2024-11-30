<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FieldSeeder::class,
            SettingSeeder::class,
            PriceSeeder::class,
            FacilitySeeder::class,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@testing.com',
            'password' => Hash::make('password'),
            'phone' => '08978301711',
            'role' => 'admin',
        ]);
    }
}
