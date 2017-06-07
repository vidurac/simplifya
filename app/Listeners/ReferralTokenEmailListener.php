<?php

namespace App\Listeners;

use App\Events\SendReferralToken;
use App\Lib\sendMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;


class ReferralTokenEmailListener
{

    /**
     * Create the event handler.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     * @param SendReferralToken $event
     */
    public function handle(SendReferralToken $event)
    {

        $data = [
            'link' => $event->details->link,
            'company' => $event->details->company,
            'from' => $event->details->from,
            'system' => $event->details->system,
            'code' => $event->details->code,
            'amount' => $event->details->amount,
        ];

        //send email
        $mail = new sendMail;
        $mail->mailSender('emails.referral_code',
            array(
                $event->details->email
            ),
            $event->details->name,
            'Your Simplifya Referral Code',
            $data
        );

    }
}
