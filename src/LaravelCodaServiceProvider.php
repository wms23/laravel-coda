<?php

namespace PhpCoda\LaravelCoda;

use Illuminate\Support\ServiceProvider;
use PhpCoda\LaravelCoda\Api\PaymentGatewayApi;
use PhpCoda\LaravelCoda\Facades\LaravelCodaFacades;

class LaravelCodaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('laravel-coda.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'laravel-coda');
        $this->registerFacade();
        $this->registerCoda();
    }

    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__.'/../config/laravel-coda.php';
    }

    private function registerFacade()
    {
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('PaymentCoda', LaravelCodaFacades::class);
        });
    }


    private function registerCoda()
    {
        $this->app->bind('coda-payment-gateway-api', function ($app) {
            $config = $app['config'];

            return new PaymentGatewayApi($config);
        });
    }
}
