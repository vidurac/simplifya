<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\AppointmentRepository;
use App\Repositories\EmailNotificationLogRepository;
use Carbon\Carbon;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;

class AuditMailer extends Command
{

    private $appointment;
    private $emailNotification;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit Reminder';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AppointmentRepository $appointment, EmailNotificationLogRepository $emailNotification)
    {
        parent::__construct();
        $this->appointment = $appointment;
        $this->emailNotification = $emailNotification;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $data = [];
        $layout ='emails.audit_reminder';
        $current_time = Carbon::now()->toDateTimeString();
        $after2day_time = Carbon::now()->addHours(48)->format('Y-m-d H:i:00');

        $subject = 'Reminder: Audit scheduled for '.Carbon::now()->addHours(48)->format('m-d-Y');
        $appointments = $this->appointment->getAllAppointmentByStatus($current_time, $after2day_time);
        
        if(count($appointments) > 0) {
            foreach ($appointments as $appointment) {
                if($appointment->company_id == null) {
                    $auditDate   = date('m/d/Y', strtotime(str_replace('/', '-', $appointment->inspection_date_time)));
                    $auditTime   = date('g:i a', strtotime(str_replace('/', '-', $appointment->inspection_date_time)));

                    $email_data = array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                        'system' => 'Simplifiya',
                        'company' => 'Simplifya',
                        'to_company' =>  $appointment->to_company_name,
                        'location_name' =>  $appointment->location_name,
                        'address_line_1' =>  $appointment->address_line_1,
                        'address_line_2' =>  $appointment->address_line_2,
                        'city' =>  $appointment->city,
                        'state' =>  $appointment->state,
                        'zip_code' =>  $appointment->zip_code,
                        'inspection_Date' =>  $auditDate,
                        'inspection_Time' =>  $auditTime
                    );
                    $this->sendWelcomeMail($appointment->email, $appointment->user_name,$layout,$subject,$email_data);
                    $data[] = array('company_id' => $appointment->id, 'notification_type' => 3, 'created_at' => $current_time, 'updated_at' => $current_time);
                }
            }
            if(isset($data[0])) {
                $this->emailNotification->insertMultipleEmailLog($data);
            }
        }

    }

    public function sendWelcomeMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
}
