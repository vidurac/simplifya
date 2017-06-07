<?php

namespace App\Listeners;

use App\Events\CcGeMailRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;

class CcGeMailSender
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
     * @param  CcGeMailRequest  $event
     * @return void
     */
    public function handle(CcGeMailRequest $event)
    {
        $data = ['from' => 'noreply@simplifya.com',
                'system' => 'Simplifya',
                'name'  => $event->CcGeMailHelper->name,
                'email'    => $event->CcGeMailHelper->email,
                'company'        => $event->CcGeMailHelper->companyname
                ];
        $email = $event->CcGeMailHelper->email;
        $company = $event->CcGeMailHelper->companyname;
        $send_mail = new sendMail();

        $layout = 'emails.ccge_approval';
        $subject = 'Welcome to Simplifya!';
        $send_mail->mailSender($layout, $email, $company, $subject, $data);
    }
}
