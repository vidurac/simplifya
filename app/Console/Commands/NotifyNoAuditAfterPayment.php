<?php

namespace App\Console\Commands;

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


class NotifyNoAuditAfterPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify_no_audit_after_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify when no audit after payment done';


    private $company;
    private $email;
    private $user;

    /**
     * Create a new command instance.
     *
     * @param CompanyRepository $companyRepository
     * @param EmailNotificationLogRepository $emailNotificationLogRepository
     * @param UsersRepository $usersRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        EmailNotificationLogRepository $emailNotificationLogRepository,
        UsersRepository $usersRepository)
    {
        parent::__construct();
        $this->email    = $emailNotificationLogRepository;
        $this->company  = $companyRepository;
        $this->user     = $usersRepository;
    }
    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("#### Notify to admin about pending payments");
        //$this->info("Notify to admin about pending payments");
        $companies = $this->company->companiesWithNoAudit(
            Config::get('simplifya.ACTIVE'),
            Config::get('simplifya.MjbMasterAdmin'));

        $simplifya_name = Config::get('messages.COMPANY_NAME');
        $simplify_email     = Config::get('simplifya.SIMPLIFIYA_SUPPORT_EMAIL');
        $time_interval     = Config::get('simplifya.MJB_PAYMENT_MADE_NO_AUDIT_ADDED_INTERVAL');
//        $max_time_interval     = Config::get('simplifya.SUPPORT_EMAILS_CRON_JOB_MAX_INTERVAL');
        $max_time_interval     = 200;

        foreach ($companies as $company) {
            // check mjb sign up account is older than 48 hours and not more than 5 days
            if (($max_time_interval >= $this->hoursDiff($company->updated_at)) && ($this->hoursDiff($company->updated_at)>= $time_interval)) {
                $this->info($company->id);
                $isEmailSent = $this->email->findEmailLogWithType($company->id, Config::get('simplifya.MJB_PAYMENT_MADE_NO_AUDIT_ADDED'));

                if (!isset($isEmailSent)){

                    $user = $this->user->findBy('company_id', $company->id);

                    $createdDate   = date('m/d/Y', strtotime(str_replace('/', '-', $company->created_at)));
                    $createdTime   = date('g:i a', strtotime(str_replace('/', '-', $company->created_at)));

                    $data = [
                        'system' => 'Simplifya',
                        'from' => 'noreply@simplifya.com',
                        'name'              => $user->name,
                        'email'             => $user->email,
                        'businessName'      => $company->name,
                        'company'           => $simplifya_name,
                        'reg_no'            => $company->reg_no,
                        'createdDate'       => $createdDate,
                        'createdTime'       => $createdTime
                    ];

                    $this->sendMail(
                        $simplify_email,
                        'Simplifya Support',
                        'emails.mjb_no_audit_made_after_payment_support',
                        'NO SELF-AUDIT CREATED YET - ' . $company->name,
                        $data
                    );


                    // update email log after email sent
                    $this->email->create(
                        array (
                            'company_id' => $company->id,
                            'notification_type' => Config::get('simplifya.MJB_PAYMENT_MADE_NO_AUDIT_ADDED')
                        )
                    );
                }
            }

//            $this->info($this->hoursDiff($company->updated_at));
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
