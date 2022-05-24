<?php

namespace Pageworks\LaravelFileManager;

use Illuminate\Http\Request;

class LaravelFileManager
{
    public function baseUrl(Request $request = null){

        if($request){
            $config_type = ($request && $request->is('api/*')) ? 'api' : 'head';
            return config("laravel-filemanager.{$config_type}.prefix", '/file-manager');
        }

        if(config('laravel-filemanager.head.routes'))
            return config('laravel-filemanager.head.prefix', '/file-manager');

        if(config('laravel-filemanager.api.routes'))
            return config('laravel-filemanager.api.prefix', '/api/v1/file-manager');
        
        return '/file-manager';
    }
}