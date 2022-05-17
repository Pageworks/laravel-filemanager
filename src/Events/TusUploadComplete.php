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
        
        // filename: file.mp4
        (new ConsoleOutput())->writeln("name: {$file->getName()}");

        // tus access: http://localhost:8000/tus/{key}
        (new ConsoleOutput())->writeln("location: {$file->getLocation()}");

        // absolute path: /Users/nick/Projects/uploader/storage/app/public/file.mp4
        (new ConsoleOutput())->writeln("filepath: {$file->getFilePath()}");
        
        $path = new FilePath($file->getFilePath());

        $model = $path->addToDB();

        (new ConsoleOutput())->writeln("added to db, id: {$model->id}");
    }
}