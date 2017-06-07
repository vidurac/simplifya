<?php

namespace App\Http\Controllers\Web;


use App\Repositories\AppointmentClassificationRepository;
use App\Repositories\CompanyLocationLicenseRepository;
use App\Repositories\CompanySubscriptionPlanRepository;
use App\Repositories\MasterApplicabilitiesRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\QuestionClassificationRepository;
use Cartalyst\Stripe\Exception\CardErrorException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\LicenseRepository;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\LicenseLocationRepository;
use App\Repositories\LicenseRemindersRepositories;
use App\Repositories\MasterStateRepository;
use App\Repositories\MasterLicenseRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\CompanyRepository;
use App\Lib\ProrateDayCalculation;
use App\Models\MasterCountry;
use App\Http\Requests\LicenseLocationRequest;
use App\Http\Requests\LicensePerchesRequest;
use App\Http\Requests\CreateLicenseRequest;
use App\Http\Requests\LicenseRequest;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use DateTime;
use Carbon\Carbon;

class LicenseController extends Controller
{
    private $license;
    private $company_location;
    private $license_location;
    private $country;
    private $states;
    private $master_license;
    private $company;
    private $payment;
    private $licenseReminder;
    private $company_location_license;
    private $question_classification;
    private $appointment_classification;
    private $company_subscription_plan;
    private $master_data;
    private $master_applicability;


