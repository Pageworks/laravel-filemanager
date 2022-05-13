<?php

namespace Pageworks\LaravelFileManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Console\Output\ConsoleOutput;

class UploadController extends BaseController {
    

    public function __construct()
    {
        //$this->middleware('auth');
    }

    protected function extractValuesFromHeaders(Request $request){

        // Uppy seems to use the following metadata by default:
        //   - relativePath (null)
        //   - name
        //   - type
        //   - filetype
        //   - filename

        $vals = [];
        $meta = explode(',', $request->header('Upload-Metadata'));
        foreach($meta as $chunk){
            $keyval = explode(' ',$chunk);
            $vals[$keyval[0]] = base64_decode($keyval[1]);
        }
        return $vals;
    }

    public function server(){
        app('tus-server')->serve()->send();
    }

    public function start(Request $request){
        
        $vals = $this->extractValuesFromHeaders($request);
        
        //print_r($vals); exit;

        $log = new ConsoleOutput();
        $log->writeln('TUS client being created...');
        
        $url = 'http://127.0.0.1:8000/tus';
        $key = uniqid();
        $client = new \TusPhp\Tus\Client($url);


        $cache = $client->getCache()->getCacheDir();

        echo $cache;

        $client->setKey($key)->file($vals['filename'], $vals['filename'])->upload();

        // upload whole file:
        //$client->file($vals['filename'], $vals['filename'])->upload();
    }
    public function resume($key){

    }
    public function check($key){

    }
    public function destroy($key){

    }
}