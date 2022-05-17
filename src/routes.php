<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Pageworks\LaravelFileManager\Http\Controllers\UploadController;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use Pageworks\LaravelFileManager\Http\Controllers\FileManageController;

// file browsing:

Route::get('/files', [FileManageController::class, 'browse']);
Route::get('/download', [FileManageController::class, 'download']);
Route::get('/files/add', [FileManageController::class, 'add']);
Route::get('/files/remove', [FileManageController::class, 'remove']);

// tus & uploads:

Route::view('/upload','laravel-filemanager::upload')->name('files.upload.large');
Route::get('/uploads', function(){
    $cache = app('tus-server')->getCache();
    $keys = $cache->keys();

    echo "<h2>Files in tus cache:</h2>";

    print('<pre>');
    print_r($keys);
    print('</pre>');


    foreach($keys as $key){
        $file = $cache->get($key, true);
        echo "<div>";
        echo "<p><b>{$key}</b></p>";
        echo "<ul>";
        echo "<li>{$file['name']}</li>";
        echo "<li>{$file['file_path']}</li>";
        echo "<li>{$file['metadata']['type']}</li>";
        echo "<li><a href='/uploads/del/{$key}'>Delete</a></li>";
        echo "</ul>";
        echo "</div>";
    }
});

Route::get('/uploads/del/{id}', function(Request $request, $id){
    $cache = app('tus-server')->getCache();
    
    $fileMeta = $cache->get($id);
    $resource = $fileMeta['file_path'] ?? null;
    $isDeleted = $cache->delete($id);

    if ( ! $isDeleted || ! file_exists($resource)) {
        return response('File not found', HttpResponse::HTTP_GONE);
    }

    unlink($resource);

    return redirect('/files');
});

Route::match(['post','put','patch','delete'], "/tus/{key?}", [UploadController::class, 'upload']);
Route::get("/tus/{key}", [UploadController::class, 'download']);