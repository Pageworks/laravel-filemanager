<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Pageworks\LaravelFileManager\Models\File;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pageworks\LaravelFileManager\FilePath;
use Pageworks\LaravelFileManager\Interfaces\FileRepositoryInterface;
use Pageworks\LaravelFileManager\Repositories\FileRepository;
use Symfony\Component\Console\Output\ConsoleOutput;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class FileManageController extends BaseController {
    
    private FileRepositoryInterface $repo;

    public function __construct(FileRepositoryInterface $repo) 
    {
        $this->repo = $repo;
    }
    protected function getConfiguredTusServer(Request $request){
        $server = app('tus-server');
        
        // The server sends the client a URL endpoint:
        // either /file-manager/tus or /api/v1/file-manager/tus
        // Here we determine what endpoint to send to the client

        // It seems silly to have two different endpoints, but
        // this gives us the most flexiblity w/ the config file.

        $config_type = ($request->is('api/*')) ? 'api' : 'head';
        $server->setApiPath(config("laravel-filemanager.{$config_type}.prefix", '/file-manager').'/tus'); // tus server endpoint
        
        return $server;
    }
    /**
     * If API request, a JSON response is sent. Otherwise, a blade
     * view is rendered and returned instead.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    protected function responseOrView(Request $request, Response $response){

        // send JSON:
        if(!$request->acceptsHtml()) return $response;

        // send HTML view:
        $vals = $response->getOriginalContent();
        $vals['baseUrl'] = config('laravel-filemanager.head.prefix', '/file-manager');
        return view('laravel-filemanager::files', $vals);
    }
    /**
     * If API request, a JSON response is sent. Otherwise, the user
     * is redirected to a blade view.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    protected function responseOrRedirect(Request $request, Response $response, string $overridePath = ""){
        
        // send JSON:
        if(!$request->acceptsHtml()) return $response;
        
        // redirect to HTML view:
        $path = $overridePath ? $overridePath : (new FilePath($request))->getDir();
        $prefix = config('laravel-filemanager.head.prefix', '/file-manager');
        return redirect("{$prefix}/browse?path={$path}");
    }

    // shows files and folders within a directory
    public function browse(Request $request)
    {
        $path = new FilePath($request);
        if($path->isFile()){
            return $this->repo->downloadFile($path);
        }
        $response = $this->repo->listItemsInDir($path);
        return $this->responseOrView($request, $response);
    }
    // adds a file to the database, only meta-data
    public function add(Request $request){
        $path = new FilePath($request);
        
        $response = $this->repo->addModel($path);

        return $this->responseOrRedirect($request, $response);
    }
    // removes a file from the database, does not delete the file
    public function remove(Request $request){

        $path = new FilePath($request);
        $file = $path->getModel();

        $response = $this->repo->removeModel($file);

        $path = $file ? $file->dir_path : '/';
        return $this->responseOrRedirect($request, $response, $path);
    }
    public function delete(Request $request){
        $path = new FilePath($request);
        
        $response = $this->repo->deleteFile($path);
        return $this->responseOrRedirect($request, $response, $path->getDir());
    }
    public function newdir(Request $request){
        
        $path = new FilePath($request);
        $vals = $request->validate([
            'name' => 'required|string|min:3|max:100',
        ]);
        $response = $this->repo->makeDir($path, $vals['name']);
        
        return $this->responseOrRedirect($request, $response, $path->getPathRelative());
    }
    // renames a resource
    // $path->rename() is called
    // if the resource is a directory and there are files within,
    // those files SHOULD have any related models updated
    // HOWEVER they are not currently updated...
    // this will result in orphaned rows
    public function rename(Request $request){

        $path = new FilePath($request);
        $vals = $request->validate([
            'name' => 'required|string|min:3|max:100',
        ]);
        $response = $this->repo->rename($path, $vals['name']);

        return $this->responseOrRedirect($request, $response, $path->getDir());
    }
    public function tusUploads(){
        $cache = app('tus-server')->getCache();
        $keys = $cache->keys();

        echo "<h2>Files in tus cache:</h2>";

        print('<pre>');
        print_r($keys);
        print('</pre>');

        $baseUrl = config('laravel-filemanager.head.prefix', '/file-manager');

        foreach($keys as $key){
            $file = $cache->get($key, true);
            echo "<div>";
            echo "<p><b>{$key}</b></p>";
            echo "<ul>";
            echo "<li>{$file['name']}</li>";
            echo "<li>{$file['file_path']}</li>";
            echo "<li>{$file['metadata']['type']}</li>";
            echo "<li><a href='{$baseUrl}/uploads/remove/{$key}'>Delete key</a></li>";
            echo "<li><a href='{$baseUrl}/uploads/delete/{$key}'>Delete key AND file</a></li>";
            echo "</ul>";
            echo "</div>";
        }
    }
    public function tusUpload(Request $request){

        $path = new FilePath($request);

        if($path->isDir()){
            $server = $this->getConfiguredTusServer($request);
            $server->setUploadDir(rtrim($path->getPathAbsolute(), '/'));
            $server->serve()->send();
        }
    }
    public function tusDownload(Request $request){
        return $this->getConfiguredTusServer($request)->serve()->send();
    }
    public function tusRemove(Request $request, $id){
        // get the tus cache
        $cache = $this->getConfiguredTusServer($request)->getCache();

        // find key in cache
        $cached_file = $cache->get($id, true);

        // delete the key
        $isDeleted = $cache->delete($id);

        $response = $isDeleted ? response([], 200) : response('Key not found', HttpResponse::HTTP_GONE);
        $path = $isDeleted ? (new FilePath($cached_file['file_path']))->getDir() : '/';

        // redirect
        return $this->responseOrRedirect($request, $response, $path);
    }
}