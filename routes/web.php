<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages.welcome');
});

Auth::routes([
    'verify' => 'true',
]);

Route::get('home', [HomeController::class, 'index'])
    ->middleware(['checkRole:developer,admin', 'autoCancel'])
    ->name('home');

Route::get('clear', function () {
    Artisan::call('optimize:clear');

    return '✅ Optimize berhasil dibuat!';
});

Route::get('storage-link', function () {
    Artisan::call('storage:link');

    return '✅ Storage link berhasil dibuat!';
});

Route::get('migrate-fresh', function () {
    Artisan::call('migrate:fresh --seed');

    return '✅ migrate dan seeder berhasil dibuat!';
});

Route::delete('blogs/{id}', [HomeController::class, 'destroy'])->name('blogs.destroy');

use Illuminate\Support\Facades\File;

Route::get('copy-storage', function () {
    $from = storage_path('app/public');
    $to = public_path('storage');

    File::copyDirectory($from, $to);

    return 'File dari storage/app/public berhasil dicopy ke public_html/storage';
});
