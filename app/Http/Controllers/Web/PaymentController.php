<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 */
namespace App\Http\Controllers\Web;

use App\Models\MasterEntityType;
use App\Repositories\CouponsRepository;
use App\Repositories\CompanySubscriptionPlanRepository;
use App\Repositories\MasterEntityTypeRepository;
use App\Repositories\MasterSubscriptionRepository;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Exception\ServerErrorException;
use Illuminate\Http\Request;

use App\Http\Requests;
use Cartalyst\Stripe\Exception\NotFoundException;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanySubscriptionRepository;
use App\Repositories\PaymentRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Cartalyst\Stripe\Exception\InvalidRequestException;
use Illuminate\Support\Facades\Session;
use App\Lib\sendMail;
use App\Lib\ProrateDayCalculation;
use Illuminate\Support\Facades\Redirect;
use Mockery\CountValidator\Exception;


class PaymentController extends Controller
{
    private $stripe;
    private $company;
    private $company_subscription;
    private $payment;private $masterEntity;
    private $subscription;
    private $company_subscription_plan;
    private $coupon;

    public function __construct(CompanyRepository $company, CompanySubscriptionRepository $company_subscription, PaymentRepository $payment, MasterEntityTypeRepository $masterEntity, MasterSubscriptionRepository $subscription, CompanySubscriptionPlanRepository $company_subscription_plan,CouponsRepository $coupon)
    {
        $this->company      = $company;
        $this->company_subscription = $company_subscription;
        $this->payment = $payment;
        $this->stripe = Stripe::make(Config::get('simplifya.STRIPE_KEY'));
        $this->masterEntity = $masterEntity;
        $this->subscription = $subscription;
        $this->company_subscription_plan = $company_subscription_plan;
        $this->coupon = $coupon;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $group_id = Auth::user()->master_user_group_id;

        // if supper admin
        if($group_id == Config::get('simplifya.MasterAdmin')){
            $companies = $this->company->findAllActiveCompanies(Auth::user()->company_id);
            $entities = $this->masterEntity->findWhere(array('status' => 1));
            return view('payment.index')->with(array('groupId' => $group_id, 'companies' => $companies, 'entities' => $entities, 'page_title' => "Payments Manager"));
        }
        else if($group_id == Config::get('simplifya.MjbMasterAdmin') || $group_id == Config::get('simplifya.CcMasterAdmin') || $group_id == Config::get('simplifya.GeMasterAdmin')){
            return view('payment.index')->with(array('groupId' => $group_id, 'page_title' => "Payments Manager"));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * All payments for company.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentHandler()
    {
        //declare and initialize variables
        $subscription_fee = Input::get('subscription_fee');
        $coupon = Input::get('coupon_id');
        $company_id = Input::get('company_id');
        $tx_type = Input::get('payment_type');
        $currency = 'USD';
        $subscribe_plan = Input::get('subscription_plan');
        \Log::debug("subscription plan id " . $coupon);

        $company_details = $this->company->find($company_id);

        $total_amount=$this->getSubscriptionPlanFee($company_id,$subscribe_plan);

        if($coupon && $coupon!=''){
            $requests = array('coupon_id'=>$coupon);
            $coupon_check= \Validator::make($requests,[
                'coupon_id' =>'unique:company_subscription_plans'
            ]);
            $coupon_status = $coupon_check->passes();
            if($coupon_status==true){
                $coupon_id=$coupon;
                $coupon_discount=$this->coupon->getDiscountAmount($subscribe_plan,$total_amount['subscription_fee'],$coupon_id);
                $coupon_amount=$coupon_discount['discount'];
            }else{
                $coupon='Coupon is already in use.';
                return response()->json( array('success' => 'false', 'message' => $coupon));
            }
        }else{
            $coupon_id=0;
            $coupon_amount=0;

        }


        if ($company_details->foc == 1 || $subscription_fee==0) {
            \Log::debug("registering user with foc");
            $master_subscription = $this->company->getSubscriptionType($company_id);
            $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($subscribe_plan, $company_id,false,false,$coupon_id);
            // save company subscription plan

            \DB::beginTransaction();
            try {
                $subscribe_planObj = $this->company_subscription_plan->create($plansParam);
                if (isset($subscribe_plan)) {
                    $company_subscription = array(
                        'company_id' => $company_id,
                        'master_subscription_id' => $master_subscription[0]->id,
                        'payment_id' => 0,
                        'created_by' => Auth::user()->id,
                        'amount' => $subscription_fee,
                        'company_subscription_plan_id' => $subscribe_planObj->id,
                        'discount'=>$coupon_amount
                    );
                }else {
                    $company_subscription = array(
                        'company_id' => $company_id,
                        'master_subscription_id' => $master_subscription[0]->id,
                        'payment_id' => 0,
                        'created_by' => Auth::user()->id,
                        'amount' => $subscription_fee,
                        'discount'=>$coupon_amount
                    );
                }
                $response_subscription = $this->company_subscription->create($company_subscription);
                $company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
                \DB::commit();
                Session::put('company_status', 2);
                return response()->json(array('success' => 'true', 'message'=> Config::get('messages.PAYMENT_SUCCESSFUL'), 'payment' => [], 'paymentId' => 0));
            }catch (\Exception $e) {
                \DB::rollback();
                return response()->json(array('success' => 'false', 'message'=> $e->getMessage()));
            }

        }else {
            if (isset($subscribe_plan) && !empty($subscribe_plan)) {
                $response = $this->paymentCommenHandler($subscription_fee, $company_id, $tx_type, $currency, $subscribe_plan,$coupon_id,$coupon_amount);
            }else {
                $response = $this->paymentCommenHandler($subscription_fee, $company_id, $tx_type, $currency,false,$coupon_id,$coupon_amount);
            }
            if($response['success'] == "true"){
                Session::put('company_status', 2);
                $company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
                return response()->json(array('success' => 'true', 'message'=> $response['message'], 'payment' => $response['payment'], 'paymentId' => $response['paymentId']));
            }
            else{
                return response()->json(array('success' => 'false', 'message'=> Config::get('messages.PAYMENT_UNSUCCESSFUL')));
            }
        }
    }

    public function getSubscriptionPlanFee($company_id,$package_id){
        $response = $this->company->calculateSubscriptionForPlanFee($company_id,$package_id);

        if (isset($response[0])) {
            $name = $response[0]->company_name;
            $entity_type = $response[0]->master_entity_name;

            $date = date("Y/m/d");
            $prorate_day_cal = new ProrateDayCalculation();
            $day_calculation = $prorate_day_cal->dayCalculation($date);
            $amount = $response[0]->amount;
            // Following statement is the way you calculate pro-rate amount
            //$subscription_fee_per_license = ($amount / $day_calculation['days_in_month']) * $day_calculation['days_remaining'];
            // Without pro-rate option
            $subscription_fee_per_license = $amount;
            $license_count = count($response);
            $subscription_fee = $license_count * $subscription_fee_per_license;
            $subscription_fee = round($subscription_fee, 2);
            $subscription_fee = number_format((float)$subscription_fee, 2, '.', '');
            //$company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
            return $data = array('entity_type' => $entity_type, 'name' => $name, 'no_license' => $license_count, 'subscription_fee' => $subscription_fee, 'monthly_fee' => $amount);

        }
    }
    public function paymentCommenHandler($subscription_fee, $company_id, $tx_type, $currency, $subscribe_plan=false,$coupon_id=false,$discount=false){
        $company_details = $this->company->getCompanyDetails($company_id);
        $response = $this->customerCharges($company_details[0]['stripe_id'], $currency, $subscription_fee);

        if($response['success']) {
            if(($company_details[0]['status'] == 4) || ($company_details[0]['status'] == 0)) {
                $layout = 'emails.account_activate';
                $subject = 'Simplifya account activated';
                $data = array('from' => 'noreply@simplifya.com', 'system' => 'Simplifya', 'company' => 'Simplifya');
                try {
                    $this->sendMails(Auth::User()->email, Auth::User()->name, $layout, $subject, $data);
                }catch (Exception $e) {}

                $this->company->updateCompanyByStatusAndId(2, $company_id);

                $master_subscription = $this->company->getSubscriptionType($company_id);
                $payments = array(
                    'req_date_time' => Carbon::now(),
                    'object' => $response['charge']['object'],
                    'req_currency' => $currency,
                    'req_amount' => $subscription_fee,
                    'res_date_time' => date("Y-m-d H:i:s", $response['charge']['created']),
                    'res_id' => $response['charge']['id'],
                    'res_currency' => strtoupper($response['charge']['currency']),
                    'res_amount' => $response['charge']['amount'] / 100,
                    'company_id' => $company_id,
                    'tx_type' => $tx_type,
                    'tx_status' => 1,
                    'charge_id' => $response['charge']['id'],
                    'balance_transaction' => $response['charge']['balance_transaction'],
                    'created_by' => Auth::user()->id
                );
                $response_payment = $this->payment->create($payments);

                if ($response_payment) {
                    $company_subscription = array(
                        'company_id' => $company_id,
                        'master_subscription_id' => $master_subscription[0]->id,
                        'payment_id' => $response_payment->id,
                        'created_by' => Auth::user()->id,
                        'amount' => $subscription_fee,
                        'discount'=>$discount
                    );
                    $response_subscription = $this->company_subscription->create($company_subscription);
                    if ($response_subscription) {
                        $message = Config::get('messages.PAYMENT_SUCCESSFUL');
                        return array(
                            'success' => 'true',
                            'message' => $message,
                            'payment' => $response_payment,
                            'paymentId' => $response_payment->id
                        );
                    }
                }
            }
            else{
                \Log::debug("==== update company subscription on payment! ---------------- ");
                $master_subscription = $this->company->getSubscriptionType($company_id);
                $payments = array(
                    'req_date_time' => Carbon::now(),
                    'object' => $response['charge']['object'],
                    'req_currency' => $currency,
                    'req_amount' => $subscription_fee,
                    'res_date_time' => date("Y-m-d H:i:s", $response['charge']['created']),
                    'res_id' => $response['charge']['id'],
                    'res_currency' => strtoupper($response['charge']['currency']),
                    'res_amount' => $response['charge']['amount'] / 100,
                    'company_id' => $company_id,
                    'tx_type' => $tx_type,
                    'tx_status' => 1,
                    'charge_id' => $response['charge']['id'],
                    'balance_transaction' => $response['charge']['balance_transaction'],
                    'created_by' => Auth::user()->id
                );
                $response_payment = $this->payment->create($payments);

                if ($response_payment) {
                    $company_subscription = array(
                        'company_id' => $company_id,
                        'master_subscription_id' => $master_subscription[0]->id,
                        'payment_id' => $response_payment->id,
                        'created_by' => Auth::user()->id,
                        'amount' => $subscription_fee,
                        'discount'=>$discount
                    );
                    if ($company_details[0]['entity_type'] == 2) {
                        \Log::debug("entity type is 2");
                        if ($subscribe_plan) {
                            $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($subscribe_plan, $company_id,false,false,$coupon_id);
                            \Log::debug("===== plans " . print_r($plansParam, true));
                            // save company subscription plan
                            $subscribe_planObj = $this->company_subscription_plan->create($plansParam);
                            \Log::debug("===== sub plan id " . $subscribe_planObj->id);
                            $company_subscription['company_subscription_plan_id'] = $subscribe_planObj->id;
                            $response_subscription = $this->company_subscription->create($company_subscription);
                        }else {
                            $response_subscription = $this->company_subscription->create($company_subscription);
                        }
                    }else {
                        \Log::debug("entity type is NOT 2");
                        $response_subscription = $this->company_subscription->create($company_subscription);
                    }

                    if ($response_subscription) {
                        $message = Config::get('messages.PAYMENT_SUCCESSFUL');
                        return array(
                            'success' => 'true',
                            'message' => $message,
                            'payment' => $response_payment,
                            'paymentId' => $response_payment->id
                        );
                    }
                }
            }
        } else {
            $message = Config::get('messages.PAYMENT_UNSUCCESSFUL');
            return array('success' => 'false', 'message'=> $message);

        }
    }


    /**
     * company charger for add license, registration.
     *
     * @param  string  $customer
     * @param  string $currency
     * @param  int $amount
     * @return \Illuminate\Http\Response
     */
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

    public function returnPaymentFee($company_id)
    {
        $subscription  = $this->company_subscription->getPaymentDetail($company_id);
        if(count($subscription) > 0) {
            $payment_detail = $this->payment->find($subscription[0]->payment_id);
            try {
                $refund = $this->stripe->refunds()->create($payment_detail->res_id);
                $payments = array(
                    'req_date_time' => Carbon::now(),
                    'object'        => $refund['object'],
                    'req_currency'  => strtoupper($refund['currency']),
                    'req_amount'    => $refund['amount']/100,
                    'res_date_time' => $refund['created'],
                    'res_id'        => $refund['id'],
                    'res_currency'  => strtoupper($refund['currency']),
                    'res_amount'    => $refund['amount']/100,
                    'company_id'    => $company_id,
                    'tx_type'       => 'subscription',
                    'charge_id'     => $refund['charge'],
                    'balance_transaction' => $refund['balance_transaction'],
                    'created_by'    => Auth::user()->id
                );
                $response_payment = $this->payment->create($payments);
                return array('success' => true, 'refund' => $refund);
            } catch(InvalidRequestException  $e) {
                // Get the status code
                $code = $e->getCode();

                // Get the error message returned by Stripe
                $message = $e->getMessage();

                // Get the error type returned by Stripe
                $type = $e->getErrorType();

                return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
            }
        } else {
            return array('success' => true, 'refund' => 0);
        }

    }

    public function sendMails($email, $name, $layout, $subject, $data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Search Appointments.
     *
     * @return json
     */
    public function getAllPayments()
    {
        $data = array();
        $fromDate = $_GET['fromDate'];
        $toDate = $_GET['toDate'];
        $txId = $_GET['txId'];
        $responseId = $_GET['responseId'];
        $txStatus = $_GET['txStatus'];
        $txType = $_GET['txType'];

        $fromDate = ($fromDate != "") ? date('Y-m-d H:i:s', strtotime($fromDate)) : "";
        $toDate = ($toDate != "") ? date('Y-m-d H:i:s', strtotime($toDate)) : "";

        $group_id = Auth::user()->master_user_group_id;

        if($group_id == Config::get('simplifya.MasterAdmin')){
            $companyType = $_GET['companyType'];
            $companyName = $_GET['companyName'];
        }
        else{
            $companyType = "";
            $companyName = Auth::user()->company_id;
        }

        $payments = $this->payment->getAllPayments($fromDate, $toDate, $txId, $responseId, $companyName,$txStatus,$txType);

        foreach ($payments as $payment) {
            $companyTypeSearch = $this->company->find($payment['company_id'], array("*"));
            $entityType = $this->masterEntity->find($companyTypeSearch->entity_type);

            $requestDate   = date('m/d/Y g:i a', strtotime(str_replace('/', '-', $payment['req_date_time'])));
            $resDate   = date('m/d/Y g:i a', strtotime(str_replace('/', '-', $payment['res_date_time'])));

            // if Marijuana master admin and search by company type
            if($group_id == 1 && $companyType != 0) {
                if ($payment["company"]["entity_type"] == $companyType){
                    $data[] = array(
                        $requestDate,
//                        $payment['tx_id'],
                        $payment['object'],
                        strtoupper($payment['req_currency']),
                        $payment['req_amount'],
                        $resDate,
                        $payment['res_id'],
                        strtoupper($payment['res_currency']),
                        $payment['res_amount'],
                        $entityType->name,
                        $companyTypeSearch->name,
                        ($payment['tx_status'] == 1)? "Succeeded" : "Failed"
                    );
                }
            }
            else{
                $data[] = array(
                    $requestDate,
//                    $payment['tx_id'],
                    $payment['object'],
                    strtoupper($payment['req_currency']),
                    $payment['req_amount'],
                    $resDate,
                    $payment['res_id'],
                    strtoupper($payment['res_currency']),
                    $payment['res_amount'],
                    $entityType->name,
                    $companyTypeSearch->name,
                    ($payment['tx_status'] == 1)? "Succeeded" : "Failed"
                );
            }
        }
        return response()->json(["data" => $data]);
    }

    /**
     * Create refund when canceled an appointment
     * @param $paymentId
     * @param $companyId
     * @return array
     */
    public function returnAppointmentPaymentFee($paymentId, $companyId)
    {
        $payment_detail = $this->payment->find($paymentId);
        try {
            $refund = $this->stripe->refunds()->create($payment_detail->res_id);
            $payments = array(
                'req_date_time' => Carbon::now(),
                'object'        => $refund['object'],
                'req_currency'  => strtoupper($refund['currency']),
                'req_amount'    => $refund['amount']/100,
                'res_date_time' => $refund['created'],
                'res_id'        => $refund['id'],
                'res_currency'  => strtoupper($refund['currency']),
                'res_amount'    => $refund['amount']/100,
                'company_id'    => $companyId,
                'tx_type'       => 'Appointment',
                'charge_id'     => $refund['charge'],
                'balance_transaction' => $refund['balance_transaction'],
                'created_by'    => Auth::user()->id
            );
            $response_payment = $this->payment->create($payments);
            return array('success' => true, 'refund' => $refund);
        } catch(InvalidRequestException  $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
        }
    }
}
