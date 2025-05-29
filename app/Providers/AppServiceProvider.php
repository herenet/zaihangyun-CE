<?php

namespace App\Providers;

use App\Libs\JsonRedisStore;
use Illuminate\Support\Facades\Cache;
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
        Cache::extend('json-redis', function ($app, $config) {
            $redis = $app['redis'];
            $connection = $config['connection'] ?? 'default';
            $prefix = $this->getPrefix($config);
            
            return Cache::repository(
                new JsonRedisStore($redis, $prefix, $connection)
            );
        });

        view()->addNamespace('saas', resource_path('views/saas'));
        view()->addNamespace('manager', resource_path('views/manager'));
    }

    protected function getPrefix(array $config)
    {
        return $config['prefix'] ?? $this->app['config']['cache.prefix'];
    }
}
