<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AdminMailRequest extends Event
{
    use SerializesModels;
    public $adminMailHelper;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($adminMailHelper)
    {
        $this->adminMailHelper = $adminMailHelper;
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
