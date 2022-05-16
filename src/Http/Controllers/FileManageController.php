<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pageworks\LaravelFileManager\FilePath;
use Symfony\Component\Console\Output\ConsoleOutput;

class FileManageController extends BaseController {
    

    public function browse(Request $request)
    {
        
        $path = new FilePath($request);
        
        if($path->isDir()){
            
            $list = $path->getListFiles();

            return view('laravel-filemanager::files', [
                'list' => $list,
                'path' => $path,
            ]);
        }
            
    }
    public function download(Request $request){
        $request_path = $request->input('path') ?? '.';
        $root = storage_path("app/public");
        $path_abs = realpath($root.DIRECTORY_SEPARATOR.$request_path);
        $path_rel = str_replace($root, '', $path_abs);

        $isOutsideSandbox = ($path_abs === $path_rel);
        if(is_file($path_abs) && $isOutsideSandbox === false){
            return response()->download($path_abs);
        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
    public function add(Request $request){

    }
}