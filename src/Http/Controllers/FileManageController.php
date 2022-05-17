<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Pageworks\LaravelFileManager\Models\File;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pageworks\LaravelFileManager\FilePath;
use Symfony\Component\Console\Output\ConsoleOutput;

class FileManageController extends BaseController {
    

    // shows files and folders within a directory
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
    // downloads a file
    public function download(Request $request){
        $path = new FilePath($request);

        if($path->isFile()){
            return response()->download($path->getPathAbsolute());
        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
    // adds a file to the database, only meta-data
    public function add(Request $request){
        $path = new FilePath($request);
        if($path->isFile()){

            $file = File::factory()->create([
                'file_path' => $path->getPathRelative(),
                'dir_path' => $path->getDir(),
            ]);

            return redirect('/files?path='.$file->dir_path);
        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
    // removes a file from the database, does not delete the file
    public function remove(Request $request){
        $id = $request->input("id");
        $file = File::find($id);
        if($file){

            $redirect = $file->dir_path;
            // remove from database:
            $file->delete();

            return redirect('/files?path='.$redirect);
        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
}