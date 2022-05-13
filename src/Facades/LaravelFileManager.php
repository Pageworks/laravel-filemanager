<?php

namespace Pageworks\LaravelFileManager\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelFileManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-filemanager';
    }
}
