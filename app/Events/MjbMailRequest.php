<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MjbMailRequest extends Event
{
    use SerializesModels;
    public $MjbMailHelper;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($MjbMailHelper)
    {
        $this->MjbMailHelper = $MjbMailHelper;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
