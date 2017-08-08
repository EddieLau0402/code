<?php

namespace Eddie\CouponCode;


use Illuminate\Support\ServiceProvider;

class CouponCodeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
        // $this->handleMigrations();
        // $this->handleViews();
         $this->handleTranslations();
        // $this->handleRoutes();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind any implementations.
        $this->app->singleton('Eddie\CouponCode\CouponCodeManager', function ($app) {
            return new CouponCodeManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Eddie\CouponCode\CouponCodeManager'];
    }


    private function handleConfigs()
    {
        /*
         * Config path.
         */
        $configPath = realpath(__DIR__ . '/../config/coupon_code.php');

        /*
         * Publish config file.
         */
        $this->publishes([$configPath => config_path('coupon_code.php')]);

        /*
         * Merge config file.
         */
        $this->mergeConfigFrom($configPath, 'coupon_code.php');
    }

    private function handleTranslations()
    {
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../lang'), 'coupon_code');
    }

    private function handleViews()
    {
        /*
         * TODO ...
         */
        //$this->loadViewsFrom(__DIR__.'/../views', 'packagename');
        //$this->publishes([__DIR__.'/../views' => base_path('resources/views/vendor/packagename')]);
    }

    private function handleMigrations()
    {
        $this->publishes([__DIR__ . '/../migrations' => base_path('database/migrations')]);
    }

    private function handleRoutes()
    {
        include __DIR__.'/../routes.php';
    }
}