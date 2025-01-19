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
            ScheduleSeeder::class,
            FacilitySeeder::class,
            ImageSeeder::class,
        ]);

        \App\Models\User::factory(20)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@testing.com',
            'password' => Hash::make('password'),
            'phone' => '08978301711',
            'role' => 'admin',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'pelanggan@testing.com',
            'password' => Hash::make('password'),
            'phone' => '08978301712',
            'role' => 'customer',
        ]);

        \App\Models\Bank::factory(3)->create();
    }
}
