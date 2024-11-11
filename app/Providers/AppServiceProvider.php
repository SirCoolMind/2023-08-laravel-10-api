<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dedoc Scramble Setup
        \Dedoc\Scramble\Scramble::afterOpenApiGenerated(function (\Dedoc\Scramble\Support\Generator\OpenApi $openApi) {
            $openApi->secure(
                \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer')
            );
        });

        \Illuminate\Support\Facades\Gate::define('viewApiDocs', function (\App\Models\User $user) {
            // Gate viewApiDocs is for environment other than local
            return in_array($user?->email, ['hafizcoolman@gmail.com']);
        });
        // END Dedoc Scramble Setup
    }
}
