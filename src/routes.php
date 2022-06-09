<?php

use Illuminate\Support\Facades\Route;
use Pageworks\LaravelFileManager\Http\Controllers\FileManageController;

if(config('laravel-filemanager.head.routes', true)){
    Route::group([
        'middleware' => config('laravel-filemanager.head.middleware', []),
        'prefix' => config('laravel-filemanager.head.prefix', '/file-manager'),
    ], function($router) {
        
        // file browsing:
        Route::get('/browse', [FileManageController::class, 'browse']);
        Route::get('/download', [FileManageController::class, 'browse']);
        Route::get('/rename', [FileManageController::class, 'rename']);
        Route::get('/delete', [FileManageController::class, 'delete']);
        Route::get('/make', [FileManageController::class, 'newdir']);

        // model manipulation:
        Route::get('/models', [FileManageController::class, 'models']);
        Route::get('/add', [FileManageController::class, 'add']);
        Route::get('/remove', [FileManageController::class, 'remove']);

        // tus & uploads mgmt:
        Route::get('/uploads', [FileManageController::class, 'tusUploads']);
        Route::get('/uploads/remove/{id}', [FileManageController::class, 'tusRemove']);
        Route::match(['post','put','patch','delete'], "/tus/{key?}", [FileManageController::class, 'tusUpload']);
        Route::get("/tus/{key}", [FileManageController::class, 'tusDownload']);
    });
}

if(config('laravel-filemanager.api.routes', true)){

    if(config('laravel-filemanager.api.view', true)){
        Route::view('/upload', 'laravel-filemanager::upload', [
            'baseUrl' => 'http://localhost:8000'.config('laravel-filemanager.api.prefix', '/api/v1/file-manager'),
            'path' => '/',
        ]);
    }

    Route::group([
        'middleware' => config('laravel-filemanager.api.middleware', []),
        'prefix' => config('laravel-filemanager.api.prefix', '/api/v1/file-manager'),
    ], function($router) {

        // file browsing:
        Route::get('/files', [FileManageController::class, 'browse']);
        Route::post('/files/model', [FileManageController::class, 'add']);
        Route::delete('/files/model', [FileManageController::class, 'remove']);
        Route::patch('/files', [FileManageController::class, 'rename']);
        Route::delete('/files', [FileManageController::class, 'delete']);
        Route::post('/dirs', [FileManageController::class, 'newdir']);

        // tus server endpoints:
        Route::match(['post','put','patch','delete'], "/tus/{key?}", [FileManageController::class, 'tusUpload']);
        Route::get("/tus/{key}", [FileManageController::class, 'tusDownload']);

        // tus key management:
        Route::get('/uploads', [FileManageController::class, 'tusUploads']);
        Route::delete('/uploads/{id}', [FileManageController::class, 'tusRemove']);
    });
}