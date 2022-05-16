<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Pageworks\LaravelFileManager\Http\Controllers\UploadController;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use Pageworks\LaravelFileManager\Http\Controllers\FileManageController;

Route::view('/upload','laravel-filemanager::upload')->name('files.upload.large');

Route::match(['post','put','patch','delete'], "/tus/{key?}", [UploadController::class, 'upload']);
Route::get("/tus/{key}", [UploadController::class, 'download']);