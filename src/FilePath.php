<?php

namespace Pageworks\LaravelFileManager;

use Illuminate\Http\Request;

class FilePath
{
    protected $path_root = '';
    protected $path_abs = '';
    protected $path_rel = '';

    protected $ignoredFiles = ['.DS_Store','.gitignore'];
    protected $ignoredDirs = ['.'];

    public function __construct(Request $request)
    {
        $request_path = $request->input('path') ?? '.';
        $this->path_root = storage_path("app/public");
        $this->path_abs = realpath($this->path_root.DIRECTORY_SEPARATOR.$request_path);
        $this->path_rel = str_replace($this->path_root, '', $this->path_abs);
    }
    public function getListFiles(){
        $paths = [];
        $paths = scandir($this->path_abs);

        $files = [];
        $dirs = [];

        foreach($paths as $p){
            $fullpath = $this->path_abs.DIRECTORY_SEPARATOR.$p;
            $relpath = str_replace($this->path_root, '', realpath($fullpath));
    
            if(is_file($fullpath)){
                if(in_array($p, $this->ignoredFiles)) continue;
                $files []= [
                    'name' => $p,
                    'path' => $relpath,
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

        $elo = config('laravel-filemanager.eloquent-class','');
        if($elo) $elo = app($elo);

        return [
            'dirs' => $dirs,
            'files' => $files,
        ];
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
        return ($this->path_rel == "");
    }
    public function isDir(){
        return $this->isOutsideRoot() === false && is_dir($this->path_abs);
    }
}