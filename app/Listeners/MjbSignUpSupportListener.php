<?php

namespace App\Listeners;

use App\Events\MjbSignUpSupport;
use App\Lib\sendMail;
use App\Repositories\EmailNotificationLogRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;


class MjbSignUpSupportListener
{
    private $emailNotificationLogRepository;

    /**
     * Create the event handler.
     *
     * @param EmailNotificationLogRepository $emailNotificationLogRepository
     */
    public function __construct(EmailNotificationLogRepository $emailNotificationLogRepository)
    {
        $this->emailNotificationLogRepository = $emailNotificationLogRepository;
    }

    /**
     * Handle the event.
     *
     * @param  MjbSignUpSupport  $event
     * @return void
     */
    public function handle(MjbSignUpSupport $event)
    {

        $data = [
            'system' => 'Simplifya',
            'from' => 'noreply@simplifya.com',
            'name'              => $event->mjbDetails->name,
            'email'             => $event->mjbDetails->email,
            'businessName'      => $event->mjbDetails->businessName,
            'company'           => $event->mjbDetails->companyname,
            'reg_no'            => $event->mjbDetails->reg_no
        ];
        $simplify_email     = Config::get('simplifya.SIMPLIFIYA_SUPPORT_EMAIL');
        $name_of_business = 'Simplifya Support';
        $send_mail = new sendMail();

        $layout = $event->mjbDetails->layout;

        $subject = $event->mjbDetails->subject;
        $send_mail->mailSender($layout, $simplify_email, $name_of_business, $subject, $data);

    }
}
