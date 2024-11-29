<?php

namespace CODEIQ\Virtualizor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VirtualizorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-virtualizor')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Virtualizor::class, function ($app) {
            return new Virtualizor($app['config']['virtualizor']);
        });
    }
}
