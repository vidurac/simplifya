<?php

namespace App\Listeners;

use App\Events\MjbInHouseAuditSupport;
use App\Events\MjbSignUpSupport;
use App\Lib\sendMail;
use App\Repositories\EmailNotificationLogRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;


class MjbInHouseAuditSupportListener
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
    public function handle(MjbInHouseAuditSupport $event)
    {

        $data = [
            'system' => 'Simplifya',
            'from' => 'noreply@simplifya.com',
            'name'              => $event->mjbDetails->name,
            'email'             => $event->mjbDetails->email,
            'businessName'      => $event->mjbDetails->businessName,
            'company'           => $event->mjbDetails->companyname,
            'reg_no'            => $event->mjbDetails->reg_no,
            'to_company'        => $event->mjbDetails->to_company,
            'location_name'     => $event->mjbDetails->location_name,
            'address_line_1'    => $event->mjbDetails->address_line_1,
            'address_line_2'    => $event->mjbDetails->address_line_2,
            'city'              => $event->mjbDetails->city,
            'state'             => $event->mjbDetails->state,
            'country'           => $event->mjbDetails->country,
            'zip_code'          => $event->mjbDetails->zip_code,
            'comment'                   => $event->mjbDetails->comment,
            'date_time'                 => $event->mjbDetails->date_time,
            'inspection_Date'           => $event->mjbDetails->inspection_Date,
            'inspection_Time'           => $event->mjbDetails->inspection_Time,
            'contact'                   => $event->mjbDetails->contact,
            'inspector_name'                   => $event->mjbDetails->inspector_name,
            'inspector_email'                   => $event->mjbDetails->inspector_email,
        ];

        $simplify_email     = Config::get('simplifya.SIMPLIFIYA_SUPPORT_EMAIL');
        $name_of_business = 'Simplifya Support';
        $send_mail = new sendMail();

        $layout = $event->mjbDetails->layout;

        $subject = $event->mjbDetails->subject;
        $send_mail->mailSender($layout, $simplify_email, $name_of_business, $subject, $data);

    }
}
