<?php

namespace Pageworks\LaravelFileManager\Repositories;

use Illuminate\Http\Response;
use Pageworks\LaravelFileManager\FilePath;
use Pageworks\LaravelFileManager\Interfaces\FileRepositoryInterface;
use Pageworks\LaravelFileManager\Models\File;
use Symfony\Component\Console\Output\ConsoleOutput;

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

        (new ConsoleOutput())->writeln("attempting to make dir: ".$dir);

        if(mkdir($dir)){
            return response([], 200);
        }
        return response(['error' => 'something bogus happened'], 403);
    }
    public function rename(FilePath $dir_path, string $name){
        if($dir_path->isFile() || $dir_path->isDir()){

            if($dir_path->isAtRoot() || $dir_path->isOutsideRoot()) return response(['error' => 'not allowed'], 401);

            $dir_path->rename($name);
            
            return response([], 200);
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

            return response([], 200);
        } else {
            return response([], 404);
        }
    }
    public function removeModel(File $file){
        if($file){

            if(config('laravel-filemanager.debug.disable_cleanup', false)){
            
                // pretend to remove model
                
            } else {

                // remove model for real
                $file->delete();
            }
            
            return response([], 204);

        } else {
            return response(['error' => 'file not found'], 404);
        }
    }
}