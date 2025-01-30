<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Folio\Folio;

class FolioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Folio::path(resource_path('views/pages'))->middleware([

            'admin/*' => [
                'auth',
                'checkRole:admin,developer',
                'autoCancel',
            ],

            'guest/bookings/*' => [
                'auth',
                'checkRole:customer,developer',
                'autoCancel',
            ],

            'guest/payment-records/*' => [
                'auth',
                'checkRole:customer,developer',
                'autoCancel',
            ],
        ]);
    }
}
