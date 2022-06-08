<?php

namespace Pageworks\LaravelFileManager\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Pageworks\LaravelFileManager\FilePath;

class FileUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $path;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FilePath $path)
    {
        $this->path = $path;
    }
}
