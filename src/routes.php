<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Pageworks\LaravelFileManager\FilePath;
use Pageworks\LaravelFileManager\Http\Controllers\UploadController;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use Pageworks\LaravelFileManager\Http\Controllers\FileManageController;

// file browsing:

Route::get('/files', [FileManageController::class, 'browse']);
Route::get('/files/download', [FileManageController::class, 'download']);
Route::get('/files/add', [FileManageController::class, 'add']);
Route::get('/files/remove', [FileManageController::class, 'remove']);
Route::get('/files/rename', [FileManageController::class, 'rename']);
Route::get('/files/delete', [FileManageController::class, 'delete']);
Route::get('/files/make', [FileManageController::class, 'newdir']);

// tus & uploads:

Route::get('/files/uploads', function(){
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
        echo "<li><a href='/files/uploads/remove/{$key}'>Delete key</a></li>";
        echo "<li><a href='/files/uploads/delete/{$key}'>Delete key AND file</a></li>";
        echo "</ul>";
        echo "</div>";
    }
});

Route::get('/files/uploads/remove/{id}', function(Request $request, $id){

    // get the tus casche
    $cache = app('tus-server')->getCache();

    // find key in cache
    $cached_file = $cache->get($id, true);

    // find path to the referenced file; used for the redirect below
    $path = new FilePath($cached_file['file_path']);

    // delete the key
    $isDeleted = $cache->delete($id);

    if ( ! $isDeleted ) {
        return response('Key not found', HttpResponse::HTTP_GONE);
    }
    // redirect
    return redirect('/files?path='.$path->getDir());
});
Route::get('/files/uploads/delete/{id}', function(Request $request, $id){
    $cache = app('tus-server')->getCache();
    
    $fileMeta = $cache->get($id);
    $resource = $fileMeta['file_path'] ?? null;
    $isDeleted = $cache->delete($id);

    if ( ! $isDeleted || ! file_exists($resource)) {
        return response('File not found', HttpResponse::HTTP_GONE);
    }

    unlink($resource);

    return redirect('/files/uploads');
});

Route::match(['post','put','patch','delete'], "/files/tus/{key?}", [UploadController::class, 'upload']);
Route::get("/files/tus/{key}", [UploadController::class, 'download']);