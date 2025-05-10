<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Unsplash\HttpClient;
use Unsplash\Search;

class GallerySeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // Inisialisasi Unsplash API dengan hanya menggunakan applicationId
        HttpClient::init([
            'applicationId' => 'S2ev8mfF52EZ1O9QL5ucmQeNR5FToQDi1_Fr7UkCATk',
            'utmSource' => 'Seeder Web',
        ]);

        // Ambil 15 gambar dengan query 'wanderlust'
        $photos = Search::photos('futsal', 1, 15);

        foreach ($photos->getResults() as $photo) {
            // Pastikan data yang diambil adalah array
            if (! is_array($photo) || ! isset($photo['urls']['regular'])) {
                continue;
            }

            $imageUrl = $photo['urls']['regular'];
            $altText = $photo['alt_description'] ?? 'Unsplash Image';

            // Animasi loading di terminal
            echo 'Loading ';
            for ($i = 0; $i < 5; $i++) {
                echo '.';
                usleep(300000); // jeda 300ms
            }
            echo "\rLoading "; // Kembalikan kursor ke awal baris
            usleep(300000);

            // Unduh gambar dari URL
            $imageContents = file_get_contents($imageUrl);
            $imageName = basename(parse_url($imageUrl, PHP_URL_PATH)).'.jpg'; // Tambahkan ekstensi
            $storagePath = 'gallery/'.$imageName;

            // Simpan gambar ke storage publik
            Storage::disk('public')->put($storagePath, $imageContents);

            // Simpan data ke database
            Gallery::create([
                'image' => $storagePath,
                'alt' => $altText,
            ]);

            echo "Gambar disimpan: {$imageName}\n";
        }

        echo "Seeding selesai!\n";
    }
}
