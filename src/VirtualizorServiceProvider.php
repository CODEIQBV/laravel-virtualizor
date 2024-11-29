<?php

namespace CODEIQ\Virtualizor;

use Illuminate\Support\ServiceProvider;
use CODEIQ\Virtualizor\Services\AdminServices;
use CODEIQ\Virtualizor\Api\AdminApi;

class VirtualizorServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the admin API
        $this->app->singleton(AdminApi::class, function ($app) {
            $config = $app['config']['virtualizor'];
            return new AdminApi(
                $config['admin']['key'],
                $config['admin']['pass'],
                $config['admin']['ip'],
                $config['admin']['port']
            );
        });

        // Bind the admin services
        $this->app->singleton('virtualizor.admin', function ($app) {
            return new AdminServices($app->make(AdminApi::class));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/virtualizor.php' => config_path('virtualizor.php'),
        ], 'virtualizor-config');
    }
}
