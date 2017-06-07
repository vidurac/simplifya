<?php

namespace App\Http\Controllers\Web;

/**
 * Created by PhpStorm.
 * User: Harsha
 */

use App\Events\MjbSignUpSupport;
use App\Repositories\CompanyLocationLicenseRepository;
use App\Repositories\CompanySubscriptionPlanRepository;
use App\Repositories\CouponsRepository;
use App\Repositories\MasterReferralsRepository;
use App\Repositories\RequestsRepository;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Exception\NotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Config;
use App\Repositories\EntityTypeRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\UsersRepository;
use App\Repositories\UserGroupesRepository;
use App\Repositories\LicenseLocationRepository;
use App\Repositories\CompanyCardRepository;
use App\Repositories\PaymentRepository;
use App\Http\Requests\CompanyLocationRequest;
use App\Http\Requests\LicenseLocationRequest;
use App\Http\Requests\ChangeBusinessInfoRequest;
use App\Repositories\MasterSubscriptionRepository;
use App\Repositories\CompanySubscriptionRepository;
use App\Repositories\MasterCountryRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\UploadRepository;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\CompanyPaymentRequest;
use App\Models\MasterCountry;
use Illuminate\Support\Facades\Hash;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Lib\sendMail;
use App\Lib\ProrateDayCalculation;
use Illuminate\Support\Facades\Input;
use App\Events\AdminMailRequest;
use App\Events\CcGeMailRequest;
use App\Events\MjbMailRequest;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Lib\CsvGenerator;
use Illuminate\Support\Facades\URL;
use Mockery\CountValidator\Exception;

/**
 * Class CompanyController
 * @package App\Http\Controllers\Web
 *   approve_status    payment_status  active_status
 *  1 pending           1                1 active
 *  2 active            0                2 in-active
 *  3 reject                             3 expired
 */
class CompanyController extends Controller
{
    private $entity;
    private $company;
    private $user;
    private $user_groupe;
    private $stripe;
    private $company_location;
    private $country;
    private $master_country;
    private $license_location;
    private $subscription;
    private $payment;
    private $company_subscription;
    private $company_card;
    private $csv;
    private $master_data;
    private $company_subscription_plan;
    private $company_location_license;
    private $coupon;
    private $upload;
    private $referral;

    /**
     * CompanyController constructor.
     * @param EntityTypeRepository $entity
     * @param CompanyRepository $company
     * @param UsersRepository $user
     * @param UserGroupesRepository $user_groupe
     * @param CompanyLocationRepository $company_location
     * @param LicenseLocationRepository $license_location
     * @param MasterCountry $country
     * @param MasterSubscriptionRepository $subscription
     * @param PaymentRepository $payment
     * @param CompanySubscriptionRepository $company_subscription
     * @param CompanyCardRepository $company_card
     * @param CsvGenerator $csv
     * @param MasterCountryRepository $master_country
     * @param MasterUserRepository $master_data
     * @param CompanySubscriptionPlanRepository $company_subscription_plan
     * @param CompanyLocationLicenseRepository $company_location_license
     */
    public function __construct(EntityTypeRepository $entity, CompanyRepository $company, UsersRepository $user, UserGroupesRepository $user_groupe, CompanyLocationRepository $company_location, LicenseLocationRepository $license_location, MasterCountry $country, MasterSubscriptionRepository $subscription, PaymentRepository $payment, CompanySubscriptionRepository $company_subscription, CompanyCardRepository $company_card, CsvGenerator $csv, MasterCountryRepository $master_country, MasterUserRepository $master_data,CompanySubscriptionPlanRepository $company_subscription_plan, CompanyLocationLicenseRepository $company_location_license, CouponsRepository $coupon,UploadRepository $upload,MasterReferralsRepository $referral)
    {
        $this->entity = $entity;
        $this->company = $company;
        $this->user = $user;
        $this->user_groupe = $user_groupe;
        $this->company_location = $company_location;
        $this->country = $country;
        $this->master_country = $master_country;
        $this->license_location = $license_location;
        $this->subscription = $subscription;
        $this->payment = $payment;
        $this->company_card = $company_card;
        $this->company_subscription = $company_subscription;
        $this->stripe = Stripe::make(Config::get('simplifya.STRIPE_KEY'));
        $this->csv = $csv;
        $this->master_data = $master_data;
        $this->company_subscription_plan = $company_subscription_plan;
        $this->company_location_license = $company_location_license;
        $this->coupon = $coupon;
        $this->upload = $upload;
        $this->referral = $referral;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        //$this->companyType($entity_type);


        //get all master data information
        $master_data = $this->master_data->all(array('*'));
        $subFee = '';
        foreach ($master_data as $data) {
            if ($data->name == 'SUBSFEE') {
                $subFee = $data->value;
            }
        }

        if (Auth::check()) {
            $entity = $this->entity->getPublicEntities();
            return view('auth.signUp')->with(array('entities' => $entity));
        } else {
            $entity = $this->entity->getPublicEntities();
            return view('auth.signUp')->with(array('entities' => $entity));
        }

    }

    public function companyType()
    {
        if (isset($_GET['entity_type']))

        $entity_type = $_GET['entity_type'];
        //get all master data information
        $master_data = $this->master_data->all(array('*'));
        $subFee = '';
        foreach ($master_data as $data) {
            if ($data->name == 'SUBSFEE') {
                $subFee = $data->value;
            }
        }

        if (Auth::check()) {
            //$entity = $this->entity->getPublicEntities();
            return view('company.create')->with(array('entity_type' => $entity_type, 'cc_ge_subscription' => $subFee));
        } else {
            //$entity_type = 2;
            $entity = $this->entity->getPublicEntities();
            return view('company.create')->with(array('entity_type' => $entity_type, 'cc_ge_subscription' => $subFee));
        }

    }

