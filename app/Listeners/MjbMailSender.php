<?php

namespace App\Listeners;

use App\Events\MjbMailRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;

class MjbMailSender
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MjbMailRequest  $event
     * @return void
     */
    public function handle(MjbMailRequest $event)
    {
        $data = [
            'from' => 'noreply@simplifya.com',
            'system' => 'Simplifya',
            'name'  => $event->MjbMailHelper->name,
            'email'    => $event->MjbMailHelper->email,
            'company'        => $event->MjbMailHelper->companyname
        ];
        $email = $event->MjbMailHelper->email;
        $company = $event->MjbMailHelper->companyname;
        $send_mail = new sendMail();

        $layout = 'emails.mjb_welcome';
        $subject = 'Welcome to Simplifya!';
        $send_mail->mailSender($layout, $email, $company, $subject, $data);
    }
}
