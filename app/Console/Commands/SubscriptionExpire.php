<?php

namespace App\Console\Commands;
use App\Repositories\CompanyRepository;
use App\Repositories\MasterSubscriptionRepository;
use App\Repositories\CompanyLocationLicenseRepository;
use App\Repositories\CompanySubscriptionRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Console\Command;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Config;
use App\Lib\sendMail;
use Carbon\Carbon;
use Cartalyst\Stripe\Exception\NotFoundException;
use Cartalyst\Stripe\Exception\CardErrorException;

class SubscriptionExpire extends Command
{
    private $company;
    private $companyLicense;
    private $masterSubscription;
    private $master_data;
    private $payment;
    private $stripe;
    private $company_subscription;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find subscription expire company';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CompanyRepository $company, MasterSubscriptionRepository $masterSubscription, MasterUserRepository $master_data, PaymentRepository $payment, CompanyLocationLicenseRepository $companyLicense, CompanySubscriptionRepository $company_subscription)
    {
        parent::__construct();
        $this->company = $company;
        $this->company_subscription = $company_subscription;
        $this->companyLicense = $companyLicense;
        $this->masterSubscription = $masterSubscription;
        $this->master_data      = $master_data;
        $this->payment = $payment;
        $this->stripe = Stripe::make(Config::get('simplifya.STRIPE_KEY'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subFee = '';
        $company_data = array();
        $current_date = date('d');
        $simplify_email     = Config::get('simplifya.admin_email');

        //last day of current month
        $last_day_this_month  = date('t');

        if($current_date == 04) {
            $master_data = $this->master_data->all(array('*'));

            foreach($master_data as $data) {
                if($data->name == 'SUBSFEE') {
                    $subFee = $data->value;
                }
            }
            $companies = $this->company->getAllActivateCompanies();
            $subscriptions = $this->masterSubscription->getMonthlySubscriptionFee();
            foreach($companies as $company) {
                foreach($subscriptions as $subscription) {
                    if($company->entity_type == $subscription->entity_type_id) {
                        $company_data[] = array('id' => $company->id, 'name' => $company->name, 'stripe_id' => $company->stripe_id, 'entity_type' => $company->entity_type, 'amount' => $subscription->amount);
                    }
                }
            }
            $this->subscriptionHandler($company_data, $subFee);
        }
    }

    private  function subscriptionHandler($company_data, $subFee)
    {
        $users = '';
        $currency = 'USD';
        foreach ($company_data as $data) {
            if($data['entity_type'] == 2) {
                $users = $this->company->getAdminUser($data['id'], 2);
                $n_of_license = $this->companyLicense->getActiveLicenseCountByCompanyId($data['id']);
                if($n_of_license >0) {
                    $entity_name = 'Marijuana Business';
                    $subject = 'MJB registration - '. $data['name'];
                    $this->stripePayments($users, $data['name'], $data['stripe_id'], $currency, $data['amount']*$n_of_license, $data['id'], $entity_name, $data['entity_type'], $subject);
                }
             } else {
                if ($data['entity_type'] == 3) {
                    $users = $this->company->getAdminUser($data['id'], 5);
                    $entity_name = 'Compliance Company';
                    $subject = 'Action required – CC registration';
                } else {
                    $users = $this->company->getAdminUser($data['id'], 7);
                    $entity_name = 'Government Entity';
                    $subject = 'Action required – GE registration';
                }
                if($subFee == 1) {
                    $this->stripePayments($users, $data['name'], $data['stripe_id'], $currency, $data['amount'], $data['id'], $entity_name, $data['entity_type'], $subject);
                }
            }
        }
    }

    private function stripePayments($users, $name_of_business, $stripe_id, $currency, $amount, $company_id, $entity_name, $entity_type, $subject)
    {
        $customer_charge = $this->customerCharges($stripe_id, $currency, $amount); 
        if ($customer_charge['success']) {
            $tx_type = 'subscription';
            $this->paymentHandler($customer_charge, $amount, $currency, $company_id, $tx_type);
            $this->company->updateCompanyByStatusAndId(2, $company_id);
        } else {
            $simplifya_name = Config::get('messages.COMPANY_NAME');
            $simplify_email     = Config::get('simplifya.admin_email');
            $layout = 'emails.account_expire';
            $this->company->updateCompanyByStatusAndId(0, $company_id);
            $subject = 'Payment Method Declined';
            foreach($users as $user) {
                $email_data = array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                    'system' => 'Simplifya',
                    'company' => 'Simplifya'
                );
                $this->sendWelcomeMail($user->email, $user->name,$layout,$subject,$email_data);
                $email_data = array();
            }
        }
    }

    private function sendWelcomeMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }

    private function customerCharges($customer, $currency, $amount)
    {
        try {
            $charge = $this->stripe->charges()->create([
                'customer' => $customer,
                'currency' => $currency,
                'amount'   => $amount,
            ]);

            return array('success' => true, 'charge' => $charge);
        } catch(NotFoundException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
        } catch (CardErrorException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
        }

    }

    public function paymentHandler($customer_charge, $subscription_fee, $currency, $company_id, $tx_type)
    {
        $master_subscription = $this->company->getSubscriptionType($company_id);
        $payments = array(
            'req_date_time' => Carbon::now(),
            'object'        => $customer_charge['charge']['object'],
            'req_currency'  => $currency,
            'req_amount'    => $subscription_fee,
            'res_date_time' => $customer_charge['charge']['created'],
            'res_id'        => $customer_charge['charge']['id'],
            'res_currency'  => strtoupper($customer_charge['charge']['currency']),
            'res_amount'    => $customer_charge['charge']['amount']/100,
            'company_id'    => $company_id,
            'tx_type'       => $tx_type,
            'created_by'    => '0'
        );
        $response_payment = $this->payment->create($payments);

        if($response_payment) {
            $company_subscription = array(
                'company_id'            => $company_id,
                'master_subscription_id'=>$master_subscription[0]->id,
                'payment_id'            =>$response_payment->id,
                'created_by'            => 0,
                'amount'                => $subscription_fee
            );
            $response_subscription = $this->company_subscription->create($company_subscription);
            if($response_subscription) {
                $message = Config::get('messages.PAYMENT_SUCCESSFUL');
                return response()->json(array('success' => 'true', 'message'=> $message));
            }
        }
    }
}
