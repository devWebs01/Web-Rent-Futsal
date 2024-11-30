<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $facilities = [
            // Fasilitas Umum
            ['facility_name' => 'Parkir Luas'],
            ['facility_name' => 'Toilet'],
            ['facility_name' => 'Kantin'],

            // Fasilitas Khusus
            ['facility_name' => 'AC Ruangan'],
            ['facility_name' => 'WiFi Gratis'],
            ['facility_name' => 'Shower'],

            // Fasilitas Olahraga
            ['facility_name' => 'Kursi Penonton'],
            ['facility_name' => 'Papan Skor Digital'],
            ['facility_name' => 'Bola Futsal'],

            // Fasilitas Premium
            ['facility_name' => 'Private Lounge'],
            ['facility_name' => 'Minuman Gratis'],
            ['facility_name' => 'Sound System Premium'],
        ];

        foreach ($facilities as $facility) {
            Facility::create([
                'field_id' => 1,
                'facility_name' => $facility['facility_name'],
            ]);
            Facility::create([
                'field_id' => 2,
                'facility_name' => $facility['facility_name'],
            ]);
        }
    }
}
