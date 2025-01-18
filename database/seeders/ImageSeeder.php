<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            // Fasilitas Umum
            ['image_path' => 'https://asset.ayo.co.id/image/venue/170529149540925.image_cropper_1705290870100_large.jpg'],
            ['image_path' => 'https://asset.ayo.co.id/image/venue/170529149722147.image_cropper_1705290898844_large.jpg'],
            ['image_path' => 'https://asset.ayo.co.id/image/venue/170529149872547.image_cropper_1705290941677_large.jpg'],
        ];

        foreach ($images as $image) {
            // Cek apakah layanan sudah ada berdasarkan vendor dan category_id
            $imageContents = file_get_contents(filename: $image['image_path']);
            $imageName = basename(path: $image['image_path']);
            $storagePath = 'fields/'.$imageName;
            Storage::disk('public')->put($storagePath, $imageContents);

            Image::create([
                'field_id' => 1,
                'image_path' => $storagePath,
            ]);
            Image::create([
                'field_id' => 2,
                'image_path' => $storagePath,
            ]);
        }

    }
}