    /**
     * @param null $token
     * @return $this
     */
    public function mjbRegister($token = null) {

        $entity_type = Config::get('simplifya.MarijuanaBusiness');
        $subFeeSetting = $this->master_data->findBy('name', 'SUBSFEE');
        $subFee = '';
        if (isset($subFeeSetting)) {
            $subFee = ($subFeeSetting->value == 1)? true : false;
        }
        $refToken = null;
        if (isset($token)) {
            // todo validate token

            $referral_code = $this->coupon->getReferralByToken($token);
            if (!isset($referral_code)) {
                abort(404);
            }
            $refToken = $token;
        }

        if (Auth::check()) {
            //$entity = $this->entity->getPublicEntities();
            return view('company.create')->with(array('entity_type' => $entity_type, 'cc_ge_subscription' => $subFee, 'ref_token' => $refToken ));
        } else {
            //$entity_type = 2;
            $entity = $this->entity->getPublicEntities();
            return view('company.create')->with(array('entity_type' => $entity_type, 'cc_ge_subscription' => $subFee, 'ref_token' => $refToken));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        //declare and initialize variables
        $entity_type = $request->entity_type;
        $name_of_business = $request->name_of_business;
        $referrel_token = $request->ref_token;
        $company_reg_no = $request->company_registration_no;
        $your_name = $request->your_name;
        $email = $request->email;
        $password = Hash::make($request->password);
        $cc_ge_subscription = $request->cc_ge_subscription;
        if ($entity_type != 2 && $cc_ge_subscription == 1) {
            $card_no = str_replace(' ', '', $request->card_number);
            $ccv_number = $request->ccv_number;
            $exp_month = $request->exp_month;
            $exp_year = $request->exp_year;
            $subscrib_fee = $request->subscrib_fee;
        }
        //\Log::info('================ last 4 digit:================'.$cc_ge_subscription." ".$entity_type); die;
        $simplify_email = Config::get('simplifya.admin_email');
        Session::put('entity_type', $entity_type);
        //$simplify_email = Config::get('simplifya.SIMPLIFIYA_SUPPORT_EMAIL');
        if ($cc_ge_subscription == 1 && $entity_type != 2) {
            return $this->companyRegistrationWithSubsFee($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $card_no, $ccv_number, $exp_month, $exp_year, $subscrib_fee, $simplify_email);
        } else {
            //find referral_code id using token

            $referral_code = null;
            if (isset($referrel_token)) {
                $referral_code = $this->coupon->getReferralByToken($referrel_token);
                \Log::debug("=== referral code found");
                \Log::debug("=== referral code id : " . $referral_code->id);

            }

            if (isset($referral_code)) {
                return $this->companyRegistrationWithoutCardDetails($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $simplify_email, $referral_code->id);
            }else {
                return $this->companyRegistrationWithoutCardDetails($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $simplify_email);
            }

            //return $this->companyRegistrationWithoutSubsFee($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $card_no, $ccv_number, $exp_month, $exp_year, $subscrib_fee, $simplify_email);
        }

       /* if ($entity_type == 2) {
            return $this->mjBCompanyRegistrationWithoutSubsFee($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $simplify_email);
        }*/
    }

    /**
     * Company Registration with subscription fee
     * @param $entity_type
     * @param $name_of_business
     * @param $company_reg_no
     * @param $your_name
     * @param $email
     * @param $password
     * @param $card_no
     * @param $ccv_number
     * @param $exp_month
     * @param $exp_year
     * @param $subscrib_fee
     * @param $simplify_email
     * @return \Illuminate\Http\JsonResponse
     */
    private function companyRegistrationWithSubsFee($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $card_no, $ccv_number, $exp_month, $exp_year, $subscrib_fee, $simplify_email)
    {
        //declare and initialize variables
        $data = array();
        $role_id            = '';
        $strip_id           = '';
        $card_response      = '';
        $entity_name        = '';
        $base_url=URL::to('/');

        //get user group ID
        $roles = $this->user_groupe->getGroupeId($entity_type);

        foreach ($roles as $role) {
            if ($role['name'] == 'Master Admin') {
                $role_id = $role['id'];
            }
        }

        //getting last 4 dogits of company reg no
        $fein_last_digits = substr($company_reg_no, -4);
        //\Log::info('================ last 4 digit:================'.$fein_last_digits);

        //encrypt the company_reg_no
        $company_reg_no = sha1($company_reg_no);

        $is_regiter = $this->company->isExistCompany($company_reg_no);
        $user_data = $this->user->isuserExist($email);
        //$MJB_FREE_SIGN_UP = $this->master_data->getMJBFOC('MJB_FREE_SIGN_UP');

        //\Log::info('================ last 4 digit:================'.print_r($MJB_FREE_SIGN_UP,true)); die;

        if (!isset($is_regiter[0])) {
            if ($user_data) {
                $message = Config::get('messages.USER_ALREADY_EXISTS');
                return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
            } else {
                $status = false;
                // Start DB transaction
                DB::beginTransaction();

                try {
                    //Execute queries here ...
                    $company_data = array(
                        'name' => $name_of_business,
                        'entity_type' => $entity_type,
                        'reg_no' => $company_reg_no,
                        'fein_last_digits' => $fein_last_digits,
                        'status' => 0,
                        'is_first_attempt' => 0
                    );
                    $response = $this->company->create($company_data);
                    if ($response) {
                        $company_id = $response->id;
                        $user = array(
                            'name' => $your_name,
                            'email' => $email,
                            'password' => $password,
                            'company_id' => $company_id,
                            'master_user_group_id' => $role_id,
                            'status' => '1'
                        );
                        $user_response = $this->user->create($user);
                        if ($user_response) {
                            $customer = $this->addCustomer($email, $name_of_business);
                            if ($customer['success']) {
                                $strip_id = $customer['customer']['id'];
                                $data['stripe_id'] = $strip_id;
                                if (!empty($data)) {
                                    $update_comapany = $this->company->updateById($response->id, $data);
                                    if ($update_comapany) {
                                        $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id);
                                        if ($card_detils['success']) {
                                            $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => $user_response->id);
                                            $this->company_card->create($data);
                                            if ($entity_type == 2) {
                                                $entity_name = 'Marijuana Business';
                                                $this->company->updateCompanyByStatusAndId(0, $company_id);
                                                $simplifya_name = Config::get('messages.COMPANY_NAME');

                                                $admin_data = new \stdClass();
                                                $admin_data->company_name = $name_of_business;
                                                $admin_data->entity_name = $entity_name;
                                                $admin_data->entity_type = $entity_type;
                                                $admin_data->simplify_email = $simplify_email;
                                                $admin_data->companyname = $simplifya_name;
                                                $admin_data->layout = 'emails.mjb_registration';
                                                $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                                                $admin_data->registrant = $your_name;
                                                $admin_data->registrantEmail = $email;

                                                event(new AdminMailRequest($admin_data));

                                                $mjb_data = new \stdClass();
                                                $mjb_data->name = $your_name;
                                                $mjb_data->email = $email;
                                                $mjb_data->companyname = $simplifya_name;
                                                event(new MjbMailRequest($mjb_data));
                                                // All good
                                                DB::commit();
                                                $message = Config::get('messages.MJB_COMPANY_REGISTRATION_SUCCESS');
                                                $login_button='<a href="'.$base_url.'" class="btn btn-lg btn-success">Log In</a>';
                                                Session::put('reg_message', $message);
                                                Session::put('reg_button', $login_button);
                                                return response()->json(array('success' => 'true', 'message' => $message));
                                            } elseif ($entity_type == 3 || $entity_type == 4) {
                                                if ($entity_type == 3) {
                                                    $entity_name = 'Compliance Company';
                                                    $subject = 'Action required – New CC registration';
                                                } else {
                                                    $entity_name = 'Government Entity';
                                                    $subject = 'Action required – New GE registration';
                                                }
                                                $currency = 'USD';
                                                $customer_charge = $this->customerCharges($strip_id, $currency, $subscrib_fee);
                                                if ($customer_charge['success']) {
                                                    $tx_type = 'subscription';
                                                    $this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type);
                                                    $this->company->updateCompanyByStatusAndId(1, $company_id);
                                                    $simplifya_name = Config::get('messages.COMPANY_NAME');

                                                    $admin_data = new \stdClass();
                                                    $admin_data->company_name = $name_of_business;
                                                    $admin_data->entity_name = $entity_name;
                                                    $admin_data->entity_type = $entity_type;
                                                    $admin_data->simplify_email = $simplify_email;
                                                    $admin_data->companyname = $simplifya_name;
                                                    $admin_data->layout = 'emails.cc_ge_registration';
                                                    $admin_data->subject = $subject;
                                                    $admin_data->registrant = $your_name;
                                                    event(new AdminMailRequest($admin_data));

                                                    $cc_ge_data = new \stdClass();
                                                    $cc_ge_data->name = $your_name;
                                                    $cc_ge_data->email = $email;
                                                    $cc_ge_data->companyname = $simplifya_name;
                                                    event(new CcGeMailRequest($cc_ge_data));

                                                    $message = Config::get('messages.CC_GE_COMPANY_REGISTRATION_SUCCESS');
                                                    $login_button= '<a href="http://simplifya.com/" class="btn btn-lg btn-success">HOME</a>';
                                                    Session::put('reg_message', $message);
                                                    Session::put('reg_button', $login_button);
                                                    // All good
                                                    DB::commit();
                                                    return response()->json(array(
                                                        'success' => 'true',
                                                        'message' => $message
                                                    ));
                                                } else {
                                                    $this->company->updateCompanyByStatusAndId(0, $company_id);
                                                    // All good
                                                    DB::commit();
                                                    $message = Config::get('messages.COMPANY_REGISTRATION_SUCCESS');
                                                    return response()->json(array(
                                                        'success' => 'true',
                                                        'message' => $message
                                                    ));
                                                }
                                            }
                                        } else {
                                            DB::rollback();
                                            return response()->json(array(
                                                'success' => 'false',
                                                'message' => $card_detils['message'],
                                                'is_redirect' => 'false'
                                            ));

                                        }
                                    }
                                }
                            } else {
                                DB::rollback();
                                return response()->json(array(
                                    'success' => 'false',
                                    'message' => $customer['message'],
                                    'is_redirect' => 'false'
                                ));
                            }
                        } else {
                            DB::rollback();
                            $message = Config::get('messages.USER_ADDED_FAILED');
                            return response()->json(array('success' => 'false', 'message' => $message));
                        }
                    } else {
                        $message = Config::get('messages.COMPANY_REGISTRATION_FAILED');
                        return response()->json(array('success' => 'false', 'message' => $message));
                    }
                    $status = true;
                } catch (Exception $ex) {
                    // rollback database transaction if Something went wrong
                    DB::rollback();
                }
            }
        } else {
            $message = Config::get('messages.COMPANY_ALREADY_REGISTRATION');
            return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
        }
    }

    /**
     * Company Registration without subscription fee
     * @param $entity_type
     * @param $name_of_business
     * @param $company_reg_no
     * @param $your_name
     * @param $email
     * @param $password
     * @param $card_no
     * @param $ccv_number
     * @param $exp_month
     * @param $exp_year
     * @param $subscrib_fee
     * @param $simplify_email
     * @return \Illuminate\Http\JsonResponse
     */
    private function companyRegistrationWithoutSubsFee($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $card_no, $ccv_number, $exp_month, $exp_year, $subscrib_fee, $simplify_email)
    {
        //declare variables
        $data = array();
        $base_url=URL::to('/');
        $role_id = '';
        $strip_id = '';
        $card_response = '';
        $entity_name = '';

        //get group ID
        $roles = $this->user_groupe->getGroupeId($entity_type);

        foreach ($roles as $role) {
            if ($role['name'] == 'Master Admin') {
                $role_id = $role['id'];
            }
        }

        $is_regiter = $this->company->isExistCompany($company_reg_no);
        $user_data = $this->user->isuserExist($email);

        if (!isset($is_regiter[0])) {
            if ($user_data) {
                $message = Config::get('messages.USER_ALREADY_EXISTS');
                return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
            } else {
                //Execute queries here ...
                $company_data = array(
                    'name' => $name_of_business,
                    'entity_type' => $entity_type,
                    'reg_no' => $company_reg_no,
                    'status' => 0,
                    'is_first_attempt' => 0
                );
                $response = $this->company->create($company_data);
                if ($response) {
                    $company_id = $response->id;
                    $user = array(
                        'name' => $your_name,
                        'email' => $email,
                        'password' => $password,
                        'company_id' => $company_id,
                        'master_user_group_id' => $role_id,
                        'status' => '1'
                    );

                    $user_response = $this->user->create($user);
                    if ($user_response) {
                        $customer = $this->addCustomer($email, $name_of_business);
                        if ($customer['success']) {
                            $strip_id = $customer['customer']['id'];
                            $data['stripe_id'] = $strip_id;
                            if (!empty($data)) {
                                $update_comapany = $this->company->updateById($response->id, $data);
                                if ($update_comapany) {
                                    $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id);
                                    if ($card_detils['success']) {
                                        $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => $user_response->id);
                                        $this->company_card->create($data);
                                        if ($entity_type == 2) {
                                            $entity_name = 'Marijuana Business';
                                            $this->company->updateCompanyByStatusAndId(0, $company_id);
                                            $simplifya_name = Config::get('messages.COMPANY_NAME');

                                            $admin_data = new \stdClass();
                                            $admin_data->company_name = $name_of_business;
                                            $admin_data->entity_name = $entity_name;
                                            $admin_data->entity_type = $entity_type;
                                            $admin_data->simplify_email = $simplify_email;
                                            $admin_data->companyname = $simplifya_name;
                                            $admin_data->layout = 'emails.mjb_registration';
                                            $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                                            $admin_data->registrant = $your_name;

                                            event(new AdminMailRequest($admin_data));

                                            $mjb_data = new \stdClass();
                                            $mjb_data->name = $your_name;
                                            $mjb_data->email = $email;
                                            $mjb_data->companyname = $simplifya_name;
                                            event(new MjbMailRequest($mjb_data));

                                                $message = Config::get('messages.MJB_COMPANY_REGISTRATION_SUCCESS');
                                                $login_button='<a href="'.$base_url.'" class="btn btn-lg btn-success">Log In</a>';
                                                Session::put('reg_message', $message);
                                                Session::put('reg_button', $login_button);
                                                return response()->json(array('success' => 'true', 'message' => $message));
                                            } elseif ($entity_type == 3 || $entity_type == 4) {
                                                if ($entity_type == 3) {
                                                    $entity_name = 'Compliance Company';
                                                    $subject = 'Action required – New CC registration';
                                                } else {
                                                    $entity_name = 'Government Entity';
                                                    $subject = 'Action required – New GE registration';
                                                }
                                            $message = Config::get('messages.CC_GE_COMPANY_REGISTRATION_SUCCESS');
                                            $login_button= '<a href="http://simplifya.com/" class="btn btn-lg btn-success">HOME</a>';
                                            Session::put('reg_message', $message);
                                            Session::put('reg_button', $login_button);
                                            return response()->json(array('success' => 'true', 'message' => $message));
                                        } elseif ($entity_type == 3 || $entity_type == 4) {
                                            if ($entity_type == 3) {
                                                $entity_name = 'Compliance Company';
                                                $subject = 'Action required – New CC registration';
                                            } else {
                                                $entity_name = 'Government Entity';
                                                $subject = 'Action required – New GE registration';
                                            }

                                            $this->company->updateCompanyByStatusAndId(1, $company_id);
                                            $simplifya_name = Config::get('messages.COMPANY_NAME');

                                            $admin_data = new \stdClass();
                                            $admin_data->company_name = $name_of_business;
                                            $admin_data->entity_name = $entity_name;
                                            $admin_data->entity_type = $entity_type;
                                            $admin_data->simplify_email = $simplify_email;
                                            $admin_data->companyname = $simplifya_name;
                                            $admin_data->layout = 'emails.cc_ge_registration';
                                            $admin_data->subject = $subject;
                                            $admin_data->registrant = $your_name;
                                            event(new AdminMailRequest($admin_data));

                                            $cc_ge_data = new \stdClass();
                                            $cc_ge_data->name = $your_name;
                                            $cc_ge_data->email = $email;
                                            $cc_ge_data->companyname = $simplifya_name;
                                            event(new CcGeMailRequest($cc_ge_data));

                                                $message = Config::get('messages.CC_GE_COMPANY_REGISTRATION_SUCCESS');
                                                $login_button= '<a href="http://simplifya.com/" class="btn btn-lg btn-success">HOME</a>';
                                                Session::put('reg_message', $message);
                                                Session::put('reg_button', $login_button);
                                            $message = Config::get('messages.CC_GE_COMPANY_REGISTRATION_SUCCESS');
                                            Session::put('reg_message', $message);
                                            Session::put('reg_button', $login_button);

                                            return response()->json(array(
                                                'success' => 'true',
                                                'message' => $message
                                            ));

                                        }
                                    } else {
                                        return response()->json(array(
                                            'success' => 'false',
                                            'message' => $card_detils['message'],
                                            'is_redirect' => 'false'
                                        ));

                                    }
                                }
                            }
                        } else {
                            return response()->json(array(
                                'success' => 'false',
                                'message' => $customer['message'],
                                'is_redirect' => 'false'
                            ));
                        }
                    } else {
                        $message = Config::get('messages.USER_ADDED_FAILED');
                        return response()->json(array('success' => 'false', 'message' => $message));
                    }
                } else {
                    $message = Config::get('messages.COMPANY_REGISTRATION_FAILED');
                    return response()->json(array('success' => 'false', 'message' => $message));
                }
            }
        } else {
            $message = Config::get('messages.COMPANY_ALREADY_REGISTRATION');
            return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
        }
    }

    /**
     * master card registration for registered company
     * @param $card_no
     * @param $exp_month
     * @param $ccv_number
     * @param $exp_year
     * @param $strip_id
     * @return array
     */
    private function cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id)
    {

        try {
            $token = $this->stripe->tokens()->create([
                'card' => [
                    'number' => $card_no,
                    'exp_month' => $exp_month,
                    'cvc' => $ccv_number,
                    'exp_year' => $exp_year,
                ],
            ]);

            $card = $this->stripe->cards()->create($strip_id, $token['id']);

            return array('success' => true, 'card' => $card);
        } catch (CardErrorException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success' => false, 'code' => $code, 'message' => $message, 'type' => $type);
        }
    }

    /**
     * add new customers into stripe account
     * @param $email
     * @param $name_of_business
     * @return array
     */
    private function addCustomer($email, $name_of_business)
    {
        try {
            $customer = $this->stripe->customers()->create(['email' => $email, 'description' => $name_of_business]);
            if ($customer) {
                return array('success' => true, 'customer' => $customer);
            }
        } catch (NotFoundException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success' => false, 'code' => $code, 'message' => $message, 'type' => $type);
        }
    }

    /**
     * pay company registrations subscription fee
     * @param $customer
     * @param $currency
     * @param $amount
     * @return array
     */
    private function customerCharges($customer, $currency, $amount)
    {
        try {
            $charge = $this->stripe->charges()->create([
                'customer' => $customer,
                'currency' => $currency,
                'amount' => $amount,
            ]);

            return array('success' => true, 'charge' => $charge);
        } catch (NotFoundException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success' => false, 'code' => $code, 'message' => $message, 'type' => $type);
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

    /**
     * retrieve company details by logged in user
     * @return company details
     */
    public function companyInfo()
    {
        //get all master data information
        $master_data = $this->master_data->all(array('*'));
        $subFee = '';
        foreach ($master_data as $data) {
            if ($data->name == 'SUBSFEE') {
                $subFee = $data->value;
            }
        }

        $company_id = Auth::User()->company_id;
        $countries = $this->country->all(array('*'));
        $company_details = $this->company->getCompanyDetailsbyId($company_id);

        //\Log::debug("++++++++++".print_r($company_details[0],true));
        //die();

        $card_existed = $this->company_card->isCompanyCardAdded($company_id);
        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        $company_detail = array(
            'user_name' => Auth::User()->name,
            'user_email' => Auth::User()->email,
            'company_name' => $company_details[0]->name,
            'company_reg' => $company_details[0]->reg_no,
            'fein_last_digits' => $company_details[0]->fein_last_digits,
            'company_status' => $company_details[0]->status,
            'company_type' => $company_details[0]->master_entity_name,
            'foc' => $company_details[0]->foc,
            'cc_data_added' =>$cc_added
        );

        //\Log::debug('====compnay details====');
        //print_r($company_details);

        $company_location = $this->company_location->getLocationByCompanyId($company_id);

        $data_to_view = array(
                            'cc_ge_subscription' => $subFee,
                            'countries' => $countries,
                            'company_id' => $company_id,
                            'company_location' => $company_location,
                            'entity_type' => $company_details[0]->entity_type,
                            //'coupon_referral_id' => $company_details[0]->coupon_referral_id,
                            'company_detail' => $company_detail,
                            'page_title' => 'Company Information',
                            'master_user_group_id' => Auth::User()->master_user_group_id
                        );

        //validate coupon and referral codes with expiary dates
        $current_date = date('Y-m-d');
        if($company_details[0]->start_date <= $current_date && $company_details[0]->end_date >= $current_date)
        {
            $data_to_view['coupon_code'] = $company_details[0]->coupon_code;
            $data_to_view['coupon_type'] = $company_details[0]->coupon_type;
            $data_to_view['coupon_amount'] = $company_details[0]->coupon_amount;
            $data_to_view['amount_type'] = $company_details[0]->amount_type;
            $data_to_view['coupon_details_id'] = $company_details[0]->coupon_details_id;
            $data_to_view['coupon_id'] = $company_details[0]->coupon_id;
            $data_to_view['coupon_referral_id'] = $company_details[0]->coupon_referral_id;
            //\Log::debug(print_r("hit.....",true));
        }
        else
        {
            $data_to_view['coupon_code'] = '';
            $data_to_view['coupon_type'] = '';
            $data_to_view['coupon_amount'] = 0;
            $data_to_view['amount_type'] = '';
            $data_to_view['coupon_details_id'] = '';
            $data_to_view['coupon_id'] = '';
            $data_to_view['coupon_referral_id'] = 0;
            //\Log::debug(print_r("not hit.....",true));
        }

        return view('company.add_company_info')->with($data_to_view);
        //return view('company.add_company_info')->with(array('cc_ge_subscription' => $subFee, 'countries' => $countries, 'company_id' => $company_id, 'company_location' => $company_location, 'entity_type' => $company_details[0]->entity_type, 'company_detail' => $company_detail, 'page_title' => 'Company Information', 'master_user_group_id' => Auth::User()->master_user_group_id));
    }

    /**
     * Insert new company location for registered company
     * @param CompanyLocationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCompanyLocation(CompanyLocationRequest $request)
    {
        //declare and initialize variables
        $user_id = Auth::user()->id;
        $company_id = Auth::user()->company_id;
        $name_of_location = $request->name_of_location;
        $add_line_1 = $request->address_line_1;
        $add_line_2 = $request->address_line_2;
        $country = $request->country;
        $state = $request->state;
        $city = $request->cities;
        $zip_code = $request->zip_code;
        $phone_no = $request->phone_number;
        $entity_type = $request->entity_type;

        $data = array('name' => $name_of_location,
            'company_id' => $company_id,
            'city_id' => $city,
            'states_id' => $state,
            'address_line_1' => $add_line_1,
            'address_line_2' => $add_line_2,
            'zip_code' => $zip_code,
            'phone_number' => $phone_no,
            'status' => '1',
            'created_by' => $user_id,
            'updated_by' => ''
        );

        \Log::info('================ log:================'.print_r(json_encode($entity_type),true));

        if ($entity_type == 3 || $entity_type == 4) {
            $locations = $this->company_location->getCompanyLocationById($company_id);
            if ($locations->isEmpty() || (isset($locations[0]) && $locations[0]->status == 0)) {
                $response = $this->company_location->create($data);
                if ($response) {
                    $this->company->updateIsAttempt($company_id);
                    $message = Config::get('messages.BUSINESS_LOCATION_ADD');
                    return response()->json(array('success' => 'true', 'message' => $message));
                } else {
                    $message = Config::get('messages.BUSINESS_LOCATION_FAIL');
                    return response()->json(array('success' => 'false', 'message' => $message));
                }
            } else {
                $message = Config::get('messages.BUSINESS_LOCATION_LIMIT');
                return response()->json(array('success' => 'false', 'message' => $message));
            }

        } elseif ($entity_type == 2) {
            $response = $this->company_location->create($data);
            if ($response) {
                $message = Config::get('messages.BUSINESS_LOCATION_ADD');
                return response()->json(array('success' => 'true', 'message' => $message));
            } else {
                $message = Config::get('messages.BUSINESS_LOCATION_FAIL');
                return response()->json(array('success' => 'false', 'message' => $message));
            }
        }

    }

    /**
     * @param CompanyLocationRequest $request
     * @return CompanyRepository
     * update company location
     */
    public function updateBusinessLocation(CompanyLocationRequest $request)
    {
        //declare and initialize variables
        $user_id = Auth::user()->id;
        $company_id = 1;
        $location_id = $request->edit_location_id;
        $name_of_location = $request->name_of_location;
        $add_line_1 = $request->address_line_1;
        $add_line_2 = $request->address_line_2;
        $country = $request->country;
        $state = $request->state;
        $city = $request->cities;
        $zip_code = $request->zip_code;
        $phone_no = $request->phone_number;

        //create data array
        $data = array('name' => $name_of_location,
            'city_id' => $city,
            'states_id' => $state,
            'zip_code' => $zip_code,
            'address_line_1' => $add_line_1,
            'address_line_2' => $add_line_2,
            'phone_number' => $phone_no,
            'updated_by' => $user_id
        );

        $response = $this->company_location->updateCompanyLocation($data, $location_id);
        if ($response) {
            $message = Config::get('messages.BUSINESS_LOCATION_UPDATE_SUCCESS');
            return response()->json(array('success' => 'true', 'message' => $message));
        } else {
            $message = Config::get('messages.BUSINESS_LOCATION_UPDATE_FAIL');
            return response()->json(array('false' => 'true', 'message' => $message));
        }
    }

    /**
     * retrieve company locations by company id
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyLocation($company_id)
    {
        //get company locations by company ID
        $company_locations = $this->company_location->getLocationByCompanyId($company_id);
        $business_locations = array();
        foreach ($company_locations as $company_location) {
            $business_locations[] = array($company_location->name,
                ($company_location->address_line_2 != '') ? $company_location->address_line_1 . ',' . $company_location->address_line_2 : $company_location->address_line_1,
                $company_location->phone_number,
                $company_location->city_name,
                $company_location->state_name,
                $company_location->zip_code,
                $company_location->country_name,
                ($company_location->status == 1) ?
                    "<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-loc_id='$company_location->id' onclick='changeBusinessLocation({$company_location->id})'><i class='fa fa-paste'></i></a>
                        <a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Active'  data-loc_id='$company_location->id'onclick='changeBusinessLocationStatus({$company_location->id}, 2)'><i class='fa fa-thumbs-o-up'></i></a>
                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-loc_id='$company_location->id'onclick='changeBusinessLocationStatus({$company_location->id}, 0)'><i class='fa fa-trash-o'></i></a>
                        " : "
                        <a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-loc_id='$company_location->id' onclick='changeBusinessLocation({$company_location->id})'><i class='fa fa-paste'></i></a>
                        <a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Inactive' data-loc_id='$company_location->id'onclick='changeBusinessLocationStatus({$company_location->id}, 1)'><i class='fa fa-thumbs-o-down'></i></a>
                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-loc_id='$company_location->id'onclick='changeBusinessLocationStatus({$company_location->id}, 0)'><i class='fa fa-trash-o'></i></a>"
            );
        }
        return response()->json(array('data' => $business_locations), 200);
    }

    /**
     * get active company locations
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyLocationById($company_id)
    {
        //get company locations by company ID
        $locations = $this->company_location->getCompanyLocationById($company_id);
        return response()->json(array('locations' => $locations), 200);
    }

    /**
     * Display  company permission levels according to  logged in user level
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyPermissionLevelById($company_id)
    {
        //declare variables
        $data = array();
        $edit_data = array();
        $user_role = Auth::User()->master_user_group_id;
        $permissions = $this->company->getUserGroupe($company_id);
        if ($user_role == Config::get('simplifya.MjbManager')) {
            foreach ($permissions as $permission) {
                $edit_data[] = array('id' => $permission['id'], 'name' => $permission['name']);
                if ($permission['id'] != Config::get('simplifya.MjbMasterAdmin')) {
                    $data[] = array('id' => $permission['id'], 'name' => $permission['name']);
                }
            }
        } elseif (($user_role == Config::get('simplifya.MjbMasterAdmin')) || ($user_role == Config::get('simplifya.CcMasterAdmin')) || ($user_role == Config::get('simplifya.GeMasterAdmin'))) {
            foreach ($permissions as $permission) {
                $data[] = array('id' => $permission['id'], 'name' => $permission['name']);
                $edit_data[] = array('id' => $permission['id'], 'name' => $permission['name']);
            }
        } elseif ($user_role == Config::get('simplifya.MasterAdmin')) {
            foreach ($permissions as $permission) {
                $data[] = array('id' => $permission['id'], 'name' => $permission['name']);
                $edit_data[] = array('id' => $permission['id'], 'name' => $permission['name']);
            }
        }

        return response()->json(array('permissions' => $data, 'edit_permission' => $edit_data), 200);
    }

    /**
     * Company license location
     * @param LicenseLocationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyLicenseLocation(LicenseLocationRequest $request)
    {
        //declare and initialize variables
        $location_id = $request->location_id;
        $license_id = $request->license_id;
        $license_no = $request->license_no;
        $company_id = $request->company_id;
        $data = array('company_id' => $company_id, 'license_id' => $license_id, 'location_id' => $location_id, 'license_number' => $license_no, 'status' => 1);
        $location_license = $this->license_location->isExistLocationLicense($company_id, $license_id, $location_id);

        if (isset($location_license->id) && $location_license->status != 0) {
            $message = Config::get('messages.ALREADY_ADDED_LICENSE_LOCATION');
            return response()->json(array('success' => 'false', 'message' => $message));
        } else {
            $response = $this->license_location->create($data);
            if ($response) {
                $message = Config::get('messages.LICENSE_ADD_SUCCESS');
                return response()->json(array('success' => 'true', 'message' => $message));
            } else {
                $message = Config::get('messages.LICENSE_ADDED_FAIL');
                return response()->json(array('false' => 'true', 'message' => $message));
            }
        }

    }

    /**
     * Retrieve location details by location id
     * @param $loction_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBusinessLocation($loction_id)
    {
        //get company location details
        $location_detail = $this->company_location->getBusinessLocation($loction_id);

        $data = array('name' => $location_detail['location'][0]['location_name'],
            'address_1' => $location_detail['location'][0]['address_line_1'],
            'address_2' => $location_detail['location'][0]['address_line_2'],
            'city_id' => $location_detail['location'][0]['city_id'],
            'zip_code' => $location_detail['location'][0]['zip_code'],
            'states_id' => $location_detail['location'][0]['states_id'],
            'country_id' => $location_detail['location'][0]['country_id'],
            'phone_no' => $location_detail['location'][0]['phone_number'],
            'cities' => $location_detail['cities'],
            'states' => $location_detail['states']
        );
        return response()->json(array('data' => $data), 200);
    }

    /**
     * check location has user and license
     * @param $location_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUsersInLocation($location_id)
    {
        //get relevant details from repositories
        $user_locations = $this->company_location->checkUserInLocation($location_id);
        $license_locations = $this->license_location->checkLicenseInLocation($location_id);

        if (isset($user_locations[0])) {
            if (isset($license_locations[0])) {
                $message = Config::get('messages.LICENSE_AND_USER_HAS_LOCATION');
                return response()->json(array('success' => 'false', 'message' => $message));
            } else {
                $message = Config::get('messages.LOCATION_HAS_USERS');
                return response()->json(array('success' => 'false', 'message' => $message));
            }
        } else {
            if (isset($license_locations[0])) {
                $message = Config::get('messages.LOCATION_HAS_LICENSE');
                return response()->json(array('success' => 'false', 'message' => $message));
            } else {
                $message = Config::get('messages.CHANGE_LOCATION_STATUS');
                return response()->json(array('success' => 'true', 'message' => $message));
            }
        }
    }

    /**
     * company location activate and inactivate
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeBusinessLocationStatus()
    {
        //declare and initialize variables
        $status = Input::get('status');
        $location_id = Input::get('location_id');
        $message = '';
        $change_location_status = $this->company_location->changeBusinessLocationStatus($location_id, $status);
        if ($change_location_status) {
            if ($status == 1) {
                $message = Config::get('messages.LOCATION_ACTIVATE');
            } elseif ($status == 2) {
                $message = Config::get('messages.LOCATION_INACTIVATE');
            }

            return response()->json(array('success' => 'true', 'message' => $message));
        }

    }

    /**
     * get company information
     * @return $this
     */
    public function getCompanyInfo()
    {
        //declare and initialize variables
        $company_id = Auth::User()->company_id;
        $countries = $this->master_country->findWhere(array('status' => 1));
        $company_details = $this->company->getCompanyDetailsbyId($company_id);

        //add company details to $company_detail array
        $company_detail = array(
            'user_name' => Auth::User()->name,
            'user_email' => Auth::User()->email,
            'company_name' => $company_details[0]->name,
            'company_reg' => $company_details[0]->reg_no,
            'fein_last_digits' => $company_details[0]->fein_last_digits,
            'fein_display' => "***********".$company_details[0]->fein_last_digits,
            'company_status' => $company_details[0]->status,
            'company_type' => $company_details[0]->master_entity_name
        );

        //\Log::info('================ last 4 digit:================'.print_r($company_detail,true));

        //get company location details
        $company_location = $this->company_location->getLocationByCompanyId($company_id);

        $master_data = $this->master_data->all(array('*'));
        $subFee = '';
        foreach ($master_data as $data) {
            if ($data->name == 'SUBSFEE') {
                $subFee = $data->value;
            }
        }
        $card_existed = $this->company_card->isCompanyCardAdded($company_id);
        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        $image = $this->upload->findWhere(array("entity_tag" => "company", "entity_id" => $company_id, "type" => "company"))->last();

        if(empty($image)){
            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.COMPANY_LOGO_IMG_DIR'), Config::get('aws.COMPANY_DEFAULT_IMAGE'));
        }
        else{
            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.COMPANY_LOGO_IMG_DIR'), $image->name);
        }

        return view('company.edit_company_info')
            ->with(array('countries' => $countries,
                    'company_id' => $company_id,
                    'company_location' => $company_location,
                    'entity_type' => $company_details[0]->entity_type,
                    'company_detail' => $company_detail,
                    'master_user_group_id' => Auth::User()->master_user_group_id,
                    'page_title' => 'Company Information',
                    'cc_ge_subscription' => $subFee,
                    'cc_added' => $cc_added,
                    'imageUrl' => $imageUrl
                )
            );
    }

    /**
     * get location information
     * @return $this
     */
    public function getLocationInfo()
    {
        //declare and initialize variables
        $company_id = Auth::User()->company_id;
        $countries = $this->master_country->findWhere(array('status' => 1));
        $company_details = $this->company->getCompanyDetailsbyId($company_id);

        //add company details to $company_detail array
        $company_detail = array(
            'user_name' => Auth::User()->name,
            'user_email' => Auth::User()->email,
            'company_name' => $company_details[0]->name,
            'company_reg' => $company_details[0]->reg_no,
            'fein_last_digits' => $company_details[0]->fein_last_digits,
            'fein_display' => "***********".$company_details[0]->fein_last_digits,
            'company_status' => $company_details[0]->status,
            'company_type' => $company_details[0]->master_entity_name
        );

        //\Log::info('================ last 4 digit:================'.print_r($company_detail,true));

        //get company location details
        $company_location = $this->company_location->getLocationByCompanyId($company_id);

        $master_data = $this->master_data->all(array('*'));
        $subFee = '';
        foreach ($master_data as $data) {
            if ($data->name == 'SUBSFEE') {
                $subFee = $data->value;
            }
        }
        $card_existed = $this->company_card->isCompanyCardAdded($company_id);
        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        return view('company.edit_location_info')
            ->with(array('countries' => $countries,
                    'company_id' => $company_id,
                    'company_location' => $company_location,
                    'entity_type' => $company_details[0]->entity_type,
                    'company_detail' => $company_detail,
                    'master_user_group_id' => Auth::User()->master_user_group_id,
                    'page_title' => 'Company Location',
                    'cc_ge_subscription' => $subFee,
                    'cc_added' => $cc_added
                )
            );
    }

    /**
     * Update company information by company id
     * @param ChangeBusinessInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeBusinessInfo(ChangeBusinessInfoRequest $request)
    {
        //declare and initialize variables
        $user_id = Auth::User()->id;
        $company_id = Auth::User()->company_id;
        $is_active = $request->is_active;

        $company_name = $request->name_of_location;
        $reg_no = $request->reg_no;
        $data = array('name' => $company_name, 'status' => ($is_active == '') ? 2 : $is_active, 'updated_by' => $user_id);
        if($reg_no != "")
        {
            $fein_last_digits = substr($reg_no, -4);
            $data['reg_no'] = sha1($reg_no);
            $data['fein_last_digits'] = $fein_last_digits;
        }

        $response = $this->company->update($data, $company_id);
        //\Log::info("stripe card".print_r($response,true));

        if ($response) {
            if ($is_active != '') {
                if ($is_active == 2) {
                    Session::put('company_status', 2);
                    $message = Config::get('messages.COMPANY_UPDATED');
                    return response()->json(array('success' => 'true', 'message' => $message, 'is_active' => $is_active));
                } else {
                    $layout = 'emails.account_inactivate';
                    $subject = 'Simplifya account deactivation';
                    $data = array('from' => 'noreply@simplifya.com', 'system' => 'Simplifya', 'company' => 'Simplifya');
                    $this->sendMails(Auth::User()->email, Auth::User()->name, $layout, $subject, $data);
                    Session::put('company_status', 4);
                    $message = Config::get('messages.COMPANY_INACTIVATED');
                    return response()->json(array('success' => 'true', 'message' => $message, 'is_active' => $is_active));
                }
            } else {
                Session::put('company_status', 2);
                $message = Config::get('messages.COMPANY_UPDATED');
                return response()->json(array('success' => 'true', 'message' => $message, 'is_active' => $is_active));
            }
        }
    }

    /**
     * Calculation Subscription fee according to company id and entity type
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateSubscriptionFee()
    {
        //declare and initialize variables
        $company_id = Input::get('company_id');
        $entity_type = Input::get('entity_type');
        $company_details = $this->company->getCompanyDetailsbyId($company_id);
        $card_detail = '';

        if ($entity_type != 2) {
            $card_detail = $this->getCardDetails($company_id);
        } elseif ($company_details[0]->status != 0 && $entity_type == 2) {
            $card_detail = $this->getCardDetails($company_id);
        } else {
            $card_detail = null;
        }

        if ($entity_type == 2) {
            $response = $this->company->calculateSubscriptionFee($company_id);

            if (isset($response[0])) {
                $name = $response[0]->company_name;
                $entity_type = $response[0]->master_entity_name;

                $date = date("Y/m/d");
                $prorate_day_cal = new ProrateDayCalculation();
                $day_calculation = $prorate_day_cal->dayCalculation($date);
                $amount = $response[0]->amount;
                $subscription_fee_per_license = ($amount / $day_calculation['days_in_month']) * $day_calculation['days_remaining'];
                $license_count = count($response);
                $subscription_fee = $license_count * $subscription_fee_per_license;
                $subscription_fee = round($subscription_fee, 2);
                $subscription_fee = number_format((float)$subscription_fee, 2, '.', '');
                //$company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
                $data = array('entity_type' => $entity_type, 'name' => $name, 'no_license' => $license_count, 'subscription_fee' => $subscription_fee, 'monthly_fee' => $amount, 'card_detail' => $card_detail);
                return response()->json(array('success' => 'true', 'data' => $data));
            }
        } elseif ($entity_type == 3 || $entity_type == 4) {

            $date = date("Y/m/d");
            $prorate_day_cal = new ProrateDayCalculation();
            $day_calculation = $prorate_day_cal->dayCalculation($date);
            $subscription = $this->subscription->subscriptionFeeByEntityType($entity_type, null);
            $amount = $subscription[0]->amount;
            $company_details = $this->company->getCompanyDetailsbyId($company_id);
            $entity_type = $company_details[0]->master_entity_name;
            $name = $company_details[0]->name;

            $subscription_fee = ($amount / $day_calculation['days_in_month']) * $day_calculation['days_remaining'];
            return response()->json(array('success' => 'true', 'subscription_fee' => round($subscription_fee, 2), 'entity_type' => $entity_type, 'name' => $name, 'card_detail' => $card_detail));
        }

    }

    /**
     * Get Company card details by company id
     * @param $company_id
     * @return array
     */
    public function getCardDetails($company_id)
    {
        //get card details
        $company = $this->company->getCardDetails($company_id);
        if($company != null && count($company) > 0){
            $customer_card = $this->stripe->cards()->find($company[0]->stripe_id, $company[0]->card_id);
            $data = array('CardNumber' => $customer_card['last4'], 'exp_month' => $customer_card['exp_month'], 'exp_year' => $customer_card['exp_year']);
            return $data;
        }

        return null;
    }

    /**
     * Update Company card details by company id
     * @param $company_id
     * @return array
     */
    public function updateCardDetails(Request $request)
    {
        $company_id = Auth::User()->company_id;
        $exp_month = $request->exp_month;
        $exp_year = $request->exp_year;

        $company = $this->company->getCardDetails($company_id);
        $card = $this->stripe->cards()->update($company[0]->stripe_id, $company[0]->card_id, [
            'exp_month' => $exp_month,
            'exp_year' => $exp_year,
        ]);
        $message = Config::get('messages.CARD_DETAIL_UPDATE');
        return response()->json(array('success' => 'true', 'card' => $card, 'message' => $message));
    }

    /**
     * calculation subscription fee
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubscriptionFee()
    {
        $date = date("Y/m/d");
        $entity_type = Input::get('entity_type');
        $prorate_day_cal = new ProrateDayCalculation();
        $day_calculation = $prorate_day_cal->dayCalculation($date);
        $subscription = $this->subscription->subscriptionFeeByEntityType($entity_type, null);
        $amount = $subscription[0]->amount;
//        $subscription_fee = ($amount / $day_calculation['days_in_month']) * $day_calculation['days_remaining'];
        $subscription_fee = $amount;
        return response()->json(array('subscription_fee' => round($subscription_fee, 2)));
    }

    /**
     * get company location by company ID
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyLocationById($company_id)
    {
        //get company location details
        $locations = $this->company_location->getCompanyLocationById($company_id);

        if (isset($locations[0])) {
            $message = $locations;
            return response()->json(array('success' => 'true', 'message' => $message));
        } else {
            $message = "";
            return response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * call mail sender method to send mail
     *
     * @param $email
     * @param $name
     * @param $layout
     * @param $subject
     * @param $data
     */
    public function sendMails($email, $name, $layout, $subject, $data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }

    /**
     * Display a companies for super admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyMangeViewer()
    {
        $entity = $this->entity->getPublicEntities();
        return view('company.company_manager')->with(array('page_title' => 'Company Manager', 'entities' => $entity));
    }

    /**
     * Display All company details and company status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCompany()
    {
        //declare and initialize variables
        $data = array();
        $status_txt = '';
        $companies = $this->company->getAllCompanies();
        foreach ($companies as $company) {
            switch ($company['status']) {
                case 0:
                    $status_txt = "<span class=\"badge badge-info\">In-progress</span>";
                    break;
                case 1:
                    $status_txt = "<span class=\"badge badge-warning\">Pending</span>";
                    break;
                case 2:
                    $status_txt = "<span class=\"badge badge-success\">Active</span>";
                    break;
                case 3:
                    $status_txt = "<span class=\"badge badge-danger\">Reject</span>";
                    break;
                case 4:
                    $status_txt = "<span class=\"badge badge-warning2\">Inactive</span>";
                    break;
                case 5:
                    $status_txt = "<span class=\"badge badge-danger\">Expire</span>";
                    break;
                case 6:
                    $status_txt = "<span class=\"badge badge-danger2\">Suspend</span>";
                    break;
            }
            $date = date_create($company['created_at']);
            $data[] = array(
                $company['id'],
                $company['name'],
                $company['masterEntityType'],
                date_format($date, 'm-d-Y'),
                $status_txt,
                "<a class='btn btn-sm btn-info' data-toggle='tooltip' data-target='#locationInfo' data-loc_id='" . $company['id'] . "' onclick='viewCompanyDetails({$company['id']}, 0)'>Change Status</a> <a class='btn btn-sm btn-info' data-toggle='tooltip' data-target='#locationInfo' data-loc_id='" . $company['id'] . "' onclick='viewCompanyDetails({$company['id']}, 1)'>View</a>"
            );
        }
        return response()->json(["data" => $data]);
    }

    /**
     * Company filtration according to Entity type and company name
     * @return \Illuminate\Http\JsonResponse
     */
    public function companySearchByTypeAndName()
    {
        //declare and initialize variables
        $business_name = Input::get('business_name');
        $entity_type = Input::get('entity_type');
        $status = Input::get('status');
        $data = array();
        $status_txt = '';
        $companies = $this->company->searchCompany($business_name, $entity_type, $status);

        foreach ($companies as $company) {
            switch ($company['status']) {
                case 0:
                    $status_txt = "<span class=\"badge badge-info\">In-progress</span>";
                    break;
                case 1:
                    $status_txt = "<span class=\"badge badge-warning\">Pending</span>";
                    break;
                case 2:
                    $status_txt = "<span class=\"badge badge-success\">Active</span>";
                    break;
                case 3:
                    $status_txt = "<span class=\"badge badge-danger\">Reject</span>";
                    break;
                case 4:
                    $status_txt = "<span class=\"badge badge-warning2\">Inactive</span>";
                    break;
                case 5:
                    $status_txt = "<span class=\"badge badge-danger\">Expire</span>";
                    break;
                case 6:
                    $status_txt = "<span class=\"badge badge-danger2\">Suspend</span>";
                    break;
            }
            $date = date_create($company['created_at']);

            $data[] = array(
                $company['id'],
                $company['name'],
                $company['masterEntityType'],
                date_format($date, 'm-d-Y'),
                $status_txt,
                "<a class='btn btn-info btn-sm' data-toggle='tooltip' data-target='#locationInfo' data-loc_id='" . $company['id'] . "' onclick='viewCompanyDetails({$company['id']}, 0)'>Change Status</a> <a class='btn btn-sm btn-info' data-toggle='tooltip' data-target='#locationInfo' data-loc_id='" . $company['id'] . "' onclick='viewCompanyDetails({$company['id']}, 1)'>View</a>"
            );
        }
        return response()->json(["data" => $data]);
    }

    /**
     * load thank page for registered user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function thankPageLoader()
    {
        return view('messages.thank');
    }

    public function errorPageLoader()
    {
        return view('messages.error');
    }

    /**
     * Get company detail for company profile
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyDetails($company_id)
    {
        //get all company details by company ID
        $company_details = $this->company->getCompanyDetailsbyId($company_id);

        $licenses = array();

        //add company details to $company_detail array
        $company_detail = array(
            'user_name' => Auth::User()->name,
            'user_email' => Auth::User()->email,
            'company_name' => $company_details[0]->name,
            'company_reg' => $company_details[0]->reg_no,
            //'fein_last_digits' => $company_details[0]->fein_last_digits,
            'company_status' => $company_details[0]->status,
            'company_type' => $company_details[0]->master_entity_name,
            'entity_type_id' => $company_details[0]->master_entity_id,
            'foc' => $company_details[0]->foc,
        );

        $company_location = $this->company_location->getLocationByCompanyId($company_id);
        $data = array('company_id' => $company_id, 'company_location' => $company_location, 'entity_type' => $company_details[0]->entity_type, 'company_detail' => $company_detail);
        return response()->json(['success' => 'true', "data" => $data]);
    }

    /**
     * company activate, inactivate ,delete and send mail company admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCompanyStatus()
    {
        //declare and initialize variables
        $company_id = Input::get('company_id');
        $status = Input::get('status');

        $message = '';
        $success = '';
        $user_detail = $this->user->getUserDetailByCompanyId($company_id);
        $company = $this->company->find($company_id);

        if ($status == Config::get('simplifya.SUSPEND')) {
            $layout = 'emails.cc_ge_suspend';
            $data = array('from' => 'noreply@simplifya.com', 'system' => 'Simplifya', 'company' => Config::get('messages.COMPANY_NAME'), 'businessName' => $company->name);
            $subject = 'Your Simplifya account has been suspended';
            $response = $this->company->updateCompanyByStatusAndId($status, $company_id);
            if ($response) {
                $this->sendMails($user_detail[0]->email, $user_detail[0]->name, $layout, $subject, $data);
                $message = Config::get('messages.SUSPEND_ACCOUNT');
                $success = 'true';
            }
        } elseif ($status == Config::get('simplifya.REJECT')) {
            $company_details = $this->company->find($company_id);
            $layout = 'emails.cc_ge_rejected';
            $data = array('from' => 'noreply@simplifya.com', 'system' => 'Simplifya', 'company' => Config::get('messages.COMPANY_NAME'), 'rejected_company_name' => $company_details->name);
            $subject = 'Your registration on Simplifya';

            $refund = app('App\Http\Controllers\Web\PaymentController')->returnPaymentFee($company_id);
            if ($refund['success'] == 'true') {
                $response = $this->company->updateCompanyByStatusAndId($status, $company_id);
                $success = 'true';
                $this->sendMails($user_detail[0]->email, $user_detail[0]->name, $layout, $subject, $data);
                $message = Config::get('messages.COMPANY_REJECTED');
            } else {
                $success = 'false';
                $message = $refund['message'];
            }
        } elseif ($status == Config::get('simplifya.ACTIVE')) {
            $company_details = $this->company->find($company_id);
            $layout = 'emails.cc_ge_approve';
            $data = array('from' => 'noreply@simplifya.com', 'system' => 'Simplifya', 'company' => Config::get('messages.COMPANY_NAME'), 'approved_company_name' => $company_details->name);
            $subject = 'Your registration has been approved!';
            $success = 'true';
            $response = $this->company->updateCompanyByStatusAndId($status, $company_id);
            if ($response) {
                $this->sendMails($user_detail[0]->email, $user_detail[0]->name, $layout, $subject, $data);
            }
            $message = Config::get('messages.COMPANY_APPROVE');
        } elseif ($status == Config::get('simplifya.INPROGRESS')) {
            $company_details = $this->company->find($company_id);
            if($company_details->entity_type == Config::get('simplifya.MarijuanaBusiness')) {
                $response = $this->company->updateCompanyByStatusAndId($status, $company_id);
                if ($response) {
                    $this->company_subscription_plan->disableSubscriptionsPlan($company_id);
                }
                $message = Config::get('messages.COMPANY_ACTIVATED');
            } else {
                $response = $this->company->updateCompanyByStatusAndId(2, $company_id);
                if ($response) {
                    $this->company_subscription_plan->disableSubscriptionsPlan($company_id);
                }
                $message = Config::get('messages.COMPANY_ACTIVATED');
            }
            $success = 'true';

        }
        return response()->json(array('success' => $success, 'message' => $message));
    }

    /**
     * insert payment details into payment table in database
     * @param $customer_charge
     * @param $subscription_fee
     * @param $currency
     * @param $company_id
     * @param $tx_type
     * @param bool $company_subscription_plan_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentHandler($customer_charge, $subscription_fee, $currency, $company_id, $tx_type, $company_subscription_plan_id=false,$discount=false,$coupon_id=0,$referral_commision=0,$is_referral=0)
    {
        \Log::debug("payment handler sub plan id : " . $company_subscription_plan_id);
        //get master subscription types by company ID
        $master_subscription = $this->company->getSubscriptionType($company_id);
        $payments = array(
            'req_date_time' => Carbon::now(),
            'object' => $customer_charge['charge']['object'],
            'req_currency' => $currency,
            'req_amount' => $subscription_fee,
            'res_date_time' => Carbon::createFromTimestamp($customer_charge['charge']['created']),
            'res_id' => $customer_charge['charge']['id'],
            'res_currency' => $customer_charge['charge']['currency'],
            'res_amount' => $customer_charge['charge']['amount'] / 100,
            'company_id' => $company_id,
            'tx_type' => $tx_type,
            'tx_status' => 1,
            'created_by' => '0'
        );
        /*\Log::info('================ payments???????????????????????????????????????'.print_r($payments,true));
        die();*/
        $response_payment = $this->payment->create($payments);

        if ($response_payment) {

            $company_subscription = array(
                'company_id' => $company_id,
                'master_subscription_id' => $master_subscription[0]->id,
                'payment_id' => $response_payment->id,
                'created_by' => 0,
                'amount' => $subscription_fee,
                'company_subscription_plan_id' => $company_subscription_plan_id,
                'discount'=>$discount,
                //'coupon_referral_id' => $coupon_id,
                'referral_commission' => $referral_commision
            );
            $response_subscription = $this->company_subscription->create($company_subscription);
            if ($response_subscription) {
                $message = Config::get('messages.PAYMENT_SUCCESSFUL');
                return response()->json(array('success' => 'true', 'message' => $message));
            }
        }
    }

    /**
     * export company search results
     */
    public function exportCompanySearchResults()
    {
        //declare and initialize variables
        $business_name = Input::get('business_name');
        $entity_type = Input::get('entity_type');
        $status = Input::get('status');
        $data = [];
        $status_txt = '';
        $companies = $this->company->searchCompany($business_name, $entity_type, $status);

        foreach ($companies as $company) {
            switch ($company['status']) {
                case 0:
                    $status_txt = "In-progress";
                    break;
                case 1:
                    $status_txt = "Pending";
                    break;
                case 2:
                    $status_txt = "Active";
                    break;
                case 3:
                    $status_txt = "Reject";
                    break;
                case 4:
                    $status_txt = "Inactive";
                    break;
                case 5:
                    $status_txt = "Expire";
                    break;
                case 6:
                    $status_txt = "Suspend";
                    break;
            }

            $date = date_create($company['created_at']);

            $data[] = [
                $company['id'],
                date_format($date, 'm-d-Y'),
                $company['name'],
                $company['masterEntityType'],
                $status_txt
            ];
        }

        // Define headers
        $headers = ['Company ID', 'Date', 'Campany Name', 'Entity Type', 'Status'];
        // Define file name
        $filename = "CompanyManager.csv";
        // Create CSV file
        return $this->csv->create($data, $headers, $filename);

    }

    public function activeCcGeCompany()
    {
        $company_id = Input::get('company_id');
        $response = $this->company->updateById($company_id, array('status' => 2));
        if ($response) {
            Session::put('company_status', 2);
            $message = Config::get('messages.COMPANY_ACTIVATED');
            return array(
                'success' => 'true',
                'message' => $message
            );
        }
    }


    private function mjBCompanyRegistrationWithoutSubsFee($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $simplify_email)
    {
        //declare and initialize variables
        $data = array();
        $role_id = '';

        //get user group ID
        $roles = $this->user_groupe->getGroupeId($entity_type);

        foreach ($roles as $role) {
            if ($role['name'] == 'Master Admin') {
                $role_id = $role['id'];
            }
        }

        $is_regiter = $this->company->isExistCompany($company_reg_no);
        $user_data = $this->user->isuserExist($email);
        if (!isset($is_regiter[0])) {
            if ($user_data) {
                $message = Config::get('messages.USER_ALREADY_EXISTS');
                return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
            } else {

                /*$status = false;
                // Start DB transaction
                DB::beginTransaction();*/

                try {
                    //Execute queries here ...
                    $company_data = array(
                        'name' => $name_of_business,
                        'entity_type' => $entity_type,
                        'reg_no' => $company_reg_no,
                        'status' => 0,
                        'is_first_attempt' => 0
                    );
                    $response = $this->company->create($company_data);
                    if ($response) {
                        $company_id = $response->id;
                        $user = array(
                            'name' => $your_name,
                            'email' => $email,
                            'password' => $password,
                            'company_id' => $company_id,
                            'master_user_group_id' => $role_id,
                            'status' => '1'
                        );
                        $user_response = $this->user->create($user);
                        if ($user_response) {
                            if ($entity_type == 2) {

                                $this->company->updateCompanyByStatusAndId(0, $company_id);

                                /*$entity_name = 'Marijuana Business';
                                $simplifya_name = Config::get('messages.COMPANY_NAME');
                                $admin_data = new \stdClass();
                                $admin_data->company_name = $name_of_business;
                                $admin_data->entity_name = $entity_name;
                                $admin_data->entity_type = $entity_type;
                                $admin_data->simplify_email = $simplify_email;
                                $admin_data->companyname = $simplifya_name;
                                $admin_data->layout = 'emails.mjb_registration';
                                $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                                $admin_data->registrant = $your_name;

                                event(new AdminMailRequest($admin_data));

                                $mjb_data = new \stdClass();
                                $mjb_data->name = $your_name;
                                $mjb_data->email = $email;
                                $mjb_data->companyname = $simplifya_name;
                                event(new MjbMailRequest($mjb_data));
                                // All good
                                DB::commit();*/

                                $message = Config::get('messages.MJB_COMPANY_REGISTRATION_SUCCESS');
                                Session::put('reg_message', $message);
                                return response()->json(array('success' => 'true', 'message' => $message));
                            }
                        } else {
                            DB::rollback();
                            $message = Config::get('messages.USER_ADDED_FAILED');
                            return response()->json(array('success' => 'false', 'message' => $message));
                        }
                    } else {
                        $message = Config::get('messages.COMPANY_REGISTRATION_FAILED');
                        return response()->json(array('success' => 'false', 'message' => $message));
                    }
                    $status = true;
                } catch (Exception $ex) {
                    // rollback database transaction if Something went wrong
                    DB::rollback();
                }
            }
        } else {
            $message = Config::get('messages.COMPANY_ALREADY_REGISTRATION');
            return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
        }
    }


    
    public function mjbPayment(CompanyPaymentRequest $request)
    {

        $card_no = $request->card_number;
        $exp_month = $request->exp_month;
        $ccv_number = $request->ccv_number;
        $exp_year = $request->exp_year;
        $subscrib_fee = $request->subscription_fee;
        $company_id = Auth::User()->company_id;
        $email = Auth::User()->email;

        $your_name = Auth::User()->name;

        $company_details = $this->company->getCompanyDetailsbyId($company_id);

        $name_of_business = $company_details[0]->name;
        try {
            $customer = $this->addCustomer($email, $name_of_business);
            if ($customer['success']) {
                $strip_id = $customer['customer']['id'];
                $data['stripe_id'] = $strip_id;
                if (!empty($data)) {
                    $update_comapany = $this->company->updateById($company_id, $data);

                    if ($update_comapany) {
                        $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id);
                        if ($card_detils['success']) {
                            \Log::info("stripe card");
                            $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => Auth::User()->id);
                            $this->company_card->create($data);
                            $currency = 'USD';
                            $customer_charge = $this->customerCharges($strip_id, $currency, $subscrib_fee);
                            $tx_type = 'subscription';
                            $this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type);

                            if ($customer_charge['success']) {
                                $company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
                                $entity_name = 'Marijuana Business';
                                $simplifya_name = Config::get('messages.COMPANY_NAME');
                                $simplify_email = Config::get('simplifya.admin_email');
                                $entity_type = 2;
                                $admin_data = new \stdClass();
                                $admin_data->company_name = $name_of_business;
                                $admin_data->entity_name = $entity_name;
                                $admin_data->entity_type = $entity_type;
                                $admin_data->simplify_email = $simplify_email;
                                $admin_data->companyname = $simplifya_name;
                                $admin_data->layout = 'emails.mjb_registration';
                                $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                                $admin_data->registrant = $your_name;

                                event(new AdminMailRequest($admin_data));

                                $mjb_data = new \stdClass();
                                $mjb_data->name = $your_name;
                                $mjb_data->email = $email;
                                $mjb_data->companyname = $simplifya_name;
                                event(new MjbMailRequest($mjb_data));


                                if ($company_data != null) {
                                    Session::put('company_status', 2);
                                    $message = Config::get('messages.MJB_COMPANY_REGISTRATION_SUCCESS');
                                    Session::put('reg_message', $message);
                                    return response()->json(array('success' => 'true', 'message' => $message));
                                }
                            } else {
                                DB::rollback();
                                return response()->json(array(
                                    'success' => 'false',
                                    'message' => $customer_charge['message'],
                                    'is_redirect' => 'false'
                                ));
                            }
                        } else {
                            DB::rollback();
                            return response()->json(array(
                                'success' => 'false',
                                'message' => $card_detils['message'],
                                'is_redirect' => 'false'
                            ));
                        }
                    }
                }
            } else {
                DB::rollback();
                return response()->json(array(
                    'success' => 'false',
                    'message' => $customer['message'],
                    'is_redirect' => 'false'
                ));
            }
            $status = true;
        } catch (Exception $ex) {
            // rollback database transaction if Something went wrong
            DB::rollback();
        }

    }

    /**
     *
     * create company without card details
     *
     * @param $entity_type
     * @param $name_of_business
     * @param $company_reg_no
     * @param $your_name
     * @param $email
     * @param $password
     * @param $simplify_email
     * @return \Illuminate\Http\JsonResponse
     */

    private function companyRegistrationWithoutCardDetails($entity_type, $name_of_business, $company_reg_no, $your_name, $email, $password, $simplify_email, $referral_code_id=0)
    {
        //declare variables
        $data = array();
        $role_id = '';
        $strip_id = '';
        $card_response = '';
        $entity_name = '';
        $base_url=URL::to('/');

        //get group ID
        $roles = $this->user_groupe->getGroupeId($entity_type);

        foreach ($roles as $role) {
            if ($role['name'] == 'Master Admin') {
                $role_id = $role['id'];
            }
        }

        $fein_last_digits = substr($company_reg_no, -4);
        //\Log::info('================ last 4 digit:================'.$fein_last_digits);

        //encrypt the company_reg_no
        $company_reg_no = sha1($company_reg_no);

        $MJB_FREE_SIGN_UP = $this->master_data->getMJBFOC('MJB_FREE_SIGN_UP')[0]->value;
        //\Log::info('================ last 4 digit:================'.$MJB_FREE_SIGN_UP); die;

        $is_regiter = $this->company->isExistCompany($company_reg_no);
        $user_data = $this->user->isuserExist($email);

        if (!isset($is_regiter[0])) {
            if ($user_data) {
                $message = Config::get('messages.USER_ALREADY_EXISTS');
                return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
            } else {
                //Execute queries here ...
                $company_data = array(
                    'name' => $name_of_business,
                    'entity_type' => $entity_type,
                    'reg_no' => $company_reg_no,
                    'fein_last_digits' => $fein_last_digits,
                    'status' => 0,
                    'is_first_attempt' => 0,
                    'foc' => $MJB_FREE_SIGN_UP,
                    'coupon_referral_id' => $referral_code_id
                );
                $response = $this->company->create($company_data);
                if ($response) {
                    $company_id = $response->id;
                    $user = array(
                        'name' => $your_name,
                        'email' => $email,
                        'password' => $password,
                        'company_id' => $company_id,
                        'master_user_group_id' => $role_id,
                        'status' => '1'
                    );
                    $user_response = $this->user->create($user);
                    if ($user_response) {
                            if ($entity_type == 2) {
                                $entity_name = 'Marijuana Business';
                                $this->company->updateCompanyByStatusAndId(0, $company_id);
                                $simplifya_name = Config::get('messages.COMPANY_NAME');

                                $admin_data = new \stdClass();
                                $admin_data->company_name = $name_of_business;
                                $admin_data->entity_name = $entity_name;
                                $admin_data->entity_type = $entity_type;
                                $admin_data->simplify_email = $simplify_email;
                                $admin_data->companyname = $simplifya_name;
                                $admin_data->layout = 'emails.mjb_registration';
                                $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                                $admin_data->registrant = $your_name;
                                $admin_data->registrantEmail = $email;

                                event(new AdminMailRequest($admin_data));

                                //send mjb-sign-up-support-email (company name, your_name, email)
                                $mjb_support_data = new \stdClass();
                                $mjb_support_data->name = $your_name;
                                $mjb_support_data->email = $email;
                                $mjb_support_data->businessName = $name_of_business;
                                $mjb_support_data->reg_no = $company_reg_no;
                                $mjb_support_data->companyname = $simplifya_name;
                                $mjb_support_data->layout = 'emails.mjb_singup_support';
                                $mjb_support_data->subject = 'NEW REGISTRATION - '. $name_of_business;

                                // Fire mjb sign up support event
                                event(new MjbSignUpSupport( $mjb_support_data ));
                                $mjb_data = new \stdClass();
                                $mjb_data->name = $your_name;
                                $mjb_data->email = $email;
                                $mjb_data->companyname = $simplifya_name;
                                event(new MjbMailRequest($mjb_data));

                                $message = Config::get('messages.MJB_COMPANY_REGISTRATION_SUCCESS');
                                $login_button='<a href="'.$base_url.'" class="btn btn-lg btn-success">Log In</a>';
                                Session::put('reg_message', $message);
                                Session::put('reg_button', $login_button);
                                return response()->json(array('success' => 'true', 'message' => $message));
                            } elseif ($entity_type == 3 || $entity_type == 4) {
                                if ($entity_type == 3) {
                                    $entity_name = 'Compliance Company';
                                    $subject = 'Action required – New CC registration';
                                } else {
                                    $entity_name = 'Government Entity';
                                    $subject = 'Action required – New GE registration';
                                }

                                $this->company->updateCompanyByStatusAndId(1, $company_id);
                                $simplifya_name = Config::get('messages.COMPANY_NAME');

                                $admin_data = new \stdClass();
                                $admin_data->company_name = $name_of_business;
                                $admin_data->entity_name = $entity_name;
                                $admin_data->entity_type = $entity_type;
                                $admin_data->simplify_email = $simplify_email;
                                $admin_data->companyname = $simplifya_name;
                                $admin_data->layout = 'emails.cc_ge_registration';
                                $admin_data->subject = $subject;
                                $admin_data->registrant = $your_name;
                                event(new AdminMailRequest($admin_data));

                                $cc_ge_data = new \stdClass();
                                $cc_ge_data->name = $your_name;
                                $cc_ge_data->email = $email;
                                $cc_ge_data->companyname = $simplifya_name;
                                event(new CcGeMailRequest($cc_ge_data));


                                $message = Config::get('messages.CC_GE_COMPANY_REGISTRATION_SUCCESS');
                                $login_button= '<a href="http://simplifya.com/" class="btn btn-lg btn-success">HOME</a>';
                                Session::put('reg_message', $message);
                                Session::put('reg_button', $login_button);

                                return response()->json(array(
                                    'success' => 'true',
                                    'message' => $message
                                ));

                            }
                    } else {
                        $message = Config::get('messages.USER_ADDED_FAILED');
                        return response()->json(array('success' => 'false', 'message' => $message));
                    }
                } else {
                    $message = Config::get('messages.COMPANY_REGISTRATION_FAILED');
                    return response()->json(array('success' => 'false', 'message' => $message));
                }
            }
        } else {
            $message = Config::get('messages.COMPANY_ALREADY_REGISTRATION');
            return response()->json(array('success' => 'false', 'message' => $message, 'is_redirect' => 'false'));
        }
    }


    /**
     *
     * company card details add to the system after sign up
     *
     * @param CompanyPaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commonCompanyPayment(CompanyPaymentRequest $request)
    {

        $requests = $request->all();
        $foc = $request->foc;
        $card_no = $request->card_number;
        $exp_month = $request->exp_month;
        $ccv_number = $request->ccv_number;
        $exp_year = $request->exp_year;
        $subscrib_fee = $request->subscription_fee;
        $subscribe_plan = $request->subscription_plan;
        $company_id = Auth::User()->company_id;
        $email = Auth::User()->email;
        $entity_type = $request->entity_type;
        $no_of_license = $request->no_of_license;

        $total_amount=$this->getSubscriptionPlanFee($company_id,$subscribe_plan);



        if(isset($request->coupon_id) && $request->coupon_id!=''){
            $coupon_status = true;

            if($request->is_referral == 0)
            {
                $coupon_check= \Validator::make($requests,[
                    'coupon_id' =>'unique:company_subscription_plans'
                ]);
                $coupon_status = $coupon_check->passes();
            }
            if($coupon_status==true){
                $coupon_id=$request->coupon_id;
                //$coupon_discount=$this->coupon->getDiscountAmount($subscribe_plan,$total_amount['subscription_fee'],$coupon_id,$request->is_referral);
                $coupon_discount=$this->coupon->getDiscountAmount($subscribe_plan,$total_amount['subscription_fee'],$coupon_id,$request->is_referral, $no_of_license);
                $coupon_amount=$coupon_discount['discount'];
            }else{
                $coupon='Coupon is already in use.';
                return response()->json( array('success' => 'false', 'message' => $coupon));
            }
        }else{
            $coupon_id=0;
            $coupon_amount=0;

        }

        /*\Log::debug("===== plans " . print_r($coupon_amount, true));
        die();*/

        $your_name = Auth::User()->name;

        $company_details = $this->company->getCompanyDetailsbyId($company_id);

        $name_of_business = $company_details[0]->name;
        DB::beginTransaction();
        try {
            if($company_details[0]->stripe_id != '' || $company_details[0]->stripe_id != null) {
                $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $company_details[0]->stripe_id);
                if ($card_detils['success']) {
                    $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => Auth::User()->id);
                    $this->company_card->create($data);

                    $update_company =$this->company_card->updateCompanyCard($company_id,$card_detils['card']['id']);
                    DB::commit();
                    $message = Config::get('messages.CARD_DETAIL_UPDATE');
                    return response()->json(array('success' => 'true', 'message' => $message));
                }
            } else {
                $customer = $this->addCustomer($email, $name_of_business);
                if ($customer['success']) {
                    $strip_id = $customer['customer']['id'];
                    $data['stripe_id'] = $strip_id;
                    if (!empty($data)) {
                        $update_comapany = $this->company->updateById($company_id, $data);

                        if ($update_comapany && $foc == 0) {
                            $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id);
                            if ($card_detils['success']) {
                                // Set this card as default
                                $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => Auth::User()->id);
                                $this->company_card->create($data);

                                $this->company_card->updateCompanyCard($company_id, $card_detils['card']['id']);

                                if($request->is_referral == 1)
                                {
                                    $subscrib_fee = $subscrib_fee - $coupon_amount;
                                }

                                if($subscrib_fee==0){
                                    $subscription=$this->addCompanyCardAndSubscriptionWithoutSubFee($subscribe_plan,$entity_type,$company_id,$name_of_business,$your_name,$email,$coupon_id,$coupon_amount);
                                    if($subscription){
                                        return response()->json($subscription);
                                    }

                                }else{
                                    if($entity_type == 2){
                                        $currency = 'USD';
                                        \Log::debug("===== subscrib_fee " . print_r($subscrib_fee, true));
                                        $customer_charge = "";

                                        $customer_charge = $this->customerCharges($strip_id, $currency, $subscrib_fee  );

                                        //\Log::debug("===== customer_charge " . print_r($customer_charge, true));
                                        //die();

                                        $tx_type = 'subscription';
                                        $referral_commision = 0;

                                        if(!empty($coupon_id))
                                        {
                                            $referral_commision = $this->referral->getReferralCommission($coupon_id,$subscribe_plan,$subscrib_fee);
                                        }

                                        if (isset($subscribe_plan)) {
                                            $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($subscribe_plan, $company_id,false,false,$coupon_id,$request->is_referral);
                                            //die();
                                            // save company subscription plan
                                            $subscribe_planObj = $this->company_subscription_plan->create($plansParam);
                                            //\Log::debug("===== sub plan id " . $subscribe_planObj->id);
                                            $this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type, $subscribe_planObj->id,$coupon_amount,$coupon_id,$referral_commision,$request->is_referral);

                                            $coupon_details = $this->coupon->getCouponDetails($coupon_id);
                                            if(isset($coupon_details->commission_period))
                                            {
                                                $setCommissionEndDate = $this->company->setCommissionEndDate($coupon_details->commission_period);
                                            }


                                            //\Log::debug("===== coupon_details " . print_r($coupon_details, true));

                                        }else {
                                            \Log::debug("===== subscription plan undefined");
                                            $this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type,false,$coupon_amount);
                                        }

                                    }
                                    if($entity_type == 2){
                                        if ($customer_charge['success']) {
                                            $company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
                                            $entity_name = 'Marijuana Business';
                                            $simplifya_name = Config::get('messages.COMPANY_NAME');
                                            $simplify_email = Config::get('simplifya.admin_email');
                                            $admin_data = new \stdClass();
                                            $admin_data->company_name = $name_of_business;
                                            $admin_data->entity_name = $entity_name;
                                            $admin_data->entity_type = $entity_type;
                                            $admin_data->simplify_email = $simplify_email;
                                            $admin_data->companyname = $simplifya_name;
                                            $admin_data->layout = 'emails.mjb_registration';
                                            $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                                            $admin_data->registrant = $your_name;

                                            event(new AdminMailRequest($admin_data));

                                            $mjb_data = new \stdClass();
                                            $mjb_data->name = $your_name;
                                            $mjb_data->email = $email;
                                            $mjb_data->companyname = $simplifya_name;
                                            event(new MjbMailRequest($mjb_data));

                                            DB::commit();

                                            if ($company_data != null) {
                                                Session::put('company_status', 2);
                                                $message = Config::get('messages.COMPANY_REGISTRATION_SUCCESS');
                                                Session::put('reg_message', $message);
                                                return response()->json(array('success' => 'true', 'message' => $message));
                                            }
                                        } else {
                                            DB::rollback();
                                            return response()->json(array(
                                                'success' => 'false',
                                                'message' => $customer_charge['message'],
                                                'is_redirect' => 'false'
                                            ));
                                        }
                                    }elseif ($entity_type == 3 || $entity_type == 4) {
                                        DB::commit();
                                        $message = Config::get('messages.CARD_DETAIL_UPDATE');
                                        Session::put('reg_message', $message);
                                        return response()->json(array(
                                            'success' => 'true',
                                            'message' => $message
                                        ));

                                    }
                                }

                            } else {
                                DB::rollback();
                                return response()->json(array(
                                    'success' => 'false',
                                    'message' => $card_detils['message'],
                                    'is_redirect' => 'false'
                                ));
                            }
                        }
                        if($foc == 1)
                        {
                            $subscription=$this->addCompanyCardAndSubscriptionWithoutSubFee($subscribe_plan,$entity_type,$company_id,$name_of_business,$your_name,$email,$coupon_id,$coupon_amount);
                            if($subscription){
                                return response()->json($subscription);
                            }
                        }
                    }
                } else {
                    DB::rollback();
                    return response()->json(array(
                        'success' => 'false',
                        'message' => $customer['message'],
                        'is_redirect' => 'false'
                    ));
                }
                $status = true;
            }
        } catch (Exception $ex) {
            // rollback database transaction if Something went wrong
            DB::rollback();
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

    public function addCompanyCardAndSubscriptionWithoutSubFee($subscribe_plan,$entity_type,$company_id,$name_of_business,$your_name,$email,$coupon_id,$discount){
        if($entity_type == 2){

            $currency = 'USD';
            $tx_type = 'subscription';

            if (isset($subscribe_plan)) {
                $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($subscribe_plan, $company_id,false,false,$coupon_id);

                // save company subscription plan
                $subscribe_planObj = $this->company_subscription_plan->create($plansParam);
                // \Log::debug("===== sub plan id " . $subscribe_planObj->id);
                //$this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type, $subscribe_planObj->id);
                $master_subscription = $this->company->getSubscriptionType($company_id);

                $company_subscription = array(
                    'company_id' => $company_id,
                    'master_subscription_id' => $master_subscription[0]->id,
                    'payment_id' => 0,
                    'created_by' => 0,
                    'amount' => 0,
                    'discount' => $discount,
                    'company_subscription_plan_id' => $subscribe_planObj->id,
                    'discount' => $discount
                );
                $response_subscription = $this->company_subscription->create($company_subscription);
            }
            $company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
            $entity_name = 'Marijuana Business';
            $simplifya_name = Config::get('messages.COMPANY_NAME');
            $simplify_email = Config::get('simplifya.admin_email');
            $admin_data = new \stdClass();
            $admin_data->company_name = $name_of_business;
            $admin_data->entity_name = $entity_name;
            $admin_data->entity_type = $entity_type;
            $admin_data->simplify_email = $simplify_email;
            $admin_data->companyname = $simplifya_name;
            $admin_data->layout = 'emails.mjb_registration';
            $admin_data->subject = 'New MJB registration - ' . $name_of_business;
            $admin_data->registrant = $your_name;

            event(new AdminMailRequest($admin_data));

            $mjb_data = new \stdClass();
            $mjb_data->name = $your_name;
            $mjb_data->email = $email;
            $mjb_data->companyname = $simplifya_name;
            event(new MjbMailRequest($mjb_data));

            DB::commit();

            if ($company_data != null) {

                Session::put('company_status', 2);
                $message = Config::get('messages.COMPANY_REGISTRATION_SUCCESS');
                Session::put('reg_message', $message);
                return array('success' => 'true', 'message' => $message);
            }

        } elseif ($entity_type == 3 || $entity_type == 4) {
            DB::commit();
            $message = Config::get('messages.CARD_DETAIL_UPDATE');
            Session::put('reg_message', $message);
            return array(
                'success' => 'true',
                'message' => $message
            );

        }
    }
    /**
     *
     * company does not added card details to the system
     *
     * @param CompanyPaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCompanyPaymentCard(CompanyPaymentRequest $request){
        $company_id         = Auth::User()->company_id;
        $email              = Auth::User()->email;

        $card_no            = $request->card_number;
        $exp_month          = $request->exp_month;
        $ccv_number         = $request->ccv_number;
        $exp_year           = $request->exp_year;

        $card_existed = $this->company_card->isCompanyCardAdded($company_id);

        \Log::debug("=== card existed " . print_r($card_existed, true));

        if(isset($card_existed[0])){
            $company    = $this->company->getCompanyById($company_id);
            $stripe_id = $company[0]->stripe_id;

            $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $stripe_id);
            if ($card_detils['success']) {
                $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => Auth::User()->id);
                $this->company_card->create($data);

                $update_company =$this->company_card->updateCompanyCard($company_id,$card_detils['card']['id']);
                $message = Config::get('messages.CARD_DETAIL_UPDATE');
                return response()->json(array('success' => 'true', 'message' => $message));
            }

        }else{
            $company_details = $this->company->getCompanyDetailsbyId($company_id);

            $name_of_business = $company_details[0]->name;

            $customer = $this->addCustomer($email, $name_of_business);

            if ($customer['success']) {
                $strip_id = $customer['customer']['id'];
                $data['stripe_id'] = $strip_id;
                if (!empty($data)) {
                    $update_comapany = $this->company->updateById($company_id, $data);
                    if ($update_comapany) {
                        $card_detils = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id);
                        if ($card_detils['success']) {
                            $data = array('company_id' => $company_id, 'card_id' => $card_detils['card']['id'], 'created_by' => Auth::User()->id);
                            $this->company_card->create($data);
                            $update_company =$this->company_card->updateCompanyCard($company_id,$card_detils['card']['id']);
                            return response()->json(array('success' => 'true'));
                        }
                    }
                }
            }
        }


    }



    public function subscriptionPlans(){
        $entity_type = $_GET['entity_type'];
        $subscriptionPlan = $this->company->getSubscriptionPackage($entity_type);

        $company_id  = Auth::User()->company_id;
        $company_details = $this->company->find($company_id);
        \Log::info(print_r($subscriptionPlan,true));
        return response()->json(array('success' => 'true','data' => $subscriptionPlan,'foc' => $company_details->foc), 200);

    }

    public function validateReferral(Request $request)
    {
        $res = $this->referral->validateCoupon($request->referral_code,$request->subscription_plan);
        return json_encode($res);
    }

    public function validateCoupon(Request $request)
    {
        $res = $this->coupon->validateCoupon($request->coupon_code,$request->subscription_plan);
        return json_encode($res);
    }

    public function getDiscount(Request $request)
    {
        $subscription_plan = $request->subscription_plan;
        $no_of_license = $request->no_of_license;
        $order = isset($request->order) ? $request->order : 1;
        $coupon_details = $this->coupon->getDiscount($request->coupon_code,$subscription_plan);
        //\Log::debug("==== .......no_of_license".print_r($no_of_license,true));
        $sub_fee = $request->sub_fee;
        $res = $this->coupon->getDiscountByOrder($coupon_details, $sub_fee,$order,$no_of_license);

        return json_encode($res);
    }


    public function selectDefaultCard(Request $request)
    {

        $company_id = Auth::User()->company_id;
        $card       = $request->card_id;
        $status     = $request->status;
        $company    = $this->company->getCompanyById($company_id);

        $stripe_id = $company[0]->stripe_id;
        $customer = $this->stripe->customers()->update($stripe_id,[
            'default_card' => $card,
        ]);
        if(isset($customer)){
            $update_company =$this->company_card->updateCompanyCard($company_id,$card);
            if(isset($update_company)){
                $message = Config::get('messages.CARD_DETAIL_UPDATE');
                return response()->json(array('success' => 'true', 'message' => $message));
            }
        }


    }


    public function getAddedCardDetails($company_id)
    {
        $company  = $this->company->getCompanyDetailsbyId($company_id);;
        \Log::debug("==== get company card details from stripe");
        $card_list = $this->stripe->cards()->all($company[0]->stripe_id);
        $i=0;
        foreach($card_list['data'] as $card) {
            $card_id = $card['id'];
            $data[$i] = array(
                "XXXX-XXXX-XXXX-".$card['last4'],
                $card['exp_month'],
                $card['exp_year'],
                $card['brand'],
                ($i == 0)?
                "
                    <a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#cardEdit' title='Edit' data-card_id='{$card_id}' onclick='editCardDetails(".'"'.$company_id.'"'.")'><i class='fa fa-paste'></i></a>
                "
                    :
                "
                    <a class='btn btn-info btn-circle' data-toggle='tooltip' title='Default Card' onclick='setDefaultCard("."\"{$card_id}\",\"{$company_id}\"" .")'><i class='fa fa-thumbs-o-up'></i></a>
                "
            );
            $i++;
        }
        return response()->json(array('data' => $data), 200);
    }


    public function getSubscriptionFeeForPlan()
    {
        //declare and initialize variables
        $company_id = Input::get('company_id');
        $package_id = Input::get('package_id');
        $entity_type = Input::get('entity_type');
        $company_details = $this->company->getCompanyDetailsbyId($company_id);
        $card_detail = '';
        if ($entity_type != 2) {
            $card_detail = $this->getCardDetails($company_id);
        } elseif ($company_details[0]->status != 0 && $entity_type == 2) {
            $card_detail = $this->getCardDetails($company_id);
        }

        if ($entity_type == 2) {
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
                $data = array('entity_type' => $entity_type, 'name' => $name, 'no_license' => $license_count, 'subscription_fee' => $subscription_fee, 'monthly_fee' => $amount, 'card_detail' => $card_detail);
                return response()->json(array('success' => 'true', 'data' => $data));
            }
        } elseif ($entity_type == 3 || $entity_type == 4) {

            $date = date("Y/m/d");
            $prorate_day_cal = new ProrateDayCalculation();
            $day_calculation = $prorate_day_cal->dayCalculation($date);
            $subscription = $this->subscription->subscriptionFeeByEntityType($entity_type, null);
            $amount = $subscription[0]->amount;
            $company_details = $this->company->getCompanyDetailsbyId($company_id);
            $entity_type = $company_details[0]->master_entity_name;
            $name = $company_details[0]->name;

            $subscription_fee = ($amount / $day_calculation['days_in_month']) * $day_calculation['days_remaining'];
            return response()->json(array('success' => 'true', 'subscription_fee' => round($subscription_fee, 2), 'entity_type' => $entity_type, 'name' => $name, 'card_detail' => $card_detail));
        }

    }


    /**
     *
     * setup subscription plan to company
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function companySubscriptionPlan(Request $request)
    {

        $company_id = Auth::User()->company_id;
        $master_subscription_id = $request->subscription_plan;
        $startDate  = date("Y/m/d");

        $dueDate    = $this->company_subscription_plan->getNextDate($startDate,1);

        $subscription_plan  = $this->subscription->getSubscriptionFee($master_subscription_id);

        $subscription_fee   = $subscription_plan->amount;
        $subscription_month = $subscription_plan->validity_period_id;
        $endDate            = $this->company_subscription_plan->getNextDate($startDate,$subscription_month);

        $data = array('company_id' => $company_id, 'master_subscription_id' => $master_subscription_id,
         'subscription_fee'=> $subscription_fee, 'start_date'=> $startDate, 'end_date'=> $endDate,'due_date'=> $dueDate, 'active' => 1);

        $this->company_subscription_plan->create($data);

        return response()->json(array('success'=> true));

    }

    /**
     * get next date passing to number of month
     *
     * @param $toDate
     * @param $month
     * @return bool|string
     * @internal param $date
     */
    public function getNextDate($toDate, $month, $withPreviousDay=false)
    {
        $time = strtotime($toDate);
        $nextDate = date("Y-m-d", strtotime("+{$month} month", $time));
        if ($withPreviousDay)  {
            $previousDayDatetime = strtotime($nextDate);
            $previousDate = date("Y-m-d", strtotime("-1 day", $previousDayDatetime));;
            return $previousDate;
        }
        return $nextDate;
    }

    public function currentPlan() {
        return view('subscription.plan')->with('page_title', 'Subscription Plan');
    }

    /**
     *
     * Get subscription plan drop down list
     * by current logged in user's entity type
     */
    public function getSubscriptionPlansByCurrentEntity() {

        $user = Auth::User();
        $company = $this->company->find($user->company_id);
        $plans = $this->subscription->getSubscriptionFeeByEntity($company->entity_type);
        if (isset($plans) && $plans->count()) {

            // Make more readable plan
            $plansArray = array_map(function ($plan){
                return [
                    'id' => "" . $plan['id'],
                    'name' => $plan['name'] . ' ' . '($' . number_format($plan['amount'], 2, '.', '') . ' per license)',
                    'amount' => number_format($plan['amount'], 2, '.', '')
                ];
            }, $plans->toArray());

            // Get current plan
            $currentPlan = $this->company_subscription_plan->getCurrentActivePlanByCompany($user->company_id);
            $remainingMonthChargeFee = 0;

            $nextPlan = array();
            if (isset($currentPlan)) {
                $nextPlanObj = $this->company_subscription_plan
                    ->getNextSubscriptionPlan($user->company_id, $currentPlan->due_date, $currentPlan->current_subscription_plan_id);
                $nextPlan = $nextPlanObj;
                if ($currentPlan->validity_period_id != 1) {
                    $remainingMonthChargeFee = $this->calculateCancelFeeForSubscriptionPlan($currentPlan->current_subscription_plan_id, $user->company_id);
                }
            }
            $hideCancelPlan = false;
            if ($company->foc == 1) {
                $hideCancelPlan = true;
            }
            // Prepare response data
            $data = [
                'plans' => $plansArray,
                'current_plan' => $currentPlan,
                'next_plan' => $nextPlan,
                'cancel_fee' => $remainingMonthChargeFee,
                'hideCancel' => $hideCancelPlan
            ];

            return response()->json(array('success' => 'true','data' => $data),200);
        }else {
            return response()->json(array('success' => 'false','message'=>'No subscription plans'),405);
        }

    }

    /**
     * Update company subscription plan
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSubscriptionPlan(Request $request) {

        $planId = $request->plan_id;
        $currentPlanId = $request->current_subscription_plan_id;
        $nextPlanId = $request->next_subscription_plan_id;
        $coupon_code = $request->coupon_code;
        $user = Auth::User();

        \Log::debug("==== coupon_code= " . $coupon_code);
        \Log::debug("==== current plan id " . $currentPlanId);
        \Log::debug("==== next plan id " . $nextPlanId);

        if (isset($currentPlanId) && $currentPlanId != '') {
            $new_plan = $this->subscription->find($planId);
            $currentPlan = $this->company_subscription_plan->findSubscriptionPlanWithMasterSubscription($currentPlanId);
            $coupon = $this->coupon->getCouponId($coupon_code);
            $coupon_id = isset($coupon->coupon_id) ? $coupon->coupon_id : '';

            //\Log::info("==== ..........cid: " . print_r($coupon->coupon_id,true));
            \Log::info("==== current plan validity period: " . $currentPlan->validity_period_id);

            if ($new_plan->validity_period_id <= $currentPlan->validity_period_id) {
                \Log::info("==== needs to change start date!");
                $endDate = $this->company_subscription_plan->getNextDate($currentPlan->start_date, $currentPlan->validity_period_id, true);

                $this->company_subscription_plan->update(['end_date' => $endDate], $currentPlan->id);
                $currentPlan = $this->company_subscription_plan->findSubscriptionPlanWithMasterSubscription($currentPlanId);
                $time = strtotime($currentPlan->end_date);
                $startDate = date("Y-m-d", strtotime("+1 day", $time));
                \Log::debug("==== start date " . $startDate);
                $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($planId, $user->company_id, $startDate, true);
            }else {
                $time = strtotime($currentPlan->due_date);
                $endDate = date("Y-m-d", strtotime("-1 day", $time));
                \Log::info("==== update end date of the current plan to " . $endDate);
                //todo update current plan end date!
                $this->company_subscription_plan->update(['end_date' => $endDate], $currentPlan->id);
                $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($planId, $user->company_id, $currentPlan->due_date, true);
            }

            \Log::debug("params : " . print_r($plansParam, true));
            \Log::debug("current plan : " . $currentPlan->validity_period_id);
            \Log::debug("new plan : " . $new_plan->validity_period_id);

            DB::beginTransaction();

            try {
                $plansParam['coupon_id'] = $coupon_id;
                if (isset($currentPlan->coupon_referral_id)) {
                    $plansParam['coupon_referral_id'] = $currentPlan->coupon_referral_id;
                }

                \Log::debug("params : ................" . print_r($plansParam, true));
                if (isset($nextPlanId) && $nextPlanId != '') {
                    // update next plan raw
                    \Log::debug("update next plan raw" );
//                    $this->company_subscription_plan->update($plansParam, $nextPlanId);
                    $this->company_subscription_plan->delete($nextPlanId);
                    $this->company_subscription_plan->create($plansParam);
                }else {
                    // add new plan raw
                    \Log::debug("add new next plan raw" );
                    $this->company_subscription_plan->create($plansParam);
                }
                DB::commit();
                return response()->json(array('success' => 'true','message' => 'Subscription plan has been successfully updated'), 200);
            }catch (Exception $e) {
                DB::rollback();
                return response()->json(array('success' => 'false','message'=>'Unable to update the subscription'), 405);
            }

        }else {
            \Log::debug("when no subscription plan exists!");
            // When old user loged in tries to update their subscription plan
            // todo needs confirmation in-order charge as first time
            DB::beginTransaction();
            try {
                $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($planId, $user->company_id);
                $this->company_subscription_plan->create($plansParam);
                DB::commit();
                return response()->json(array('success' => 'true','message' => 'Subscription plan has been successfully updated'), 200);
            }catch (Exception $e) {
                DB::rollback();
                return response()->json(array('success' => 'false','message'=>'Unable to update the subscription'), 405);
            }
        }

    }

    public function cancelSubscriptionPlan(Request $request) {

        $user = Auth::User();

        DB::beginTransaction();

        try {
            $currentPlan = $this->company_subscription_plan->getCurrentActiveSubscriptionPlanByDate($user->company_id);
            if (!isset($currentPlan)) {
                \Log::debug("==== current plan " . print_r($currentPlan->toArray(), true));
                return response()->json(array('success' => 'false','message'=> 'Unable to find current subscription'), 405);
            }
            if ($currentPlan->validity_period_id != 1) {
                // charge cancel fee
                $remainingMonthChargeFee = $this->calculateCancelFeeForSubscriptionPlan($currentPlan->id, $user->company_id);
                \Log::debug("=== charge fee " . $remainingMonthChargeFee);
                if ($remainingMonthChargeFee != 0) {
                    $company = $this->company->find($user->company_id);
                    $currency = 'USD';
                    $customer_charge = $this->customerCharges($company->stripe_id, $currency, $remainingMonthChargeFee);
                    if ($customer_charge['success']) {
                        $tx_type = 'subscription_cancel';
                        $this->paymentHandler($customer_charge, $remainingMonthChargeFee, $currency, $user->company_id, $tx_type);
                    }else {
                        DB::rollback();
                        \Log::debug("==== unable to un-subscribe charge failed");
                        $message = 'Unable to un-subscribe';
                        if (isset($customer_charge['message'])) {
                            $message = $customer_charge['message'];
                        }
                        return response()->json(array('success' => 'false','message'=> $message), 405);
                    }

                }else {
                    \Log::debug("==== un-subscribe with zero amount");
                    //return response()->json(array('success' => 'false','message'=>'Unable to un-subscribe'), 405);
                }
            }

            //disable plans
            $this->company_subscription_plan->disableAllActiveSubscriptions($user->company_id);
            //change status of the company user
            $this->company->updateCompanyByStatusAndId(Config::get('simplifya.EXPIRE'), $user->company_id);

            $email_data = array(
                'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                'system' => 'Simplifya',
                'company' => 'Simplifya'
            );
            $subject = 'Simplifya Un-subscribed';
            $layout = 'emails.account_cancel';
            $this->sendMails($user->email, $user->name,$layout,$subject,$email_data);

            DB::commit();
            return response()->json(array('success' => 'false','message'=>'You have successfully un-subscribed from all plans.'), 200);

        }catch (Exception $e) {
            DB::rollback();
            return response()->json(array('success' => 'false','message'=>'Unable to un-subscribe'), 405);
        }

    }

    /**
     * Returns cancellation fee based on current subscription plan
     */
    private function calculateCancelFeeForSubscriptionPlan($currentPlanId, $companyId) {
        // fetch all licenses including inactive once
        $licenseCount = $this->company_location_license->getActiveInactiveLicenseCountByCompanyId($companyId);
        // fetch how many months got charged out of plan validity period
        $chargeSummary = $this->company_subscription_plan->getChargeCountFromSubscriptionPlan($currentPlanId);
        \Log::debug("=== license count = " . $licenseCount);
        \Log::debug("=== charge summary  " . print_r($chargeSummary->toArray(), true));
        // take a charge for remaining amount x no of license
        $remainingMonths = $chargeSummary->validity_period_id - $chargeSummary->total_charge_count;
        if ($remainingMonths != 0 && $licenseCount != 0) {
            \Log::debug("=== remaining months " . $remainingMonths);
            \Log::debug("=== subscription fee " . $chargeSummary->subscription_fee);
            \Log::debug("=== calculation : ({$chargeSummary->subscription_fee} * {$licenseCount}) * $remainingMonths");
            $allLicenseFee = $chargeSummary->subscription_fee * $licenseCount;
            $remainingMonthsCharge = $allLicenseFee * $remainingMonths;
            return $remainingMonthsCharge;
        }

        return 0; // mean not a valid charge
    }

    /**
     * Change company foc param
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCompanyFoc(Request $request) {
        \Log::debug("param foc " . $request->foc);
        \Log::debug("param foc " . $request->company_id);

        if (isset($request->foc) && isset($request->company_id)) {
            $update_foc = $this->company->update(['foc' => $request->foc], $request->company_id);
            return response()->json(array('success' => 'true','message'=>'You have successfully changed foc status of the company'), 200);

        }else {
            return response()->json(array('success' => 'false','message'=>'Unable to update FOC'), 405);
        }

    }

    /**
     * Add company default card
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDefaultCard(Request $request)
    {
        $company_id         = Auth::User()->company_id;
        $email              = Auth::User()->email;

        $card_no            = $request->card_number;
        $exp_month          = $request->exp_month;
        $ccv_number         = $request->ccv_number;
        $exp_year           = $request->exp_year;
        $entity_type        = $request->entity_type;
        $subscrib_fee       = $request->subscription_fee;

        $company_details = $this->company->getCompanyDetailsbyId($company_id);
        $your_name = Auth::User()->name;
        $name_of_business = $company_details[0]->name;

        $strip_id = $company_details[0]->stripe_id;
        $card_details = $this->cardRegistration($card_no, $exp_month, $ccv_number, $exp_year, $strip_id);

        DB::beginTransaction();

        if ($card_details['success']) {
            // Set this card as default
            $data = array('company_id' => $company_id, 'card_id' => $card_details['card']['id'], 'created_by' => Auth::User()->id);
            $this->company_card->create($data);
            $this->company_card->updateCompanyCard($company_id, $card_details['card']['id']);

            if($entity_type == 2){
                $currency = 'USD';
                $customer_charge = $this->customerCharges($strip_id, $currency, $subscrib_fee);
                $tx_type = 'subscription';

                if (isset($subscribe_plan)) {
                    $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($subscribe_plan, $company_id);
                    \Log::debug("===== plans " . print_r($plansParam, true));
                    // save company subscription plan
                    $subscribe_planObj = $this->company_subscription_plan->create($plansParam);
                    \Log::debug("===== sub plan id " . $subscribe_planObj->id);
                    $this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type, $subscribe_planObj->id);
                }else {
                    \Log::debug("===== subscription plan undefined");
                    $this->paymentHandler($customer_charge, $subscrib_fee, $currency, $company_id, $tx_type);
                }

            }
            if($entity_type == 2){
                if ($customer_charge['success']) {
                    $company_data = $this->company->updateCompanyByStatusAndId(2, $company_id);
                    $entity_name = 'Marijuana Business';
                    $simplifya_name = Config::get('messages.COMPANY_NAME');
                    $simplify_email = Config::get('simplifya.admin_email');
                    $admin_data = new \stdClass();
                    $admin_data->company_name = $name_of_business;
                    $admin_data->entity_name = $entity_name;
                    $admin_data->entity_type = $entity_type;
                    $admin_data->simplify_email = $simplify_email;
                    $admin_data->companyname = $simplifya_name;
                    $admin_data->layout = 'emails.mjb_registration';
                    $admin_data->subject = 'New MJB registration - ' . $name_of_business;
                    $admin_data->registrant = $your_name;

                    event(new AdminMailRequest($admin_data));

                    $mjb_data = new \stdClass();
                    $mjb_data->name = $your_name;
                    $mjb_data->email = $email;
                    $mjb_data->companyname = $simplifya_name;
                    event(new MjbMailRequest($mjb_data));

                    DB::commit();

                    if ($company_data != null) {
                        Session::put('company_status', 2);
                        $message = Config::get('messages.COMPANY_REGISTRATION_SUCCESS');
                        Session::put('reg_message', $message);
                        return response()->json(array('success' => 'true', 'message' => $message));
                    }
                } else {
                    DB::rollback();
                    return response()->json(array(
                        'success' => 'false',
                        'message' => $customer_charge['message'],
                        'is_redirect' => 'false'
                    ));
                }
            }elseif ($entity_type == 3 || $entity_type == 4) {
                DB::commit();
                $message = Config::get('messages.CARD_DETAIL_UPDATE');
                Session::put('reg_message', $message);
                return response()->json(array(
                    'success' => 'true',
                    'message' => $message
                ));

            }
        } else {
            DB::rollback();
            return response()->json(array(
                'success' => 'false',
                'message' => $card_details['message'],
                'is_redirect' => 'false'
            ));
        }

    }

    public function saveNonMjb(Request $request){
            $companyData = $request->all();
        if (!isset($companyData['company_id'])) {
            $companyData['company_id']=0;
        }
        if (!isset($companyData['company_location_id'])) {
            $companyData['company_location_id']=0;
        }

        try {
            $company_id=$this->company->createNonMjb($companyData,Auth::User()->id);
            return response()->json(array('success' => 'true', 'message' => 'Ok','company_id'=>$company_id));
        }catch (\Exception $e) {
            \Log::debug("error saving Non mjb");
            \Log::debug($e->getTraceAsString());
            \Log::debug($e->getMessage());
            $exceptionCode = $e->getCode();
            $message = 'Non MJB creation failed.';
            if ($exceptionCode == 10) {
                $message = $e->getMessage();
            }
            return response()->json(array('success' => 'false', 'message' => $message));
        }


    }

}
