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

    protected $model = null;

    public function __construct(string|Request $request = '.')
    {
        if(is_string($request)) {
            $request_path = $request;
        }
        else if($id = $request->input('id')){
            $this->model = File::find($id);
            $request_path = $this->model->file_path ?? '.';
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
            
            $this->path_rel .= '/';
            $this->path_abs .= '/';
        }
    }
    public function getListFiles(){

        if(!$this->isDir()){
            // error, not a directory
            return [];
        }        

        // search db for files:
        $files_in_db = File::where('dir_path','=',$this->path_rel)->get();

        // search redis for files:
        $cache = app('tus-server')->getCache();
        $keys = $cache->keys();
        $keys_in_dir = [];
        foreach($keys as $key){
            $file = $cache->get($key, true);
            $temp = new FilePath($file['file_path']);
            if($temp->getDir() == $this->path_rel)
                $keys_in_dir [$file['name']] = $key;
        }

        // search the directory:
        $paths = [];
        $paths = scandir($this->path_abs);
        $files = [];
        $dirs = [];
        foreach($paths as $p){
            $fullpath = $this->path_abs.DIRECTORY_SEPARATOR.$p;
            $relpath = str_replace($this->path_root, '', realpath($fullpath));
    
            if(is_file($fullpath)){
                if(in_array($p, $this->ignoredFiles)) continue;

                // get filesystem info:

                $sizeBytes = Storage::size('public/'.$relpath);
                $sizeFormatted = $this->formatSize($sizeBytes);
                $stats = lstat($this->path_abs);
                $os_owner_id = fileowner($this->path_abs);
                $os_owner_user = posix_getpwuid($os_owner_id);
                $os_permissions = substr(sprintf('%o', fileperms($this->path_abs)), -4);

                $data = [
                    'name' => $p,
                    'path' => $relpath,
                    'size' => $sizeFormatted,
                    'bytes' => $sizeBytes,
                    'location_rel' => $this->path_rel,
                    'location_abs' => $this->path_abs,
                    'owner_name' => $os_owner_user['name'],
                    'owner_id' => $os_owner_id,
                    'permissions' => $os_permissions,
                    'atime' => $stats['atime'],
                    'mtime' => $stats['mtime'],
                    'ctime' => $stats['ctime'],
                ];

                // look in list of keys:
                if(array_key_exists($p, $keys_in_dir)){
                    $data['tus_key'] = $keys_in_dir[$p];
                }

                // look in db collection using the file path:
                $file_model = $files_in_db->firstWhere('file_path', $relpath);
                if($file_model){
                    $data['model'] = $file_model->toArray();
                }

                // build urls for hateoas
                $urls = [];
                $lookup = '';

                
                if($file_model){
                    $lookup = "id={$file_model->id}";
                    $urls['remove'] = "/files/remove?{$lookup}";
                } else {
                    $lookup = "path={$relpath}";
                    $urls['add'] = "/files/add?{$lookup}";
                }
                if(array_key_exists('tus_key', $data)){
                    $urls['remove-upload-key'] = "/files/uploads/remove/{$data['tus_key']}";
                }
                
                $urls['download'] = "/files/download?{$lookup}";
                $urls['delete'] = "/files/delete?{$lookup}";
                $urls['rename'] = "/files/rename?{$lookup}";

                $data['urls'] = $urls;

                $files []= $data;

            } else {

                $os_owner_id = fileowner($this->path_abs);
                $os_owner_user = posix_getpwuid($os_owner_id);
                $os_permissions = substr(sprintf('%o', fileperms($this->path_abs)), -4);

                if(in_array($p, $this->ignoredDirs)) continue;
                if($p == '..' && $this->isAtRoot()) continue;
                $dirs [$p]= [
                    'name' => $p,
                    'path' => $relpath,
                    'urls' => [
                        'browse' => "/files?path={$relpath}",
                        'rename' => "/files/rename?path={$relpath}",
                    ],
                    'owner_name' => $os_owner_user['name'],
                    'owner_id' => $os_owner_id,
                    'permissions' => $os_permissions,
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
    public function getDirAbs(){
        return preg_replace('/\/[^\/]+\/?$/', '/', $this->path_abs);
    }
    public function getFileName(){
        return preg_replace('/^.*\/([^\/]+)$/', '$1', $this->path_rel);
    }
    public function getModel(){
        if($this->model){
            return $this->model;
        }
        $this->model = \Pageworks\LaravelFileManager\Models\File::where('file_path','=',$this->getPathRelative())->first();
        return $this->model;
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
    public function updateDB(){
        //$model = $this->getModel();
        if($this->model) $this->model->update([
            'file_name' => $this->getFileName(),
            'file_path' => $this->getPathRelative(),
            'dir_path' => $this->getDir(),
            'size' => $this->getSize(),
        ]);
    }
    public function rename($name){

        if($this->isDir()){
            // TODO: check for links in DB
        }


        if($this->isFile() || $this->isDir()){

            $newpath = $this->getDirAbs().$name;

            if(rename($this->getPathAbsolute(), $newpath)){

                $this->getModel();

                $this->path_abs = $newpath;
                $this->path_rel = str_replace($this->path_root, '', $this->path_abs);

                $this->updateDB();
                return true;
            }
            return false;
        }
        return false;
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