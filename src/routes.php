<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Pageworks\LaravelFileManager\Http\Controllers\UploadController;
use Symfony\Component\Console\Output\ConsoleOutput;

Route::view('/upload','pageworks::upload')->name('files.upload.large');
Route::get('/tmp', function(){
    
    $val = Redis::get('tus');
    var_dump($val);
    
});
Route::get('/info', function(){

    phpinfo();
    
});
Route::any("/tus/{key?}", function(Request $request, $key = null){
    (new ConsoleOutput())->writeln("/tus endpoint hit with KEY {$key} w/ {$request->method()}");
    app(UploadController::class)->server();
});

Route::get("/files/{key}", [UploadController::class, "check"]);
Route::post("/files", [UploadController::class, "start"]);
Route::patch("/files/{key}", [UploadController::class, "check"]);
Route::delete("/files/{key}", [UploadController::class, "delete"]);