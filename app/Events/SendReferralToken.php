<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendReferralToken extends Event
{
    use SerializesModels;
    public $details;

    /**
     * Create a new event instance.
     *
     * @param $details
     * @internal param $mjbDetails
     */
    public function __construct($details)
    {
        $this->details = $details;
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
