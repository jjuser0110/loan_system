<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Set locale from session on every request
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }
    }
}