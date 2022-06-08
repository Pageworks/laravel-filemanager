<?php

namespace Pageworks\LaravelFileManager\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TusPhp\Events\TusEvent;

class TusUploadProgress
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tusEvent;
    public function __construct(TusEvent $tusEvent)
    {
        $this->tusEvent = $tusEvent;
    }
}