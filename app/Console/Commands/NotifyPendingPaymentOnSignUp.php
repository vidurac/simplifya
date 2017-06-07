<?php

namespace App\Console\Commands;

use App\Repositories\CompanyLocationRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\EmailNotificationLogRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\UsersRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Config;
use App\Lib\sendMail;


class NotifyPendingPaymentOnSignUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify_admin_pending_sign_up_payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify to admin about mjb pending payments on sign up';


    private $company;
    private $email;
    private $user;
    private $companyLocation;

    /**
     * Create a new command instance.
     *
     * @param CompanyRepository $companyRepository
     * @param EmailNotificationLogRepository $emailNotificationLogRepository
     * @param UsersRepository $usersRepository
     * @param CompanyLocationRepository $companyLocationRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        EmailNotificationLogRepository $emailNotificationLogRepository,
        UsersRepository $usersRepository,
        CompanyLocationRepository $companyLocationRepository)
    {
        parent::__construct();
        $this->email    = $emailNotificationLogRepository;
        $this->company  = $companyRepository;
        $this->user     = $usersRepository;
        $this->companyLocation = $companyLocationRepository;
    }
    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        //$this->info("Notify to admin about pending payments");
        $companies = $this->company->findWhere(
            array(
                'entity_type' => Config::get('simplifya.MjbMasterAdmin'),
                'status' => Config::get('simplifya.INPROGRESS')
            )
        );

        $simplifya_name = Config::get('messages.COMPANY_NAME');
        $simplify_email     = Config::get('simplifya.SIMPLIFIYA_SUPPORT_EMAIL');
        $time_interval     = Config::get('simplifya.MJB_SIGNED_UP_NO_PAYMENT_INTERVAL');
        $max_time_interval     = Config::get('simplifya.SUPPORT_EMAILS_CRON_JOB_MAX_INTERVAL');

        foreach ($companies as $company) {
            // check mjb sign up account is older than 48 hours and not more than 5 days
            if (($max_time_interval >= $this->hoursDiff($company->created_at)) && ($this->hoursDiff($company->created_at)>= $time_interval)) {
                $isEmailSent = $this->email->findEmailLogWithType($company->id, Config::get('simplifya.MJB_SIGNED_UP_NO_PAYMENT'));
                if (!isset($isEmailSent)){
//                    $this->info($company->id . ' hours diff ' . $this->hoursDiff($company->created_at));
                    $user = $this->user->findBy('company_id', $company->id);

                    $createdDate   = date('m/d/Y', strtotime(str_replace('/', '-', $company->created_at)));
                    $createdTime   = date('g:i a', strtotime(str_replace('/', '-', $company->created_at)));

                    $companyLocations = $this->companyLocation->getLocationByCompanyId($company->id);
                    $contactNumber = '';
                    if ($companyLocations->count()) {
                        $location = $companyLocations->first();
                        $contactNumber = $location->phone_number;
                    }

                    $data = [
                        'system' => 'Simplifya',
                        'from' => 'noreply@simplifya.com',
                        'name'              => $user->name,
                        'email'             => $user->email,
                        'businessName'      => $company->name,
                        'contact'           => $contactNumber,
                        'company'           => $simplifya_name,
                        'reg_no'            => $company->reg_no,
                        'createdDate'       => $createdDate,
                        'createdTime'       => $createdTime
                    ];

                    $this->sendMail(
                        $simplify_email,
                        'Simplifya Support',
                        'emails.mjb_pending_payment_singup_support',
                        'NO PAYMENT AFTER 48 HOURS - ' . $company->name,
                        $data
                    );

                    // update email log after email sent
                    $this->email->create(
                        array (
                            'company_id' => $company->id,
                            'notification_type' => Config::get('simplifya.MJB_SIGNED_UP_NO_PAYMENT')
                        )
                    );
                }
            }
        }

    }

    /**
     * Returns hours difference in between two dates
     * @param $dateString
     * @return int
     */
    protected function hoursDiff($dateString) {
        $date1 = new DateTime($dateString);
        $date2 = new DateTime();

        //determine what interval should be used - can change to weeks, months, etc
        $interval = new DateInterval('PT1H');

        //create periods every hour between the two dates
        $periods = new DatePeriod($date1, $interval, $date2);

        //count the number of objects within the periods
        $hours = iterator_count($periods);
        return $hours;
    }

    protected function sendMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
}
