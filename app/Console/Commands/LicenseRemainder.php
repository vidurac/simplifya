<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\LicenseRemindersRepositories;
use App\Repositories\MasterLicenseRepository;
use App\Repositories\UsersRepository;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;

class LicenseRemainder extends Command
{
    private $reminder;
    private $license;
    private $user;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'License Renewal Date Remainder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LicenseRemindersRepositories $reminder, MasterLicenseRepository $license, UsersRepository $user)
    {
        parent::__construct();
        $this->reminder = $reminder;
        $this->license = $license;
        $this->user = $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->reminder->getLicenseRenewalDate();
        $data_set = array();
        $today = date("Y-m-d");
        $layout ='emails.license_reminder';
        $subject ='You have a license that will expire soon';

        if(count($response)>0) {
            foreach($response as $reminder) {
                $data_set[$reminder->license_location_id]['license_loc_id'] = $reminder->license_location_id;
                $data_set[$reminder->license_location_id]['license_number'] = $reminder->license_number;
                $data_set[$reminder->license_location_id]['renewal_date'] = $reminder->renewal_date;
                $data_set[$reminder->license_location_id]['license_date'] = $reminder->license_date;// Expiry date
                $data_set[$reminder->license_location_id]['company_id'] = $reminder->company_id;
                $data_set[$reminder->license_location_id]['license_id'] = $reminder->license_id;
                $data_set[$reminder->license_location_id]['reminders'][$reminder->reminder_id]['reminder'] = ($reminder->reminder + 1);
            }
        }

        foreach($data_set as $data) { 
            $datetime1 = date_create($today);
            $datetime2 = date_create($data['renewal_date']);
            $interval = date_diff($datetime1, $datetime2);
            $days = $interval->format('%R%a');
            if($days>0) {
                foreach($data['reminders'] as $day) {
                    if($days == $day['reminder']) {
                        $users = $this->user->findWhere(array('master_user_group_id' => 2, 'company_id' => $data['company_id']));
                        $license = $this->license->find($data['license_id']);
                        $date = new \DateTime($data['renewal_date']);
                        $expiryDate = new \DateTime($data['license_date']);
                        foreach($users as $user) {
                            $email_data = array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                'system' => 'Simplifya',
                                'company' => 'Simplifya',
                                'license_type' => $license->name,
                                'no_of_day' => ($day['reminder'] - 1),
                                'license_number' => $data['license_number'],
                                'expiry_date' => $date->format('m-d-Y'),
                                'license_date' => $expiryDate->format('m-d-Y')
                            );
                            $this->sendWelcomeMail($user->email, $user->name,$layout,$subject,$email_data);
                            $email_data = array();
                        }
                    }
                }
            }
        }

    }

    public function sendWelcomeMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
}
