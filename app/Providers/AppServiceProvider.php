<?php

namespace App\Providers;

use App\Extensions\JWTLibraryClient;
use App\Extensions\LcobucciJWT;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(JWTLibraryClient::class, LcobucciJWT::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
