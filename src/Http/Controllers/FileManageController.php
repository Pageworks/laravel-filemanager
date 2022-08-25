<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Pageworks\LaravelFileManager\Models\File;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pageworks\LaravelFileManager\FilePath;
use Pageworks\LaravelFileManager\Interfaces\FileRepositoryInterface;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
/**
 * @group File Endpoints
 */
class FileManageController extends BaseController {
    
    private FileRepositoryInterface $repo;

    public function __construct(FileRepositoryInterface $repo) 
    {
        $this->repo = $repo;
    }
    protected function getConfiguredTusServer(Request $request){
        
        // The server sends the client a URL endpoint:
        // either /file-manager/tus or /api/v1/file-manager/tus
        // Here we determine what endpoint to send to the client
        
        // It seems silly to have two different endpoints, but
        // this gives us the most flexiblity w/ the config file.
        
        $endpoint = app('laravel-filemanager')->baseUrl($request).'/tus';
        $server = app('tus-server');
        $server->setApiPath($endpoint);
        
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
    protected function responseOrView(Response $response, string $view = 'laravel-filemanager::files'){

        // send JSON:
        $request = request();
        if(!$request->acceptsHtml()) return $response;

        // send HTML view:
        $vals = $response->getOriginalContent();
        $vals['baseUrl'] = app('laravel-filemanager')->baseUrl($request);

        return view($view, $vals);
    }

    /**
     * If API request, a JSON response is sent. Otherwise, the user
     * is redirected to a blade view.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    protected function responseOrRedirect(Response $response, string $overridePath = '/'){
        
        $request = request();

        // send JSON:
        if(!$request->acceptsHtml()) return $response;
        
        // redirect to HTML view:
        $prefix = config('laravel-filemanager.head.prefix', '/file-manager');
        
        $ref_from_head = $request->headers->get('referer');
        
        $referrer = preg_replace('/^[^\?]+(\/[a-zA-Z0-9\-_]+\??)(.*)?$/','$1', $ref_from_head);

        $endpoint = $referrer ?? '/';

        //echo $endpoint ."  from " . $ref_from_head; exit;

        $path = $overridePath ?? (new FilePath($request))->getDir();

        return redirect("{$prefix}{$endpoint}?path={$path}");
    }

    /**
     * Fetch directory contents
     */
    public function browse(Request $request)
    {
        $path = new FilePath($request);
        if($path->isFile()){
            return $this->repo->downloadFile($path);
        }
        $response = $this->repo->listItemsInDir($path);

        return $this->responseOrView($response,'laravel-filemanager::files');
    }
    /**
     * Fetch files in DB
     */
    public function models(Request $request){
        
        $all = File::all();
        $orphaned = [];
        foreach($all as $file){
            if(!$file->file_exists()) $orphaned []= $file->toArray();
        }
        $response = response([
            'total_models' => $all->count(),
            'orphaned_models' => $orphaned,
            'path' => '/',
            'baseUrl' => app('laravel-filemanager')->baseUrl($request),
        ], 200);

        return $this->responseOrView($response,'laravel-filemanager::models');
    }
    /**
     * Add a file to DB
     * 
     * A record is added to the DB. This does NOT upload or otherwise create an actual file.
     */
    public function add(Request $request){
        $path = new FilePath($request);
        
        $response = $this->repo->addModel($path);

        return $this->responseOrRedirect($response);
    }
    /**
     * Remove a file from DB
     * 
     * A record is deleted. This does NOT delete an actual file.
     */
    public function remove(Request $request){

        $path = new FilePath($request);
        $file = $path->getModel();

        $response = $this->repo->removeModel($file);

        $path = $file ? $file->dir_path : '/';
        return $this->responseOrRedirect($response, $path);
    }
    /**
     * Delete a file
     * 
     * If the file has a tus key or a record in the DB, both are deleted.
     */
    public function delete(Request $request){
        $path = new FilePath($request);
        
        $response = $this->repo->deleteFile($path);
        
        return $this->responseOrRedirect($response, $path->getDir());
    }
    /**
     * Create a directory
     */
    public function newdir(Request $request){
        
        $path = new FilePath($request);
        $vals = $request->validate([
            'name' => 'required|string|min:3|max:100',
        ]);
        $response = $this->repo->makeDir($path, $vals['name']);
        
        return $this->responseOrRedirect($response, $path->getPathRelative());
    }
    /**
     * Rename a file / dir
     * 
     * $path->rename() is called
     * If the resource is a directory and there are files within,
     * those files SHOULD have any related models updated, but they
     * do not at this time which results in orphaned rows.
     */
    public function rename(Request $request){

        $path = new FilePath($request);
        $vals = $request->validate([
            'name' => 'required|string|min:3|max:100',
        ]);
        $response = $this->repo->rename($path, $vals['name']);

        return $this->responseOrRedirect($response, $path->getDir());
    }
    /**
     * Fetch tus uploads
     */
    public function tusUploads(Request $request){

        $lfm = app('laravel-filemanager');

        $keys_found = 0;
        $keys_found = count(app('tus-server')->getCache()->keys());

        $response = response([
            'baseUrl' => $lfm->baseUrl($request),
            'path' => '/',
            'orphaned_tuskeys' => $lfm->getOrphanedTusKeys(),
            'total_keys' => $keys_found,
        ], 200);

        return $this->responseOrView($response, 'laravel-filemanager::tuskeys');
    }
    /**
     * Connect to tus server
     */
    public function tusUpload(Request $request){

        $path = new FilePath($request);
        
        if($path->isDir()){
            $server = $this->getConfiguredTusServer($request);
            $server->setUploadDir(rtrim($path->getPathAbsolute(), '/'));
            $response = $server->serve();
            
            return $response;
        }
    }
    /**
     * Download a file vis tus
     */
    public function tusDownload(Request $request, $key){
        $server = $this->getConfiguredTusServer($request);
        $response = $server->serve();

        // if key found:
        if($response->getStatusCode() == 200){

            // lookup model, inject into results:
            
            // find key in the tus cache
            $cached_file = $server->getCache()->get($key, true);
            
            // lookup model in db:
            $model = (new FilePath($cached_file['file_path']))->getModel();

            // copy headers out of original response
            //$headers = []; 
            //foreach($response->headers->all() as $header=>$val) {
            //    $headers[$header] = $val[0];
            //}
            // build new resonse with model:
            //return response()->json(['file'=>$model])->withHeaders($headers);

            $response->setContent(json_encode(['file' => $model]));
            $response->headers->set('Content-Type', 'application/json');
        }
        return $response;
    }
    /**
     * Delete tus key
     * 
     * This removes the tus upload key from the file (or redis) cache. The file is NOT deleted.
     */
    public function tusRemove(Request $request, $id){
        // get the tus cache
        $cache = $this->getConfiguredTusServer($request)->getCache();

        // find key in cache
        $cached_file = $cache->get($id, true);
        $path_to_file = $cached_file['file_path'];

        // delete the key
        $isDeleted = $cache->delete($id);

        $response = $isDeleted ? response([], 200) : response('Key not found', HttpResponse::HTTP_GONE);
        $path = $isDeleted ? app('laravel-filemanager')->relative_path($path_to_file) : '/';

        // redirect
        return $this->responseOrRedirect($response, $path);
    }
}