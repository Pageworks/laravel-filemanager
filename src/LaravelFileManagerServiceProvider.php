<?php

namespace Pageworks\LaravelFileManager;

use Illuminate\Support\ServiceProvider;
use TusPhp\Tus\Server as TusServer;

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'pageworks');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pageworks');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-filemanager.php', 'laravel-filemanager');

        // write file cache settings
 
        \TusPhp\Config::set([
            'file' => [
                'dir' => '/tmp/',
                'name' => 'tus_php.cache',
            ]
        ]);

        // Register the service the package provides.
        $this->app->singleton('laravel-filemanager', function ($app) {
            return new LaravelFileManager;
        });

        $this->app->singleton('tus-server', function ($app) {

            $server = new TusServer('redis');
            $server->setApiPath('/tus'); // tus server endpoint.
            $server->setUploadDir(storage_path('app/public'));

            return $server;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-filemanager'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravel-filemanager.php' => config_path('laravel-filemanager.php'),
        ], 'laravel-filemanager.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/pageworks'),
        ], 'laravel-filemanager.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/pageworks'),
        ], 'laravel-filemanager.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/pageworks'),
        ], 'laravel-filemanager.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
