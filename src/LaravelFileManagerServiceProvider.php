<?php

namespace Pageworks\LaravelFileManager;

use Illuminate\Support\ServiceProvider;
use TusPhp\Tus\Server as TusServer;
use TusPhp\Events\TusEvent;

use Pageworks\LaravelFileManager\Events\TusUploadStart;
use Pageworks\LaravelFileManager\Events\TusUploadProgress;
use Pageworks\LaravelFileManager\Events\TusUploadMerged;
use Pageworks\LaravelFileManager\Events\TusUploadComplete;

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'pageworks');
        
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-filemanager');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }
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

        $this->app->singleton('laravel-filemanager', function ($app) {
            return new LaravelFileManager;
        });

        $this->app->singleton('tus-server', function ($app) {
            
            $server = new TusServer('file');
            
            $server->setApiPath('/files/tus'); // tus server endpoint.
            $server->setUploadDir(storage_path('app/public'));

            $server->event()->addListener('tus-server.upload.created', function(TusEvent $e){ event(new TusUploadStart($e)); });
            $server->event()->addListener('tus-server.upload.progress', function(TusEvent $e){ event(new TusUploadProgress($e)); });
            $server->event()->addListener('tus-server.upload.complete', function(TusEvent $e){ event(new TusUploadComplete($e)); });
            $server->event()->addListener('tus-server.upload.merged', function(TusEvent $e){ event(new TusUploadMerged($e)); });

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
