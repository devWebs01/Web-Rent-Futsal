<?php

use App\Models\Blog;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('informations.blog');

// State untuk artikel utama dan rekomendasi berita
state([
    'recommendations' => Blog::latest()->limit(5)->get(),
    'blog',
]);

?>

<x-guest-layout>
    <style>
        .card-img-top {
            max-height: 400px;
            object-fit: cover;
        }

        h1 {
            font-size: 2rem;
        }

        .list-group-item {
            border: none;
            padding: 10px 0;
        }

        .list-group-item img {
            border-radius: 8px;
        }
    </style>
    @volt
        <div>
            <div class="container-fluid px-3">
                <div class="row">
                    <!-- Artikel Utama -->
                    <div class="col-lg-8">
                        <div class="card border-0">
                            <img src="{{ Storage::url($blog->thumbnail) }}" class="card-img-top rounded"
                                alt="{{ $blog->title }}">
                            <div class="card-body">
                                <h1 class="fw-bold">{{ $blog->title }}</h1>
                                <p class="text-muted">Dipublikasikan pada {{ $blog->created_at->format('d M Y') }}</p>
                                <div class="content">
                                    {!! $blog->body !!}
                                </div>

                                <div class="mt-4">
                                    <h6 class="fw-bold">Tags:</h6>
                                    <div>
                                        @foreach (explode(',', $blog->tag) as $tag)
                                            <span class="badge bg-light text-dark me-2">
                                                #{{ trim($tag) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Sticky -->
                    <div class="col-lg-4">
                        <div class="position-sticky top-0" style="z-index: 1020;">
                            <div class="card p-3 shadow-sm">
                                <h4 class="mb-3">Baca Lainnya</h4>
                                <ul class="list-group list-group-flush">
                                    @foreach ($recommendations as $post)
                                        <li class="list-group-item d-flex align-items-center">
                                            <img src="{{ Storage::url($post->thumbnail) }}" class="rounded me-3"
                                                width="60" height="60" style="object-fit: cover;">
                                            <a href="{{ route('informations.blog', ['blog' => $post->slug]) }}"
                                                class="text-dark text-decoration-none fw-semibold">
                                                {{ Str::limit($post->title, 60) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-guest-layout>
