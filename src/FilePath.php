<?php

namespace Pageworks\LaravelFileManager;

use Pageworks\LaravelFileManager\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\ConsoleOutput;

class FilePath
{
    protected $path_root = '';
    protected $path_abs = '';
    protected $path_rel = '';

    protected $ignoredFiles = ['.DS_Store','.gitignore'];
    protected $ignoredDirs = ['.'];

    public function __construct(string|Request $request = '.')
    {
        if(is_string($request)) {
            $request_path = $request;
        }
        else if($id = $request->input('id')){
            $file = File::find($id);
            $request_path = $file->file_path ?? '.';
        }
        else $request_path = $request->input('path') ?? '.';

        $this->path_root = storage_path("app/public");
        
        if(strpos($request_path, $this->path_root) === 0){
            $this->path_abs = realpath($request_path);
        } else {
            $this->path_abs = realpath($this->path_root.DIRECTORY_SEPARATOR.$request_path);
        }
        
        $this->path_rel = str_replace($this->path_root, '', $this->path_abs);

        if($this->isDir()){
            // add trailing slash:
            
            //$index = strlen($this->path_rel) - 1;
            //if($index >= 0 && $this->path_rel[$index] != '/') $this->path_rel = $this->path_rel.'/';
            
            $this->path_rel .= '/';
            $this->path_abs .= '/';
        }
    }
    public function getListFiles(){
        $paths = [];
        $paths = scandir($this->path_abs);
        
        $files = [];
        $dirs = [];

        // search db for files:
        $files_in_db = File::where('dir_path','=',$this->path_rel)->get();

        foreach($paths as $p){
            $fullpath = $this->path_abs.DIRECTORY_SEPARATOR.$p;
            $relpath = str_replace($this->path_root, '', realpath($fullpath));
    
            if(is_file($fullpath)){
                if(in_array($p, $this->ignoredFiles)) continue;

                $file_model = $files_in_db->firstWhere('file_path', $relpath);
                $id = $file_model->id ?? 0;

                $sizeBytes = Storage::size('public/'.$relpath);
                $sizeFormatted = $this->formatSize($sizeBytes);

                $files []= [
                    'name' => $p,
                    'path' => $relpath,
                    'file_id' => $id,
                    'size' => $sizeFormatted,
                    'bytes' => $sizeBytes
                ];
            } else {
                if(in_array($p, $this->ignoredDirs)) continue;
                if($p == '..' && $this->isAtRoot()) continue;
                $dirs [$p]= [
                    'name' => $p,
                    'path' => $relpath,
                ];
            }
        }

        return [
            'dirs' => $dirs,
            'files' => $files,
        ];
    }
    public function getSize(){
        $sizeBytes = Storage::size('public/'.$this->path_rel);
        //$sizeFormatted = $this->formatSize($sizeBytes);
        return $sizeBytes;
    }
    public function getPathRelative(){
        return $this->path_rel;
    }
    public function getPathAbsolute(){
        return $this->path_abs;
    }
    public function isOutsideRoot(){
        return ($this->path_abs === $this->path_rel);
    }
    public function isAtRoot(){
        return ($this->path_rel == '/');
    }
    public function isDir(){
        return $this->isOutsideRoot() === false && is_dir($this->path_abs);
    }
    public function isFile(){
        return $this->isOutsideRoot() === false && is_file($this->path_abs);
    }
    public function getDir(){
        return preg_replace('/\/[^\/]+$/', '/', $this->path_rel);
    }
    public function getFileName(){
        return preg_replace('/^.*\/([^\/]+)$/', '$1', $this->path_rel);
    }
    public function addToDB(){
        
        if(!$this->isFile()) return null;

        return \Pageworks\LaravelFileManager\Models\File::create([
            'file_name' => $this->getFileName(),
            'file_path' => $this->getPathRelative(),
            'dir_path' => $this->getDir(),
            'size' => $this->getSize(),
        ]);
    }
    public function delete(){
        if(!$this->isFile()) return;
        unlink($this->path_abs);
    }
    protected function formatSize($size){

        if ($size >= 1073741824) {
            return round($size / 1024 / 1024 / 1024,1) . 'GB';
        } else if ($size >= 1048576) {
            return round($size / 1024 / 1024,1) . 'MB';
        } else if($size >= 1024) {
            return round($size / 1024,1) . 'KB';
        } else {
            return $size . ' bytes';
        }
    }
}