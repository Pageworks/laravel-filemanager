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

class FileRenamed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $path_from;
    public $path_to;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FilePath $path_from, FilePath $path_to)
    {
        $this->path_from = $path_from;
        $this->path_to = $path_to;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
