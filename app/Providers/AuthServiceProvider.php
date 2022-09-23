<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Extensions\JWTGuard;
use App\Extensions\JWTLibraryClient;
use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // add custom guard
        Auth::extend('jwt', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            if ($provider !== null) {
                return new JWTGuard($provider, $app->make('request'), $app->make(JWTLibraryClient::class));
            }

            throw new InvalidArgumentException('UserProvider cannot be null');
        });
    }
}
