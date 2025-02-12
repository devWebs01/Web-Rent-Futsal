<?php

namespace Database\Seeders;

use App\Models\Blog;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://api-berita-indonesia.vercel.app/antara/bola');

        if (! $response->successful()) {
            $this->command->error('Failed to fetch data from the API.');

            return;
        }

        $data = $response->json();
        $posts = $data['data']['posts'] ?? [];

        if (empty($posts)) {
            $this->command->warn('No posts found in the API response.');

            return;
        }

        foreach ($posts as $postData) {
            if (! isset($postData['title'], $postData['thumbnail'], $postData['description'])) {
                continue; // Skip jika data tidak lengkap
            }

            $imageName = basename($postData['thumbnail']);
            $slug = Str::slug($postData['title']).'-'.Str::random(2);
            $tags = implode(',', array_slice(explode(' ', Str::slug($postData['title'])), 0, 3)); // Ambil 3 kata dari slug title
            $faker = Faker::create();

            // Buat data Blog
            $blog = Blog::create([
                'title' => $postData['title'],
                'thumbnail' => 'thumbnail/'.$imageName,
                'slug' => $slug,
                'body' => '<h2>'.$faker->sentence(rand(5, 10)).'</h2>'. // Judul kecil
                    '<p>'.Str::limit($faker->paragraph(8), 500).'</p>'. // Paragraf utama
                    '<blockquote>"'.$faker->sentence(rand(10, 20)).'"</blockquote>'. // Kutipan menarik
                    '<p>'.implode('</p><p>', $faker->paragraphs(rand(4, 8))).'</p>'. // Beberapa paragraf
                    '<h3>'.$faker->sentence(rand(3, 7)).'</h3>'. // Subjudul tambahan
                    '<ul>'.
                    '<li>'.$faker->sentence(rand(5, 12)).'</li>'.
                    '<li>'.$faker->sentence(rand(5, 12)).'</li>'.
                    '<li>'.$faker->sentence(rand(5, 12)).'</li>'.
                    '</ul>'. // Daftar poin
                    '<p>'.implode('</p><p>', $faker->paragraphs(rand(3, 6))).'</p>'. // Tambahan paragraf
                    '<ol>'.
                    '<li>'.$faker->sentence(rand(6, 14)).'</li>'.
                    '<li>'.$faker->sentence(rand(6, 14)).'</li>'.
                    '<li>'.$faker->sentence(rand(6, 14)).'</li>'.
                    '</ol>'. // Daftar bernomor
                    '<p>'.$faker->sentence(rand(10, 25)).'</p>'.
                    '<p><strong>'.$faker->sentence(rand(8, 15)).'</strong></p>', // Kalimat tebal
                'tag' => $tags, // Tambahkan tag yang dihasilkan
            ]);

            // Ambil gambar jika bisa
            try {
                $imageUrl = $postData['thumbnail'];
                $imageData = file_get_contents($imageUrl);
                Storage::put('public/thumbnail/'.$imageName, $imageData);
            } catch (\Exception $e) {
                $this->command->warn("Gagal mengunduh gambar untuk: {$postData['title']}");
            }

            $this->command->info("Tambah Berita: {$blog->title}");
        }

        $this->command->info('Posts seeded successfully!');
    }
}
