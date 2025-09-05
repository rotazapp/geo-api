<?php

namespace Rotaz\GeoData\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;
class GeoDataServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('geo-data')
            ->hasConfigFile('geo-data')
            ->hasMigration('create_geo_data_table')
            ->hasCommands([
                \Rotaz\GeoData\Commands\GeoDataLoader::class,
            ])
            ->runsMigrations(true);
    }

    /**
     * Register any application services.
     */
    public function packageRegistered(): void
    {

        $this->app->singleton('GeoDataService', function ($app) {
            return new \Rotaz\GeoData\Services\GeoDataService(new \Rotaz\GeoData\Providers\ViaCepProvider($app));
        });


    }

    public function packageBooted(): void
    {

    }

}
