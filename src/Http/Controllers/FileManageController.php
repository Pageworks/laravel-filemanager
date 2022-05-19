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
        } else {
            // path->isDir() also checks whether directory is within the root directory
            
            return response("<h1>Directory not found</h1><h2><a href='/files'>Back to /</h2>", 404);
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

            $file = $path->addToDB();

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
    public function delete(Request $request){
        $path = new FilePath($request);
        if($path->isFile()){

            $path->delete();

            return redirect('/files?path='.$path->getDir());
        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
    // renames a resource
    // $path->rename() is called
    // if the resource is a directory and there are files within,
    // those files SHOULD have any related models updated
    // HOWEVER they are not currently updated...
    // this will result in orphaned rows
    public function rename(Request $request){
        $path = new FilePath($request);
        if($path->isFile() || $path->isDir()){

            $vals = $request->validate([
                'name' => 'required|string|min:3|max:100',
            ]);
            $path->rename($vals['name']);
            
            return redirect('/files?path='.$path->getDir());
        }
        return response(['error' => 'file not found'], 404);
    }
}