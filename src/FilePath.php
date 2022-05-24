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

    public static function root() : string {
        return storage_path("app/public");
    }
    protected static function url_in_dir($item_path, $dir_path){

        $needle = FilePath::dir_of_path($item_path);
        $haystack = preg_replace('/\/{2,}/', '/', strtolower($dir_path));

        if($haystack == $needle){
            return true;
        }
        return false;
    }
    protected static function dir_of_path($url){
        $url = strtolower(preg_replace('/^(.+)\/[^\/]+$/', '/$1/', $url));
        $url = preg_replace('/\/{2,}/', '/', $url);
        return $url;
    }
    public static function relative_path($url){
        $url = FilePath::dir_of_path($url);
        return str_ireplace(FilePath::root(), '', $url);
    }
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

        $this->path_root = FilePath::root();
        
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
    public static function getTusKeysByPath($path = false){

        $cache = app('tus-server')->getCache();
        $keys = $cache->keys();
        
        $files = [];

        foreach($keys as $key){
            $file = $cache->get($key, true);
            if($path == false || FilePath::url_in_dir($file['file_path'], $path)){ //$this->getPathAbsolute())){

                $file ['key'] = $key;
                $files [$file['name']] = $file;
            }
        }
        return $files;
    }
    public static function getOrphanedTusKeys($path = false){
        
        $results = FilePath::getTusKeysByPath($path);
        foreach($results as $path => $file){
            if( (new FilePath($path))->isFile()){
                unset($results[$path]);
            }
        }
        return $results;
    }
    public function getListFiles(){

        if(!$this->isDir()){
            // error, not a directory
            return [];
        }        

        // search db for files:
        $files_in_db = File::where('dir_path','=',$this->path_rel)->get();
        $models = $files_in_db->toArray();

        // search redis for files:
        $keys_in_dir = FilePath::getTusKeysByPath($this->getPathAbsolute());

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
                    unset($keys_in_dir[$p]);
                }

                // look in db collection using the file path:
                $file_model = null;
                foreach($models as $i => $m){
                    if($m['file_path']==$relpath){
                        $file_model = $m;
                        $data['model'] = $m;
                        unset($models[$i]);
                        break;
                    }
                }

                // build look-up parameter:
                $data['lookup'] = ($file_model) ? "id={$file_model['id']}" : "path={$relpath}";

                // insert file-data into list of files:
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
                    'lookup' => "path={$relpath}",
                    'owner_name' => $os_owner_user['name'],
                    'owner_id' => $os_owner_id,
                    'permissions' => $os_permissions,
                ];
            }
        }

        return [
            'dirs' => $dirs,
            'files' => $files,
            'orphaned_models' => $models,
            'orphaned_tuskeys' => $keys_in_dir,
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
        return preg_replace('/\/[^\/]+\/?$/', '/', $this->path_rel);
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

            $newurl = $this->getDirAbs().$name;

            $newpath = new FilePath($newurl);
            if($newpath->isFile() || $newpath->isDir()) return false;

            if(rename($this->getPathAbsolute(), $newurl)){

                $this->getModel();

                $this->path_abs = $newurl;
                $this->path_rel = str_replace($this->path_root, '', $this->path_abs);

                $this->updateDB();
                return true;
            }
            return false;
        }
        return false;
    }
    public function delete(){
        if($this->isFile()){
            unlink($this->path_abs);
        }
        if($this->isDir()){
            rmdir($this->path_abs);
        }
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