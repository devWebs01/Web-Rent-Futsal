<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'field_name' => 'Lapangan 1',
                'description' => 'Lapangan futsal rumput sintetis 1 powered grass yang berlokasi di tengah Kota Jambi yang dilengkapi dengan fasilitas kantin dan wifi',
                'status' => 'ACTIVE',
            ],
            [
                'field_name' => 'Lapangan 2',
                'description' => 'Lapangan futsal rumput sintetis 2 powered grass yang berlokasi di tengah Kota Jambi yang dilengkapi dengan fasilitas kantin dan wifi',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($fields as $field) {
            // Efek animasi titik-titik di terminal
            for ($i = 0; $i < 5; $i++) {
                echo '.';
                usleep(300000); // Tunggu 300ms
            }
            echo "\r"; // Kembali ke awal baris
            echo 'Loading'; // Cetak ulang teks "Loading"
            usleep(300000); // Tunggu sebelum mengulang titik

            Field::create($field);
        }
    }
}
