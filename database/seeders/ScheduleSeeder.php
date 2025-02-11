<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Senin - Kamis
            [
                'start_day' => 'Monday',
                'end_day' => 'Thursday',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'type' => 'STUDENT',
                'cost' => '50000',
            ],
            [
                'start_day' => 'Monday',
                'end_day' => 'Thursday',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'type' => 'GENERAL',
                'cost' => '60000',
            ],
            [
                'start_day' => 'Monday',
                'end_day' => 'Thursday',
                'start_time' => '16:00',
                'end_time' => '18:00',
                'type' => 'STUDENT',
                'cost' => '60000',
            ],
            [
                'start_day' => 'Monday',
                'end_day' => 'Thursday',
                'start_time' => '16:00',
                'end_time' => '18:00',
                'type' => 'GENERAL',
                'cost' => '80000',
            ],
            [
                'start_day' => 'Monday',
                'end_day' => 'Thursday',
                'start_time' => '18:00',
                'end_time' => '23:00',
                'type' => 'STUDENT',
                'cost' => '120000',
            ],
            [
                'start_day' => 'Monday',
                'end_day' => 'Thursday',
                'start_time' => '18:00',
                'end_time' => '23:00',
                'type' => 'GENERAL',
                'cost' => '120000',
            ],

            // Jumat
            [
                'start_day' => 'Friday',
                'end_day' => 'Friday',
                'start_time' => '18:00',
                'end_time' => '23:00',
                'type' => 'STUDENT',
                'cost' => '130000',
            ],
            [
                'start_day' => 'Friday',
                'end_day' => 'Friday',
                'start_time' => '18:00',
                'end_time' => '23:00',
                'type' => 'GENERAL',
                'cost' => '130000',
            ],

            // Sabtu, Minggu, Tanggal Merah
            [
                'start_day' => 'Saturday',
                'end_day' => 'Sunday',
                'start_time' => '08:00',
                'end_time' => '11:00',
                'type' => 'STUDENT',
                'cost' => '70000',
            ],
            [
                'start_day' => 'Saturday',
                'end_day' => 'Sunday',
                'start_time' => '08:00',
                'end_time' => '11:00',
                'type' => 'GENERAL',
                'cost' => '85000',
            ],
            [
                'start_day' => 'Saturday',
                'end_day' => 'Sunday',
                'start_time' => '11:00',
                'end_time' => '16:00',
                'type' => 'STUDENT',
                'cost' => '60000',
            ],
            [
                'start_day' => 'Saturday',
                'end_day' => 'Sunday',
                'start_time' => '11:00',
                'end_time' => '16:00',
                'type' => 'GENERAL',
                'cost' => '70000',
            ],
            [
                'start_day' => 'Saturday',
                'end_day' => 'Sunday',
                'start_time' => '16:00',
                'end_time' => '23:00',
                'type' => 'STUDENT',
                'cost' => '70000',
            ],
            [
                'start_day' => 'Saturday',
                'end_day' => 'Sunday',
                'start_time' => '16:00',
                'end_time' => '23:00',
                'type' => 'GENERAL',
                'cost' => '85000',
            ],

            // Turnamen / Keramaian
            [
                'start_day' => 'Monday',
                'end_day' => 'Sunday',
                'start_time' => '08:00',
                'end_time' => '18:00',
                'type' => 'TOURNAMENT',
                'cost' => '100000',
            ],
        ];

        foreach ($data as $shedule) {
            // Efek animasi titik-titik di terminal
            for ($i = 0; $i < 5; $i++) {
                echo '.';
                usleep(300000); // Tunggu 300ms
            }
            echo "\r"; // Kembali ke awal baris
            echo 'Loading'; // Cetak ulang teks "Loading"
            usleep(300000); // Tunggu sebelum mengulang titik

            Schedule::create($shedule);
        }
    }
}
