<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware(['api', 'api_version:v1'])
                ->prefix('api/v1')
                ->namespace($this->getControllerNamespace('V1'))
                ->group(base_path('routes/api_v1.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            /** @phpstan-ignore-next-line  */
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }


    /**
     * Get the Controllers namespace based on the api version
     *
     * @param string $version
     * @return string
     */
    private function getControllerNamespace(string $version): string
    {
        return "App\Http\{$version}\Controllers";
    }
}
