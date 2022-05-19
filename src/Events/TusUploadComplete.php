<?php

namespace Pageworks\LaravelFileManager\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Pageworks\LaravelFileManager\FilePath;
use Symfony\Component\Console\Output\ConsoleOutput;
use TusPhp\Events\TusEvent;

//use TusPhp\File;

class TusUploadComplete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tusEvent;
    public function __construct(TusEvent $tusEvent)
    {
        $this->tusEvent = $tusEvent;
        $file = $this->tusEvent->getFile();
        
        (new ConsoleOutput())->writeln("upload complete ========");
       
        $details = $file->details();
        (new ConsoleOutput())->writeln("tusEvent->file->details() info:");
        $this->dumpToConsole($details);

        // make model in db:
        $path = new FilePath($file->getFilePath());
        $model = $path->addToDB();
        (new ConsoleOutput())->writeln("added to db, id: {$model->id}");

    }
    protected function dumpToConsole(array $arr, int $depth = 1){
        foreach($arr as $key => $val){

            $out = str_repeat(' ', 4 * $depth)."[{$key}] => ";
            if(!is_array($val)) $out .= $val;
            (new ConsoleOutput())->writeln($out);
            
            if(is_array($val)){
                $this->dumpToConsole($val, $depth + 1);
            }
        }
    }
}