    public function __construct(LicenseRepository $license,
                                CompanyLocationRepository $company_location,
                                LicenseLocationRepository $license_location,
                                MasterCountry $country, MasterStateRepository $states,
                                MasterLicenseRepository $master_license,
                                CompanyRepository $company,
                                PaymentRepository $payment,
                                LicenseRemindersRepositories $licenseReminder,
                                CompanyLocationLicenseRepository $company_location_license,
                                QuestionClassificationRepository $question_classification,
                                AppointmentClassificationRepository $appointment_classification,
                                CompanySubscriptionPlanRepository $company_subscription_plan,
                                MasterUserRepository $master_data,
                                MasterApplicabilitiesRepository $master_applicability

    ){
        $this->license       = $license;
        $this->company_location = $company_location;
        $this->license_location = $license_location;
        $this->country = $country;
        $this->states = $states;
        $this->master_license = $master_license;
        $this->company = $company;
        $this->payment = $payment;
        $this->licenseReminder = $licenseReminder;
        //$this->stripe = Stripe::make('sk_test_rEI3IHfp9TDIlV8JcRhPQ5i8');
        $this->stripe = Stripe::make(Config::get('simplifya.STRIPE_KEY'));
        $this->company_location_license = $company_location_license;
        $this->question_classification = $question_classification;
        $this->appointment_classification = $appointment_classification;
        $this->company_subscription_plan = $company_subscription_plan;
        $this->master_data = $master_data;
        $this->master_applicability = $master_applicability;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = $this->country->all(array('*'));
        return view('configuration.licenseManager')->with(array('page_title' => 'License Manager', 'countries' =>$countries));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLicenseRequest $request)
    {
        $license_name = $request->license_name;
        $state_id = $request->state;
        $fee = $request->checklist_fee;
        $type = $request->type;
        $applicability_ids = $request->applicability_ids;

        $data = array('name' => $license_name, 'master_states_id' => $state_id, 'checklist_fee' => $fee , 'type' => $type, 'status' => 1, 'created_by '=> Auth::User()->id);
        DB::beginTransaction();
        try{
            $response = $this->license->create($data);
            if (isset($applicability_ids) && is_array($applicability_ids)) {
                \Log::debug("==== app ids ");
                \Log::debug(print_r($applicability_ids, true));
                $this->master_license->saveMasterApplicabilitiesForLicense($response->id, $applicability_ids);
                \Log::debug("==== app ids ");
            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            $message = Config::get('messages.ADD_LICENSE_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }

        if($response) {
            $message = Config::get('messages.ADD_LICENSE_SUCCESS');
            return response()->json(array('success' => 'true', 'message'=> $message));
        } else {
            $message = Config::get('messages.ADD_LICENSE_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateLicenseRequest $request)
    {
        //declare and initialize variables
        $license_id = $request->license_id;
        $license_name = $request->license_name;
        $state_id = $request->state;
        $fee = $request->checklist_fee;
        $type = $request->type;
        $applicability_ids = $request->applicability_ids;

        $data = array('name' => $license_name, 'master_states_id' => $state_id, 'checklist_fee' => $fee, 'type' => $type, 'status' => 1, 'updated_by'=> Auth::User()->id);

        DB::beginTransaction();
        try{
            $response = $this->license->changeLicenseType($license_id, $data);
            if (isset($applicability_ids) && is_array($applicability_ids)) {
                $this->master_license->saveMasterApplicabilitiesForLicense($license_id, $applicability_ids);
            }
            DB::commit();
            if($response) {
                $message = Config::get('messages.UPDATE_LICENSE');
                return response()->json(array('success' => 'true', 'message'=> $message));
            } else {
                $message = Config::get('messages.UPDATE_LICENSE_FAIL');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        }catch (\Exception $e) {
            DB::rollback();
            $message = Config::get('messages.UPDATE_LICENSE_FAIL');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }


    }

    /**
     * Get License Types by states id
     * @param LicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLicenseTypeByStateId(LicenseRequest $request)
    {
        //declare and initialize variables
        $location_id = $request->location_id;
        $licenses = $this->company_location->getLicenseTypes($location_id);

        $data = array();
        if(isset($licenses[0])) {
            foreach($licenses as $license) {
                $data[] = array('id' => $license->id, 'name' => $license->license_name);
            }
        }
        return response()->json(array('data' => $data), 200);
    }

    /**
     * @param $company_id
     * @return \Illuminate\Http\Response
     */
    public function getCompanyLicense($company_id)
    {
        $data = array();
        $company_licenses = $this->license_location->getLicenseLocation($company_id);
        foreach($company_licenses as $company_license) {
            $data[] = array($company_license->company_loc_name,
                $company_license->master_license_name,
                $company_license->license_number,
                ($company_license->license_status == 1)?
                    "<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#licenseInfo' title='Edit' data-license_id='$company_license->id' onclick='changeLocationLicense({$company_license->license_id})'><i class='fa fa-paste'></i></a>
                             <a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#licenseDelete' title='Inactive' data-license_id='$company_license->id'onclick='changeLocationLicenseStatus({$company_license->license_id}, 2)'><i class='fa fa-thumbs-o-up'></i></a>
                             <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#licenseDelete' title='Delete' data-license_id='$company_license->id'onclick='changeLocationLicenseStatus({$company_license->license_id}, 0)'><i class='fa fa-trash-o'></i></a>
                            ":
                    "
                            <a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#licenseInfo' title='Edit' data-license_id='$company_license->id' onclick='changeLocationLicense({$company_license->license_id})'><i class='fa fa-paste'></i></a>
                             <a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#licenseDelete' title='Inactive' data-license_id='$company_license->id'onclick='changeLocationLicenseStatus({$company_license->license_id}, 1)'><i class='fa fa-thumbs-o-down'></i></a>
                             <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#licenseDelete' title='Delete' data-license_id='$company_license->id'onclick='changeLocationLicenseStatus({$company_license->license_id}, 0)'><i class='fa fa-trash-o'></i></a>
                            "
            );
        }
        return response()->json(array('data' => $data), 200);
    }

    /**
     * Get company locations and license type
     * @return company license and location
     */
    public  function mjbLicense()
    {
        //declaring variables
        $locations_array = array();
        $license_array = array();
        $states_array = array();
        $company_id = Auth::User()->company_id;
        $locations = $this->company_location->getLocationByCompanyId($company_id);
        foreach($locations as $location) {
            $states_array[] = $location->states_id;
            $locations_array[] = array('loc_id' => $location->id, 'loc_name' => $location->name);
        }
        $states = array_unique($states_array);
        $licenses = $this->master_license->getLicenseByStatesId($states);
        foreach($licenses as $license) {
            $license_array[] = array('license_id' => $license->id, 'license_name' => $license->name);
        }
        $subscription_plan = $this->company_subscription_plan->getCurrentActiveSubscriptionPlanByDate($company_id);
        $amount = $subscription_plan->amount;
        $mjbFreeLicense = $this->master_data->findWhere(array('name' => 'MJB_FREE_LICENSE'))->first()->value;
        if (isset($mjbFreeLicense) && $mjbFreeLicense == 1) {
            $foc = 1;
        }else {
            $foc = 0;
        }
        return view('license.licenseManager')->with(array('page_title' => 'License Manager', 'locations' =>$locations_array, 'licenses' => $license_array, 'plan_amount' => $amount, 'foc' => $foc));
    }
    /**
     * @param $company_id
     * @return CompanyLocationRepository
     */
    public function getMjbLicenseByCompanyId()
    {
        //declare and initialize variables
        $company_id = Auth::User()->company_id;
        $license = array();
        $date = '';
        $license_status = '';
        $responses = $this->license_location->getAllLicensesByCompanyId($company_id);


        if(empty($responses)) {
            return response()->json(array('data'=> $license));
        } else {
            foreach($responses as $response) {
                $toDay = date("m/d/Y");
                $curdate = new DateTime(date('m/d/Y'));
                $exp_date = new DateTime($response->renewal_date);

                if($exp_date->format('Y') == '-0001') {
                    $license_status = '-';
                    $date = '-';
                } else {
                    $date = $exp_date->format('m/d/Y');
                    if ($exp_date < $curdate) {
                        $license_status = '<span class="badge badge-item ">Expired</span>';
                    } else {
                        $license_status = '<span class="badge badge-success ">Active</span>';
                    }
                }
                $license[] = array(
                    $response->license_id,
                    $response->license_number,
                    $response->name,
                    $response->location_name,
                    $date,
                    $license_status,
                    ($response->status == '1')?"<a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Active' data-user_id='2' onclick='changeLicenseStatus({$response->license_id}, 2)'><i class='fa fa-thumbs-o-up'></i></a>":"<a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Inactive' data-user_id='50' onclick='changeLicenseStatus({$response->license_id}, 1)'><i class='fa fa-thumbs-o-down'></i></a>",
                    "<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#licenseInfo' title='Edit' data-license_id='$response->license_id' onclick='editLicenseDetails({$response->license_id})'><i class='fa fa-paste'></i></a>
                    <a class=\"btn btn-danger btn-circle\" data-toggle=\"tooltip\" data-target=\"#locationDelete\" title=\"Delete\" data-user_id=\"2\" onclick=\"changeLicenseStatus({$response->license_id}, 0)\"><i class=\"fa fa-trash-o\"></i></a>
                    "
                );
            }
            return response()->json(array('data'=> $license));
        }
    }

    /**
     * get license details by id
     * @param $license_id
     */
    public function getLicenseDetailsById($license_id)
    {
        //declare and initialize variables
        $reminder_id = array();
        $master_license_id = $this->license_location->findWhere(['id' => $license_id]);
        $check_ml = $this->master_license->findWhere(['id' => $master_license_id[0]->license_id]);
        $reminders = $this->licenseReminder->findWhere(array('license_location_id' => $license_id));
        foreach($reminders as $reminder) {
            $reminder_id[] = $reminder->reminder;
        }

        if($check_ml[0]->status!=2) {
            $licenses_details = $this->license_location->getLicenseDetailsById($license_id);
            $renewal_date = date('m/d/Y', strtotime(str_replace('/', '-', $licenses_details[0]->renewal_date)));
            $license_date = date('m/d/Y', strtotime(str_replace('/', '-', $licenses_details[0]->license_date)));

            if($renewal_date=="11/30/-0001" &&  $license_date=="11/30/-0001"){
                $renewal_date = date('m/d/Y');
                $license_date = date('m/d/Y');
            }

            $license_types = $this->license_location->getMasterLicense($licenses_details[0]['states_id']);
            $data = array('license_details' => $licenses_details, 'master_license_type' => $license_types, 'renewal_date' => $renewal_date, 'license_date' => $license_date, 'reminder' => $reminder_id);
            return response()->json(array('success' => 'true', 'data' => $data), 200);
        }else{
            $message = Config::get('messages.LICENSE_NOT_AVAILABLE');
            return response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * search by license no, license type
     * @return CompanyLocationRepository
     */
    public function searchLicense()
    {
        //declare and initialize variables
        $license = array();
        $date = '';
        $company_id = Auth::User()->company_id;
        $license_number = Input::get('license_number');
        $license_type = Input::get('license_type');
        $license_location = Input::get('license_location');
        $responses = $this->license_location->searchLicense($company_id, $license_number, $license_type, $license_location);
        if($responses->isEmpty()) {
            return response()->json(array('data'=> $license));
        } else {
            foreach($responses as $response) {
                $toDay = date("d/m/Y");

                $exp_date = new DateTime($response->renewal_date);
                if($exp_date->format('Y') == '-0001') {
                    $license_status = '-';
                    $date = '-';
                } else {
                    $date = $exp_date->format('d/m/Y');
                    if ($exp_date->format('d/m/Y') < $toDay) {
                        $license_status = '<span class="badge badge-item ">Expire</span>';
                    } else {
                        $license_status = '<span class="badge badge-success ">Active</span>';
                    }
                }
                $license[] = array(
                    $response->license_id,
                    $response->license_number,
                    $response->name,
                    $date,
                    $license_status,
                    ($response->status == '1')?"<a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Active' data-user_id='2' onclick='changeLicenseStatus({$response->license_id}, 2)'><i class='fa fa-thumbs-o-up'></i></a>":"<a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Inactive' data-user_id='50' onclick='changeLicenseStatus({$response->license_id}, 1)'><i class='fa fa-thumbs-o-down'></i></a>",
                    "<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#licenseInfo' title='Edit' data-license_id='$response->license_id' onclick='editLicenseDetails({$response->license_id})'><i class='fa fa-paste'></i></a>
                    <a class=\"btn btn-danger btn-circle\" data-toggle=\"tooltip\" data-target=\"#locationDelete\" title=\"Delete\" data-user_id=\"2\" onclick=\"changeLicenseStatus({$response->license_id}, 0)\"><i class=\"fa fa-trash-o\"></i></a>
                    "
                );
            }
            return response()->json(array('data'=> $license));
        }
    }

    /**
     * Update license details
     * @param LicenseLocationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLocationLicense(LicenseLocationRequest $request)
    {
        //declare and initialize variables
        $location_license_id = $request->location_license_id;
        $location_id = $request->location_id;
        $license_id = $request->license_id;
        $license_no = $request->license_no;
        $company_id = $request->company_id;
        $license_response = $this->license_location->isExistLocationLicense($company_id, $license_id, $location_id);

        if($license_response) {
            $data = array('license_number' => $license_no);
            $response = $this->license_location->updateLocationLicense($location_license_id, $company_id, $location_id, $data);
            if ($response) {
                $message = Config::get('messages.EDIT_LOCATION_LICENSE');
                return response()->json(array('success' => 'true', 'message' => $message));
            } else {
                $message = Config::get('messages.EDIT_LOCATION_LICENSE_FAIL');
                return response()->json(array('success' => 'false', 'message' => $message));
            }
        }

    }

    /**
     * License activate, inactivate and delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLocationLicenseStatus()
    {
        //declare and initialize variables
        $status = Input::get('status');
        $license_id = Input::get('license_id');

        $license_location_status =$this->license_location->changeLicenseLocationStatus($license_id, $status);

        if($license_location_status) {
            if($status == 0) {
                $message = Config::get('messages.LICENSE_LOCATION_DELETE');
            } elseif($status == 1) {
                $message = Config::get('messages.LICENSE_LOCATION_ACTIVATE');
            } elseif($status == 2) {
                $message = Config::get('messages.LICENSE_LOCATION_INACTIVATE');
            }

            return response()->json(array('success' => 'true', 'message'=> $message));
        } else {
            $message = Config::get('messages.ERROR');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * Get License by location and company id
     * @param LicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllLicenses(LicenseRequest $request)
    {
        //declare and initialize variables
        $locationId = $request->location_id;
        $companyId = $request->company_id;
        $amount = $this->license_location->getAllLicenses($locationId, $companyId);
        if($amount)
        {
            $message = "";
            return response()->json(array('success' => 'true', 'data' => $amount));
        }
        else
        {
            $message = "";
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * Get license fee according to location ;
     * @param LicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLicenseAmount(LicenseRequest $request)
    {
        //declare and initialize variables
        $license_id = $request->location_id; //get license id from post method
        $type = $request->type; //get license id from post method

        $amount = $this->license_location->getLicenseAmount($license_id, $type);

        if($amount)
        {
            $message = "";
            return response()->json(array('success' => 'true', 'data' => $amount));
        }
        else
        {
            $message = "";
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    public function getLicenseTypes()
    {
        //get all license types
        $licenses = $this->license->getAllLicenseTypes();
        $data = array();
        $applicability_types=Config::get('simplifya.APPLICABILITY_TYPES');
        foreach($licenses as $license) {
            $data[] = array(
                //$license['id'],
                $license['name'],
                $license['checklist_fee'],
                $applicability_types[$license['type']],
                ($license['status'] == 1)?
                    "<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-license_id='".$license['id']."' href='" . URL('/configuration/licenses/edit/'.$license['id'] ) ."'><i class='fa fa-paste'></i></a>
                        <a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Active'  data-license_id='".$license['id']."'onclick='changeLicenseStatus({$license['id']}, 2)'><i class='fa fa-thumbs-o-up'></i></a>
                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationRemove' title='Remove'  data-license_id='".$license['id']."'onclick='removeLicenceStatusCheck({$license['id']})'><i class='fa fa-trash'></i></a>
                        ":"
                        <a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-license_id='".$license['id']."' href='" . URL('/configuration/licenses/edit/'.$license['id'] ) ."'><i class='fa fa-paste'></i></a>
                        <a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Inactive' data-license_id='".$license['id']."'onclick='changeLicenseStatus({$license['id']}, 1)'><i class='fa fa-thumbs-o-down'></i></a>
                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationRemove' title='Remove'  data-license_id='".$license['id']."'onclick='removeLicenceStatusCheck({$license['id']})'><i class='fa fa-trash'></i></a>
                        "
            );
        }
        return response()->json(["data" => $data]);
    }

    public function changeLicenseStatus()
    {
        //declare and initialize variables
        $status = Input::get('status');
        $license_id = Input::get('license_id');

        $change_license_status =$this->license->changeLicenseStatus($license_id, $status);

        if($change_license_status) {
            if($status == 1) {
                $message = Config::get('messages.LICENSE_ACTIVE');
            } else {
                $company_location_list = $this->company_location_license->findWhere(array('license_id' => $license_id));

                if(isset($company_location_list[0])){
                    foreach ($company_location_list as $item){
                        $this->company_location_license->update(['status' => 2], $item->id);
                    }
                }

                $message = Config::get('messages.LICENSE_INACTIVE');
            }

            return response()->json(array('success' => 'true', 'message' => $message), 200);
        }
    }

    public function getLicenseById($license_id)
    {
        $license = $this->license->licenseDetailsById($license_id);
        $selectedIds = $this->master_license->getMasterApplicabilitiesForLicense($license_id);
        \Log::debug("selected ids");
        \Log::debug(print_r($selectedIds, true));
        $license_name   = $license[0]['license_name'];
        $checklist_fee  = $license[0]['checklist_fee'];
        $country_name   = $license[0]['country_name'];
        $country_id     = $license[0]['country_id'];
        $state_id       = $license[0]['state_id'];
        $state_name     = $license[0]['state_name'];
        $type           = $license[0]['type'];
        $states = $this->states->getAllStatesByCountry($country_id);

        $applicability_types=Config::get('simplifya.APPLICABILITY_TYPES');
        $applicability_groups=Config::get('simplifya.APPLICABILITY_GROUPS');

        $results = $this->master_applicability->getApplicabilitesWith(array('type' => $type, 'country_id' => $country_id));
        $applicabilityData =  array_map(function ($item) use ($applicability_types,$applicability_groups, $selectedIds) {
            return [
                'id' => (INT)$item['id'],
                'name' => $item['name'],
                'country' => $item['country'],
                'type' => $applicability_types[$item['type']],
                'group'=>$applicability_groups[$item['group_id']],
                'checked' => in_array($item['id'], $selectedIds)
            ];
        }, $results->toArray());


        $data = array('license_name' => $license_name,
            'checklist_fee' => $checklist_fee,
            'country_name' => $country_name,
            'country_id' => $country_id,
            'state_id' => $state_id,
            'states' => $states,
            'type' => $type,
            'applicabilties' => $applicabilityData
        );
        return response()->json(["data" => $data, 'success' => 'true']);
    }

    /*
     * get license types from licensed location
     */
    public function getLicenseTypeLicenseLocation()
    {
        //declare variables
        $locations = array();
        $licenses  = array();
        $states = array();
        $company_id = Auth::User()->company_id;

        $company_locations = $this->company_location->getLocationByCompanyId($company_id);

        if($company_locations) {
            foreach($company_locations as $company_location) {
                $locations[] = array('id' => $company_location->id, 'name' => $company_location->name);
                $states[] = $company_location->states_id;
            }
            $company_licenses = $this->master_license->getLicenseByStatesId(array_unique($states));
            foreach($company_licenses as $company_license) {
                $licenses[] = array('id' => $company_license->id, 'name' => $company_license->name);
            }
        }
        $data = array('location' => $locations, 'license' => $licenses);
        return response()->json(["data" => $data, 'success' => 'true']);
    }

    /**
     * license perches function
     * @param LicensePerchesRequest $request
     */
    public function perchesLicense(LicensePerchesRequest $request)
    {
        //declare and initialize variables
        $license_id     = $request->license_id;
        $location_id    = $request->location_id;
        $license_number = $request->license_no;
        $now = new DateTime();
        $renewal_date   = new DateTime($request->renewal_date);
        $license_date = new DateTime($request->license_date);

        $amount     = $request->amount;
        $reminders   = $request->reminder;
        //$name   = $request->name;
        $company_id  = Auth::User()->company_id;
        $user_id    = Auth::User()->id;
        $company_details = $this->company->find($company_id);
        $license_details = $this->license_location->isExistLocationLicense($company_id, $license_id, $location_id);

        if(!$license_details) {
            $data = array(
                'company_id' => $company_id,
                'license_id' => $license_id,
                'location_id' => $location_id,
                'license_number' => $license_number,
                //'name' => $name,
                'license_date' => $license_date->format('Y-m-d'),
                'renewal_date' => $renewal_date->format('Y-m-d'),
                'amount' => $amount,
                'status' => 0,
                'created_by' => $user_id
            );
            $status = false;
            // Start DB transaction
            DB::beginTransaction();
            try {
                $response = $this->license_location->create($data);

                if($response) {
                    $remind_data = array();
                    $currency = 'USD';
                    foreach($reminders as $reminder) {
                        $remind_data[] = array('license_location_id' => $response->id, 'user_id' => Auth::User()->id, 'reminder' => $reminder, 'created_at' => $now, 'updated_at' => $now);
                    }
                    $this->licenseReminder->insertReminders($remind_data);
                    //check mjb company's foc flag if it is `1` skip payment process
                    $mjbFreeLicense = $this->master_data->findWhere(array('name' => 'MJB_FREE_LICENSE'))->first()->value;
                    if (isset($mjbFreeLicense) && $mjbFreeLicense == 1) {
                        \Log::debug("=== foc is enabled");

                        $message = Config::get('messages.LICENSE_PERCHES_SUCCESS');
                        // All good
                        $license_update = $this->license_location->updateLicenseById(0, $license_id, $location_id, $user_id);
                        DB::commit();
                        return response()->json(array(
                            'success' => 'true',
                            'message' => $message
                        ));

                    }else { // continue payment process
                        \Log::debug("=== foc is disabled");
                        $customer_charge = $this->licenseCharges($company_details->stripe_id, $currency,$amount);
                        if ($customer_charge['success']) {
                            $tx_type = 'license';
                            $response = $this->paymentHandler($customer_charge, $amount, $currency, $company_id, $tx_type);
                            if($response['success'] == 'true') {

                            }
                            $license_update = $this->license_location->updateLicenseById($response['payment_id'], $license_id, $location_id, $user_id);
                            if($license_update) {
                                $message = Config::get('messages.LICENSE_PERCHES_SUCCESS');
                                // All good
                                DB::commit();
                                return response()->json(array(
                                    'success' => 'true',
                                    'message' => $message
                                ));
                            }
                        } else {
                            // Something went wrong
                            DB::rollback();
                            if (isset($customer_charge['message'])) {
                                $message = $customer_charge['message'];
                            }else {
                                $message = Config::get('messages.LICENSE_PERCHES_FAIL');
                            }
                            return response()->json(array(
                                'success' => 'flase',
                                'message' => $message
                            ));
                        }
                    }

                }
            } catch(Exception $ex){
                // database transaction rollback if Something went wrong
                DB::rollback();
            }
        } else {
            //create data array
            $data = array(
                'license_number' => $license_number,
                //'name' => $name,
                'license_date' => $license_date,
                'renewal_date' => $renewal_date,
                'amount' => $amount,
                'status' => 1,
                'updated_by' => $user_id
            );
            if(($license_details->status == 0)) {
                $currency = 'USD';
                $customer_charge = $this->licenseCharges($company_details->stripe_id, $currency,$amount);
                if ($customer_charge['success']) {
                    $tx_type = 'license';
                    $response = $this->paymentHandler($customer_charge, $amount, $currency, $company_id, $tx_type);
                    if($response['success'] == 'true') {
                        $data['payment_id'] = $response['payment_id'];
                        $license_update = $this->license_location->updateDeletedLicenseById($license_id, $location_id, $user_id, $data);
                        if($license_update) {
                            $message = Config::get('messages.LICENSE_PERCHES_SUCCESS');

                            return response()->json(array(
                                'success' => 'true',
                                'message' => $message
                            ));
                        }
                    }
                } else {
                    if (isset($customer_charge['message'])) {
                        $message = $customer_charge['message'];
                    }else {
                        $message = Config::get('messages.LICENSE_PERCHES_FAIL');
                    }
                    return response()->json(array(
                        'success' => 'flase',
                        'message' => $message
                    ));
                }
            } else {
                $message = Config::get('messages.ALREADY_ADDED_LICENSE_LOCATION');
                return response()->json(array(
                    'success' => 'false',
                    'message' => $message
                ));
            }
        }
    }

    /**
     * License charge private class
     * @param $customer
     * @param $currency
     * @param $amount
     * @return array
     */
    private function licenseCharges($customer, $currency, $amount)
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

    /**
     * calculate license perches fee
     * @param $license_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateLicenseFee($license_id)
    {
        //declare and initialize variables
        $company_id = Auth::User()->company_id;
        $date = date("Y/m/d");
        $prorate_day_cal = new ProrateDayCalculation();
        $day_calculation = $prorate_day_cal->dayCalculation($date);
        $response = $this->company->getSubscriptionFee($company_id);

        // Get charge amount based on selected subscription plan
        $subscription_plan = $this->company_subscription_plan->getCurrentActiveSubscriptionPlanByDate($company_id);
        if($response) {
            //$amount = $response[0]->amount;
            $amount = $subscription_plan->amount;

            $nextDueDayDatetime = strtotime($subscription_plan->due_date);
            $nextDueDayPreviousDate = date("Y-m-d", strtotime("-1 day", $nextDueDayDatetime ));;

            \Log::debug("==== next  due date " . $nextDueDayPreviousDate);
            $daysRemaining = $prorate_day_cal->getDaysRemaining($subscription_plan->due_date);
            \Log::debug("==== new days remaining " . $daysRemaining);
            $subscription_fee = ($amount/$day_calculation['days_in_month'])*$daysRemaining;
            //$subscription_fee = $amount;
            return response()->json(["data" => round($subscription_fee, 2) , 'success' => 'true']);
        } else {
            $message = '';
            return response()->json(['message' => $message, 'success' => 'false']);
        }


    }

    /**
     * license payment add payment table
     * @param $customer_charge
     * @param $subscription_fee
     * @param $currency
     * @param $company_id
     * @param $tx_type
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentHandler($customer_charge, $subscription_fee, $currency, $company_id, $tx_type)
    {
        //get company subscription type by company ID
        $master_subscription = $this->company->getSubscriptionType($company_id);
        $payments = array(
            'req_date_time' => Carbon::now(),
            'object'        => $customer_charge['charge']['object'],
            'req_currency'  => $currency,
            'req_amount'    => $subscription_fee,
            'res_date_time' => date("Y-m-d H:i:s", $customer_charge['charge']['created']),
            'res_id'        => $customer_charge['charge']['id'],
            'res_currency'  => $customer_charge['charge']['currency'],
            'res_amount'    => $customer_charge['charge']['amount']/100,
            'company_id'    => $company_id,
            'tx_type'       => $tx_type,
            'created_by'    => Auth::user()->id,
            'tx_status' => 1,
            'charge_id' => $customer_charge['charge']['id'],
            'balance_transaction' => $customer_charge['charge']['balance_transaction'],
            'created_by' => Auth::user()->id
        );
        $response_payment = $this->payment->create($payments);

        if($response_payment) {
            $message = Config::get('messages.PAYMENT_SUCCESSFUL');
            return array('success' => 'true', 'message'=> $message, 'payment_id'=>$response_payment->id);
        }
    }

    /**
     * update license
     * @param LicensePerchesRequest $request
     */
    public function updateLicense(LicensePerchesRequest $request)
    {
        //declare & initialize variables
        $saved_reminders = array();
        $remind_data = array();
        $reminder1 = '';
        $reminder2 = '';
        $reminders = array();
        $now = new DateTime();
        $license_id     = $request->license_id;
        $location_id    = $request->location_id;
        $license_number = $request->license_no;
        $license_location_id = $request->license_location_id;
        $license_date   = date('Y-m-d', strtotime($request->license_date));
        $renewal_date   = date('Y-m-d', strtotime($request->renewal_date));

        $reminder   = $request->reminder;
        //$name       = $request->name;
        $company_id = Auth::User()->company_id;
        $user_id    = Auth::User()->id;

        $license_details = $this->license_location->isExistLocationLicense($company_id, $license_id, $location_id);
        $license_reminders = $this->licenseReminder->findWhere(array('license_location_id' => $license_location_id));
        if(count($license_reminders) > 0) {
            foreach($license_reminders as $license_reminder) {
                $saved_reminders[] = $license_reminder->reminder;
            }
            $reminder1 = array_diff($saved_reminders, $reminder);
            $reminder2 = array_diff($reminder, $saved_reminders);

        } else {
            foreach($reminder as $remind) {
                $reminders[] = array('license_location_id' => $license_location_id, 'user_id' => Auth::User()->id, 'reminder' => $remind, 'created_at' => $now, 'updated_at' => $now);
            }
        }

        if($license_details) {
            $data = array(
                'company_id' => $company_id,
                'license_id' => $license_id,
                'location_id' => $location_id,
                'license_number' => $license_number,
                //'name' => $name,
                'license_date' => $license_date,
                'renewal_date' => $renewal_date,
                'created_by' => $user_id
            );
            $response = $this->license_location->update($data, $license_location_id);
            if(!empty($reminder1)) {
                $this->licenseReminder->deleteReminders($reminder1, $license_location_id);
            }
            if(!empty($reminder2)) {
                foreach($reminder2 as $reminder_2) {
                    $remind_data[] = array('license_location_id' => $license_location_id, 'user_id' => Auth::User()->id, 'reminder' => $reminder_2, 'created_at' => $now, 'updated_at' => $now);
                }

                $this->licenseReminder->insertReminders($remind_data);
            }
            if(!empty($reminders)) {
                $this->licenseReminder->insertReminders($reminders);
            }

            if($response) {
                $message = Config::get('messages.EDIT_LOCATION_LICENSE');
                return array('success' => 'true', 'message'=> $message);
            } else {
                $message = Config::get('messages.EDIT_LOCATION_LICENSE_FAIL');
                return array('success' => 'false', 'message'=> $message);
            }
        }
    }

    /**
     * get locations by license ID
     * @param $license_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocationsByLicenseId($license_id)
    {
        $license_locations = array();
        $company_id = Auth::User()->company_id;
        $locations = $this->license->getLicenseLocations($license_id, $company_id);
        foreach($locations as $location) {
            $license_locations[] = array('id' => $location->location_id, 'name' => $location->location_name);
        }
        return response()->json(["data" => $license_locations, 'success' => 'true']);
    }

    /**
     * Activate license
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateLicense()
    {
        //declare and initialize variables
        $amount = Input::get('amount');
        $license_id = Input::get('license_id');
        $company_id = Auth::User()->company_id;
        $currency = 'USD';

        $company_details = $this->company->find($company_id);

        $mjbFreeLicense = $this->master_data->findWhere(array('name' => 'MJB_FREE_LICENSE'))->first()->value;
        if (isset($mjbFreeLicense) && $mjbFreeLicense == 1) {
            $license_update = $this->license_location->update(['status' => 1], $license_id);
            if ($license_update) {
                $message = Config::get('messages.LICENSE_PERCHES_SUCCESS');
                return response()->json(array(
                    'success' => 'true',
                    'message' => $message
                ));
            }else {
                $message = Config::get('messages.LICENSE_PERCHES_FAIL');
                return response()->json(array(
                    'success' => 'false',
                    'message' => $message
                ));
            }
        }else {
            $customer_charge = $this->licenseCharges($company_details->stripe_id, $currency, $amount);
            if ($customer_charge['success']) {
                $tx_type = 'license';
                $response = $this->paymentHandler($customer_charge, $amount, $currency, $company_id, $tx_type);
                if ($response['success'] == 'true') {
                    $license_update = $this->license_location->update(['status' => 1], $license_id);
                    if ($license_update) {
                        $message = Config::get('messages.LICENSE_PERCHES_SUCCESS');
                        return response()->json(array(
                            'success' => 'true',
                            'message' => $message
                        ));
                    }
                } else {
                    $message = Config::get('messages.LICENSE_PERCHES_FAIL');
                    return response()->json(array(
                        'success' => 'false',
                        'message' => $message
                    ));
                }
            }
        }



    }

    /**
     * Check if license is currently use in the system
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLicenseExists()
    {
        //declare and initialize variable
        $licenseId = $_GET['license_id'];

        //get relevant data from repositories
        $check_question_classification = $this->question_classification->findWhere(array('entity_tag' => 'LICENCE', 'option_value' => $licenseId));
        $check_appointment_classification = $this->appointment_classification->findWhere(array('entity_type' => 'LICENCE', 'option_value' => $licenseId));
        $check_company_ll = $this->company_location_license->find(array('incense_id' => $licenseId));

        if(isset($check_question_classification[0]) || isset($check_appointment_classification[0]) || isset($check_company_ll[0]) ){
            return response()->json(array('success' => 'true', 'status' => 'false', 'message' => 'License is currently occupied!'));
        }else{
            return $this->removeLicenseFromSystem($licenseId);
        }
    }

    /**
     * Remove if license not use in the system
     * @param $licenseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeLicenseFromSystem($licenseId){
        //remove master license
        $remove = $this->master_license->delete($licenseId);
        if($remove){
            return response()->json(array('success' => 'true', 'status' => 'true', 'message' => 'License is no longer available!'));
        }else{
            return response()->json(array('success' => 'false', 'message' => 'License remove failed!'));
        }
    }

    public function activateLicenseCount()
    {
        $company_id = Auth::User()->company_id;
        return $this->license_location->getActiveLicense($company_id);
    }

    /**
     * Add new license view
     */
    public function createLicenseView($license_id=null) {
        if(isset($license_id)){
            $page_title='Edit License';
            $license_id=$license_id;
        }else{
            $page_title='Add New License';
            $license_id=0;
        }
        return view('license.createLicense')->with('page_title', $page_title)->with('license_id',$license_id);
    }

    /**
     * Get master applicability
     * @param $type
     * @param $country_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplicabilityByStateAndCountries($type, $country_id) {

        $applicability_types=Config::get('simplifya.APPLICABILITY_TYPES');
        $applicability_groups=Config::get('simplifya.APPLICABILITY_GROUPS');

        $results = $this->master_applicability->getApplicabilitesWith(array('type' => $type, 'country_id' => $country_id));
        $applicabilityData =  array_map(function ($item) use ($applicability_types,$applicability_groups) {
            return [
                'id' => (INT)$item['id'],
                'name' => $item['name'],
                'country' => $item['country'],
                'type' => $applicability_types[$item['type']],
                'group'=>$applicability_groups[$item['group_id']]
            ];
        }, $results->toArray());
        return response()->json(array('success' => 'true', 'data' => $applicabilityData));
    }
}

