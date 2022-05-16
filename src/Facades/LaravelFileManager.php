<?php

namespace Pageworks\LaravelFileManager\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelFileManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-filemanager';
    }
}
