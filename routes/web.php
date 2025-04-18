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

Route::get('/home', [HomeController::class, 'index'])
    ->middleware(['checkRole:developer,admin', 'autoCancel'])
    ->name('home');

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return '✅ Storage link berhasil dibuat!';
});

Route::get('/migrate-fresh', function () {
    Artisan::call('php artisan migrate:fresh --seed');
    return '✅ migrate dan seeder berhasil dibuat!';
});