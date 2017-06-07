<?php

namespace App\Listeners;

use App\Events\AdminMailRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;

class AdminMailSender
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
     * @param  AdminMailRequest  $event
     * @return void
     */
    public function handle(AdminMailRequest $event)
    {
        $data = ['from' => 'noreply@simplifya.com',
                'system' => 'Simplifya',
                'company_name'  => $event->adminMailHelper->company_name,
                'entity_name'    => $event->adminMailHelper->entity_name,
                'company'        => $event->adminMailHelper->companyname,
                'registrant'        => $event->adminMailHelper->registrant,
                'registrantEmail'        => isset($event->adminMailHelper->registrantEmail)? $event->adminMailHelper->registrantEmail : null,
                ];
        $simplify_email = $event->adminMailHelper->simplify_email;
        $name_of_business = $event->adminMailHelper->company_name;
        $send_mail = new sendMail();

        $layout = $event->adminMailHelper->layout;
        //$subject = 'CC/GE Sign Up';

        $subject = $event->adminMailHelper->subject;
        $send_mail->mailSender($layout, $simplify_email, $name_of_business, $subject, $data);
    }
}
