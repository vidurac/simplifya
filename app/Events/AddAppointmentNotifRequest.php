<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AddAppointmentNotifRequest extends Event
{
    use SerializesModels;
    public $userNotifHelper;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userNotifHelper)
    {
        $this->userNotifHelper = $userNotifHelper;
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
