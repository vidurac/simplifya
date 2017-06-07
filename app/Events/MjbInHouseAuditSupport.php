<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MjbInHouseAuditSupport extends Event
{
    use SerializesModels;
    public $mjbDetails;

    /**
     * Create a new event instance.
     *
     * @param $mjbDetails
     */
    public function __construct($mjbDetails)
    {
        $this->mjbDetails = $mjbDetails;
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
