<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pageworks\LaravelFileManager\FilePath;
use Symfony\Component\Console\Output\ConsoleOutput;

class UploadController extends BaseController {
    

    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function upload(Request $request){

        $path = new FilePath($request);

        if($path->isDir()){
            $server = app('tus-server');
            $server->setUploadDir($path->getPathAbsolute());
            $server->serve()->send();
        }
    }
    public function download(){
        return app('tus-server')->serve()->send();
    }
}