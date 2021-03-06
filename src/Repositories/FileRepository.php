<?php

namespace Pageworks\LaravelFileManager\Repositories;

use Illuminate\Http\Response;
use Pageworks\LaravelFileManager\FilePath;
use Pageworks\LaravelFileManager\Interfaces\FileRepositoryInterface;
use Pageworks\LaravelFileManager\Models\File;
use Pageworks\LaravelFileManager\Events\DirectoryCreated;
use Pageworks\LaravelFileManager\Events\DirectoryDeleted;
use Pageworks\LaravelFileManager\Events\DirectoryRenamed;
use Pageworks\LaravelFileManager\Events\FileDeleted;
use Pageworks\LaravelFileManager\Events\FileModelAdded;
use Pageworks\LaravelFileManager\Events\FileModelRemoved;
use Pageworks\LaravelFileManager\Events\FileRenamed;

class FileRepository implements FileRepositoryInterface {

    protected function valsFromPath(FilePath $path){
        return [
            'path' => $path->getPathRelative(),
            'pathAbs' => $path->getPathAbsolute(),
        ];
    }
    public function listItemsInDir(FilePath $dir_path) : Response {

        $vals = $this->valsFromPath($dir_path);

        if($dir_path->isDir()){

            $list = $dir_path->getListFiles();
            $vals ['list'] = $list;
            return response($vals, 200);

        } else {
            return response($vals, 404);
        }
    }
    public function makeDir(FilePath $dir_path, string $name){
        if($dir_path->isOutsideRoot()) return response(['error' => 'not allowed'], 401);
        if(!$dir_path->isDir()) return response(['error' => 'directory not found'], 404);
        
        $dir = $dir_path->getPathAbsolute() . $name;
        
        if(is_dir($dir) || is_file($dir)) return response(['error' => 'file exists'], 403);

        if(mkdir($dir)){
            event(new DirectoryCreated(new FilePath($dir)));
            return response([], 200);
        }
        return response(['error' => 'something bogus happened'], 403);
    }
    public function rename(FilePath $dir_path, string $name){

        $isDir = $dir_path->isDir();
        if($dir_path->isFile() || $isDir){

            if($dir_path->isAtRoot() || $dir_path->isOutsideRoot()) return response(['error' => 'not allowed'], 401);

            $old_path = $dir_path->copy();

            if($dir_path->rename($name)){
                event( $isDir ? new DirectoryRenamed($old_path, $dir_path) : new FileRenamed($old_path, $dir_path));
                return response([], 200);
            } 
            return response(['error' => 'file could not be renamed'], 409);
        }
        return response(['error' => 'file not found'], 404);
    }
    public function downloadFile(FilePath $file_path) {

        if($file_path->isFile()){
            return response()->download($file_path->getPathAbsolute());
        } else {
            return response([], 404);
        }
    }
    public function deleteFile(FilePath $file_path){
        if($file_path->isAtRoot() || $file_path->isOutsideRoot()) return response(['error' => 'not allowed'], 401);
        if($file_path->isFile()){
            $file_path->delete();
            event(new FileDeleted($file_path));
            return response([], 204);
        } 
        else if($file_path->isDir()){

            $files = array_diff(scandir($file_path->getPathAbsolute()), array('.','..'));

            $regular_files = array_diff($files, array('.DS_Store'));
            $hidden_files = array_diff($files, $regular_files);

            if(count($regular_files) > 0) {
                return response(['error' => 'directory not empty'], 403);
            }
            else if(count($hidden_files) > 0 && count($regular_files) == 0){
                foreach($hidden_files as $hidden_file){
                    unlink($file_path->getPathAbsolute().$hidden_file);
                }
            }

            $file_path->delete();
            event(new DirectoryDeleted($file_path));

            return response([], 204);
        } 
        else {
            return response(['error' => 'file not found'], 404);
        }
    }
    public function addModel(FilePath $file_path){

        //$vals = $this->valsFromPath($file_path);

        if($file_path->isFile()){

            $file = $file_path->addToDB();

            event(new FileModelAdded($file_path));

            return response([], 200);
        } else {
            return response([], 404);
        }
    }
    public function removeModel(File $file){
        if($file){

            $path = new FilePath($file->file_path);

            // remove the model
            $file->delete();

            event(new FileModelRemoved($path));

            return response([], 204);

        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
}