<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CompanyRepository;
use App\Repositories\ConfirmationCodeRepository;
use App\Lib\sendMail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use App\Repositories\UsersRepository;

class InviteMailer extends Command
{
    private $company;
    private $user;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invitation for employee';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CompanyRepository $company, UsersRepository $user, ConfirmationCodeRepository $confirmation)
    {
        parent::__construct();
        $this->company = $company;
        $this->user = $user;
        $this->confirmation = $confirmation;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inviters = $this->company->getAllActiveCompanyUsers();
        $layout ='emails.emp_invitation';
        
        if(count($inviters)>0) {
            foreach($inviters as $user) {
                $user_id = $user->user_id;
                $company_details = $this->company->find($user->company_id);
                $createdBy = $this->user->find($user->created_user);
                $subject ='You’ve been invited to join Simplifya by '.$createdBy->name;
                $confirmation_code =  sha1(uniqid().$user_id); //Hash::make(time());
                $data = array('user_id' => $user_id, 'confirmation_code' => $confirmation_code, 'is_confirm' => '0');
                $response = $this->confirmation->create($data);
                $data = array();
                $base_url = '/register/'.$confirmation_code;
                $url = URL::to($base_url);
                $email_data = array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),'system' => 'Simplifya','url' => $url, 'company' => 'Simplifya', 'sent_name' => $createdBy->created_user, 'company_name' => $company_details->name);
                $this->sendWelcomeMail($user->email, $user->user_name,$layout,$subject,$email_data);
                $email_data = array();
                $this->user->userUpdate($user_id, array('is_send_mail' => '1'));
            }
        }
    }

    public function sendWelcomeMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
}
