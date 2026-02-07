<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
{
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}

    /**
     * Bootstrap any application services.
     */
   /* public function boot(): void
    {
        //
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }//Comentar todo si no funciona
    }*/


}
