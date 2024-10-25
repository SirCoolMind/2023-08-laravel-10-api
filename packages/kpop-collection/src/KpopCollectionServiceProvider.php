<?php

namespace HafizRuslan\KpopCollection;

use Illuminate\Support\ServiceProvider;

class KpopCollectionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
    }
}
