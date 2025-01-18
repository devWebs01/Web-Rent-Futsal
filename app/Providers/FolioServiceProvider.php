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
                'verified',
                'checkRole:admin,dev',
            ],

            'guest/bookings/*' => [
                'auth',
                'verified',
                'checkRole:customer,dev',
            ],

            'guest/payment-records/*' => [
                'auth',
                'verified',
                'checkRole:customer,dev',
            ],
        ]);
    }
}
