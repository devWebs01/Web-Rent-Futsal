<?php

namespace Database\Seeders;

use App\Models\Blog;
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

        if (!$response->successful()) {
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
            if (!isset($postData['title'], $postData['thumbnail'], $postData['description'])) {
                continue; // Skip jika data tidak lengkap
            }

            $imageName = basename($postData['thumbnail']);
            $slug = Str::slug($postData['title']) . '-' . Str::random(2);
            $tags = implode(',', array_slice(explode(' ', Str::slug($postData['title'])), 0, 3)); // Ambil 3 kata dari slug title


            // Buat data Blog
            $blog = Blog::create([
                'title' => $postData['title'],
                'thumbnail' => 'thumbnail/' . $imageName,
                'slug' => $slug,
                'body' => '<p>' . Str::limit($postData['description'], 500) . '</p>',
                'tag' => $tags, // Tambahkan tag yang dihasilkan
            ]);

            // Ambil gambar jika bisa
            try {
                $imageUrl = $postData['thumbnail'];
                $imageData = file_get_contents($imageUrl);
                Storage::put('public/thumbnail/' . $imageName, $imageData);
            } catch (\Exception $e) {
                $this->command->warn("Gagal mengunduh gambar untuk: {$postData['title']}");
            }

            $this->command->info("Tambah Berita: {$blog->title}");
        }

        $this->command->info('Posts seeded successfully!');
    }
}
