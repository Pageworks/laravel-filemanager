<?php

namespace Pageworks\LaravelFileManager;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use TusPhp\Tus\Server as TusServer;
use TusPhp\Events\TusEvent;

use Pageworks\LaravelFileManager\Events\DirectoryDeleted;
use Pageworks\LaravelFileManager\Events\DirectoryRenamed;
use Pageworks\LaravelFileManager\Events\FileDeleted;
use Pageworks\LaravelFileManager\Events\FileModelAdded;
use Pageworks\LaravelFileManager\Events\FileModelRemoved;
use Pageworks\LaravelFileManager\Events\FileRenamed;
use Pageworks\LaravelFileManager\Events\DirectoryCreated;
use Pageworks\LaravelFileManager\Events\FileUploaded;
use Pageworks\LaravelFileManager\Events\TusUploadStart;
use Pageworks\LaravelFileManager\Events\TusUploadProgress;
use Pageworks\LaravelFileManager\Events\TusUploadMerged;
use Pageworks\LaravelFileManager\Events\TusUploadComplete;
use Pageworks\LaravelFileManager\Interfaces\FileRepositoryInterface;
use Pageworks\LaravelFileManager\Repositories\FileRepository;
use Symfony\Component\Console\Output\ConsoleOutput;

//require __DIR__.'/../vendor/autoload.php';

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        // publish config file:
        $this->publishes([
            __DIR__.'/../config/laravel-filemanager.php' => config_path('laravel-filemanager.php'),
        ]);
        // publish files in /public/
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/pageworks/laravel-filemanager'), 
        ], 'public');

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'pageworks');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-filemanager');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->addEventListeners();

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-filemanager.php', 'laravel-filemanager');


        // default FileRepositoryInterface is FileRepository
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);

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
            $server->setApiPath(app('laravel-filemanager')->baseUrl().'/tus');
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
    protected function addEventListeners(){
        Event::listen(function(DirectoryCreated $event){(new ConsoleOutput())->writeln("==> event: directory created: {$event->path->getPathRelative()}");});
        Event::listen(function(DirectoryDeleted $event){(new ConsoleOutput())->writeln("==> event: directory deleted: {$event->path->getPathRelative()}");});
        Event::listen(function(DirectoryRenamed $event){(new ConsoleOutput())->writeln("==> event: directory renamed: {$event->path_from->getPathRelative()} to {$event->path_to->getPathRelative()}");});
        Event::listen(function(FileDeleted $event){(new ConsoleOutput())->writeln("==> event: file deleted: {$event->path->getPathRelative()}");});
        Event::listen(function(FileModelAdded $event){(new ConsoleOutput())->writeln("==> event: file model added: {$event->path->getPathRelative()}");});
        Event::listen(function(FileModelRemoved $event){(new ConsoleOutput())->writeln("==> event: file model removed: {$event->path->getPathRelative()}");});
        Event::listen(function(FileRenamed $event){(new ConsoleOutput())->writeln("==> event: file renamed: {$event->path_from->getPathRelative()} to {$event->path_to->getPathRelative()}");});
        Event::listen(function(FileUploaded $event){(new ConsoleOutput())->writeln("==> event: file uploaded: {$event->path->getPathRelative()}");});
    }
}
