<?php

namespace Pageworks\LaravelFileManager\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Output\ConsoleOutput;
use TusPhp\Events\TusEvent;

class TusUploadMerged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tusEvent;
    public function __construct(TusEvent $tusEvent)
    {
        $this->tusEvent = $tusEvent;
        (new ConsoleOutput())->writeln("upload merged");
    }
}