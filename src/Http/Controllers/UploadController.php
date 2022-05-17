<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Console\Output\ConsoleOutput;

class UploadController extends BaseController {
    

    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function upload(){
        app('tus-server')->serve()->send();
    }
    public function download(){
        return app('tus-server')->serve()->send();
    }
}