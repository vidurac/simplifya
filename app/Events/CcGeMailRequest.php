<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;

class CcGeMailRequest extends Event
{
    use SerializesModels;
    public $CcGeMailHelper;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($CcGeMailHelper)
    {
        $this->CcGeMailHelper = $CcGeMailHelper;
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
