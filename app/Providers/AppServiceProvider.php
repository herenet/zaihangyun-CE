<?php

namespace App\Providers;

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
        $this->app->singleton(\App\SaaSAdmin\SaaSAdmin::class, function ($app) {
            return new \App\SaaSAdmin\SaaSAdmin();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->addNamespace('saas', resource_path('views/saas'));
        view()->addNamespace('manager', resource_path('views/manager'));
    }
}
