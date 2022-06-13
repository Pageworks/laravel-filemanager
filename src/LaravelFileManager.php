<?php

namespace Pageworks\LaravelFileManager;

use Illuminate\Http\Request;
use Pageworks\LaravelFileManager\Models\File;

class LaravelFileManager
{
    // determines what base-url to use for on-site urls
    public function baseUrl(Request $request = null){

        if($request){
            $config_type = ($request && $request->is('api/*')) ? 'api' : 'head';
            return config("laravel-filemanager.{$config_type}.prefix", '/file-manager');
        }

        if(config('laravel-filemanager.head.routes'))
            return config('laravel-filemanager.head.prefix', '/file-manager');

        if(config('laravel-filemanager.api.routes'))
            return config('laravel-filemanager.api.prefix', '/api/v1/file-manager');
        
        return '/file-manager';
    }
    // returns a list of tus keys, optionally filtered by path
    public function getTusKeysByPath($path = false){

        // get all tuskeys:
        $cache = app('tus-server')->getCache();
        $keys = $cache->keys();
        
        // filter to only include keys matching a specific path:
        $results = [];
        foreach($keys as $key){
            $file = $cache->get($key, true);
            if($path == false || $this->url_in_dir($file['file_path'], $path)){ //$this->getPathAbsolute())){

                $file ['key'] = $key;
                $file_path = $file['file_path'];
                //if(!array_key_exists($file_path, $results)) $results[$file_path] = [];
                //$results [$file_path] []= $file;
                $results [$key]= $file;
            }
        }
        return $results;
    }
    // returns a list of keys that don't have matching DB records:
    public function getOrphanedTusKeys($path = false){
        
        // find all keys in path
        $keys = $this->getTusKeysByPath($path);
        $only_keys = collect($keys)->pluck('key')->flatten()->toArray();

        // remove fetched rows:
        $rows = File::select()->whereIn('tuskey', $only_keys)->get();
        foreach($rows as $row){
            unset($keys[$row->tuskey]);
        }

        return $keys;
    }

    // returns true
    // if the first url is "within" the second url
    // does NOT check if actual file exists
    
    protected function url_in_dir($item_path, $dir_path){

        $folder_name = $this->dir_of_path($item_path);
        $last_part_of_path = preg_replace('/\/{2,}/', '/', strtolower($dir_path));

        return ($folder_name == $last_part_of_path);
    }

    protected function dir_of_path($url){
        $url = strtolower(preg_replace('/^(.+)\/[^\/]+$/', '/$1/', $url));
        $url = preg_replace('/\/{2,}/', '/', $url);
        return $url;
    }
    public function relative_path($url){
        $url = $this->dir_of_path($url);
        return str_ireplace(FilePath::root(), '', $url);
    }
}