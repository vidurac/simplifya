<?php

namespace App\Http\Controllers\Web;

use App\Events\MjbInHouseAuditSupport;
use App\Events\MjbSignUpSupport;
use App\Models\AppointmentClassification;
use App\Models\AppointmentQuestion;
use App\Models\MasterClassification;
use App\Models\MasterClassificationOption;
use App\Repositories\CompanyCardRepository;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanyUserRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\MasterCityRepository;
use App\Repositories\MasterClassificationEntityAllocationRepository;
use App\Repositories\MasterCountryRepository;
use App\Repositories\MasterLicenseRepository;
use App\Repositories\MasterStateRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\UsersRepository;
use App\Repositories\RequestsRepository;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AppointmentCreateRequest;
use App\Repositories\AppointmentRepository;
use App\Repositories\MasterClasificationRepository;
use DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use App\Lib\sendMail;
use App\Events\AddAppointmentNotifRequest;
use App\Repositories\LicenseLocationRepository;


class AppointmentController extends Controller
{
    private $inspection, $company, $appointment, $questionClassification, $location, $question, $appointmentQuestion, $appointmentClassification,
        $masterClassiEntityAllo, $masterClassification, $masterClassificationOption, $user, $masterLicences, $companyUser, $masterState, $masterCity, $masterCountry, $request,
        $licenseLocation, $payment ,$company_card,$master_data;

    /**
     * AppointmentController constructor.
     * @param InspectionRepository $inspection
     * @param CompanyRepository $company
     * @param AppointmentRepository $appointment
     * @param QuestionClassificationRepository $questionClassification
     * @param CompanyLocationRepository $location
     * @param QuestionRepository $question
     * @param AppointmentQuestion $appointmentQuestion
     * @param AppointmentClassification $appointmentClassification
     * @param MasterClassificationEntityAllocationRepository $masterClassiEntityAllo
     * @param MasterClasificationRepository $masterClassification
     * @param MasterClassificationOption $masterClassificationOption
     * @param UsersRepository $user
     * @param MasterLicenseRepository $masterLicences
     * @param CompanyUserRepository $companyUser
     * @param MasterStateRepository $masterState
     * @param MasterCityRepository $masterCity
     * @param MasterCountryRepository $masterCountry
     * @param RequestsRepository $request
     * @param LicenseLocationRepository $licenseLocation
     */
    public function __construct(InspectionRepository $inspection,
                                CompanyRepository $company,
                                AppointmentRepository $appointment,
                                QuestionClassificationRepository $questionClassification,
                                CompanyLocationRepository $location,
                                QuestionRepository $question,
                                AppointmentQuestion $appointmentQuestion,
                                AppointmentClassification $appointmentClassification,
                                MasterClassificationEntityAllocationRepository $masterClassiEntityAllo,
                                MasterClasificationRepository $masterClassification,
                                MasterClassificationOption $masterClassificationOption,
                                MasterUserRepository $master_data,
                                UsersRepository $user,
                                MasterLicenseRepository $masterLicences,
                                CompanyUserRepository $companyUser,
                                MasterStateRepository $masterState,
                                MasterCityRepository $masterCity,
                                MasterCountryRepository $masterCountry,
                                RequestsRepository $request,
                                LicenseLocationRepository $licenseLocation,
                                PaymentRepository $payment,
                                CompanyCardRepository $company_card
    )

    {
        $this->inspection = $inspection;
        $this->company    = $company;
        $this->appointment= $appointment;
        $this->questionClassification  = $questionClassification;
        $this->location   = $location;
        $this->question   = $question;
        $this->appointmentQuestion   = $appointmentQuestion;
        $this->appointmentClassification = $appointmentClassification;
        $this->masterClassiEntityAllo = $masterClassiEntityAllo;
        $this->masterClassification = $masterClassification;
        $this->masterClassificationOption = $masterClassificationOption;
        $this->user = $user;
        $this->masterLicences = $masterLicences;
        $this->companyUser = $companyUser;
        $this->masterState = $masterState;
        $this->masterCity = $masterCity;
        $this->masterCountry = $masterCountry;
        $this->master_data = $master_data;
        $this->request = $request;
        $this->licenseLocation = $licenseLocation;
        $this->payment = $payment;
        $this->company_card = $company_card;
    }

    /**
     * Display a listing of the appointments.
     *
     * @return index view
     */
    public function index()
    {
        //declare and initialize variables
        $entityType = app('App\Http\Controllers\Web\UserController')->getUserEntitiyType(Auth::user()->id);
        $mjBusinesses = $this->company->findWhere(array('entity_type' => 2));
        $companies = $this->company->findCompanyByEntity(array(3,4));
        $user = $this->user->find(Auth::user()->id, array("*"));
        $isMjDisabled = false; $isCompanyDisabled = false;

        // if MJ User
        if($entityType->id == Config::get('simplifya.MarijuanaBusiness')){
            $isMjDisabled = true;
            return view('appointment.index')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled, 'mjBusinesses' => $mjBusinesses, 'companies' => $companies, 'companyId'=>$user->company_id, 'type'=> 'mj', 'page_title' => 'Self-Audit Manager'));
        }
        // if Compliance company or Government entity
        else if($entityType->id == Config::get('simplifya.ComplianceCompany') || $entityType->id == Config::get('simplifya.GovernmentEntity')){
            $isCompanyDisabled = true;
            return view('appointment.index')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled, 'mjBusinesses' => $mjBusinesses, 'companies' => $companies, 'companyId'=>$user->company_id, 'type'=> 'cc', 'page_title' => 'Appointment Manager'));
        }
        // if supper admin
        else if($entityType->id == Config::get('simplifya.Simplifya')){
            return view('appointment.index')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled, 'mjBusinesses' => $mjBusinesses, 'companies' => $companies, 'companyId'=>$user->company_id, 'type'=> 'admin', 'page_title' => 'Appointment Manager'));
        }
    }


    /**
     * Search Appointments.
     *
     * @return json
     */
    public function getAllAppointments(){
        //declare and initialize variables
        $data = array();
        $fromDate = $_GET['fromDate'];
        $toDate= $_GET['toDate'];
        $mjBusiness= $_GET['mjBusiness'];
        $companyName= $_GET['companyName'];
        $status= $_GET['status'];
        $entityType= $_GET['entityType'];
        $thPartyAudit = $_GET['thPartyAudit'];
        $user = Auth::User();
        //$fromDate = ($fromDate != "") ? date('m-d-Y H:i:s', strtotime($fromDate)) : "";
        //$toDate = ($toDate != "") ? date('m-d-Y H:i:s', strtotime($toDate)) : "";
        $fromDate = ($fromDate != "") ? date('Y-m-d', strtotime($fromDate)) : "";
        $toDate = ($toDate != "") ? date('Y-m-d', strtotime($toDate)) : "";
        $status = '';// make status as empty due to the SWA-30 (in order to display canceled appointments which are appointment_status = 2)
        $appointments = $this->appointment->searchAppointments($fromDate, $toDate, $mjBusiness, $companyName, $status, $entityType, $thPartyAudit, $user);

        foreach($appointments as $appointment) {

            $mj = $this->company->find($appointment['to_company_id'],array("*"));
            $bs = $this->company->find($appointment['from_company_id'],array("*"));
            $inspector = $this->user->find($appointment['assign_to_user_id'],array("*"));
            $location = $this->location->find($appointment['company_location_id'], array("*"));

            $inspectionDate   = date('m/d/Y g:i a', strtotime(str_replace('/', '-', $appointment['inspection_date_time'])));

            $status = '';
            if ($appointment->appointment_status == 1) {
                $status = "<span class='badge badge-success'>Active</span>";
            }else if ($appointment->appointment_status == 2) {
                $status = "<span class='badge badge-danger'>Canceled</span>";
            }

            $editLink = "<a href='/appointment/create?manage=3&appointmentId=".$appointment['id']."' class='btn btn-info btn-circle' data-toggle='tooltip' title='View'><i class='fa fa-paste'></i></a>";

            $data[] = array(
                $inspectionDate,
                $mj->name,
                $bs->name,
                $inspector->name,
                $location->name,
                $status,
                $row[] = $editLink
            );
        }
        return response()->json(["data" => $data]);
    }


    /**
     * Create appointment from request view.
     * @param $req_details
     * @param $inspector_list
     * @param $entityType
     * @param $classifications
     * @param $auditType
     *
     * @return create view
     */
    private function createNewAppointmentFromRequest($req_details, $inspector_list, $entityType, $classifications,$auditType,$cc_ge_foc){
        //declare and initialize variables
        $mjcompany = $req_details[0]->marijuanaCompany->name;
        $mjcompany_id = $req_details[0]->marijuanaCompany->id;
        $location = $req_details[0]->companyLocation->name;
        $location_id = $req_details[0]->companyLocation->id;
        $inspector = $inspector_list[0]->user;
        $comment =  $req_details[0]->comment;

        $card_existed = $this->company_card->isCompanyCardAdded(Auth::user()->company_id);
        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        if($entityType==Config::get('simplifya.ComplianceCompany') || $entityType==Config::get('simplifya.GovernmentEntity')) {
            return view('appointment.create')->with(array(
                'page_title' => 'Create New Appointment',
                'company' => $mjcompany,
                'company_id' => $mjcompany_id,
                'location' => $location,
                'location_id' => $location_id,
                'inspector' => $inspector,
                'classifications' => $classifications,
                'auditType' => $auditType,
                'appointmentType' => 'create',
                'entity_type' => 'CC',
                'cc_data_added' => $cc_added,
                'cc_ge_foc'=>$cc_ge_foc
            ));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }
    /**
     * Create appointment from request view.
     * @param $req_details
     * @param $inspector_list
     * @param $entityType
     * @param $classifications
     * @param $auditType
     *
     * @return create view
     */
    private function createNewAppointmentNonMjb($req_details, $inspector_list, $entityType, $classifications,$auditType,$cc_ge_foc){
        //declare and initialize variables
        $mjcompany = $req_details->name;
        $mjcompany_id = $req_details->id;
        $location = $req_details->companyLocation[0]->name;
        $location_id = $req_details->companyLocation[0]->id;
        $inspector = $inspector_list[0]->user;

        $card_existed = $this->company_card->isCompanyCardAdded(Auth::user()->company_id);
        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        if($entityType==Config::get('simplifya.ComplianceCompany') || $entityType==Config::get('simplifya.GovernmentEntity')) {
            return view('appointment.create')->with(array(
                'page_title' => 'Create New Appointment',
                'company' => $mjcompany,
                'company_id' => $mjcompany_id,
                'location' => $location,
                'location_id' => $location_id,
                'inspector' => $inspector,
                'classifications' => $classifications,
                'auditType' => $auditType,
                'appointmentType' => 'create',
                'entity_type' => 'CC',
                'cc_data_added' => $cc_added,
                'cc_ge_foc'=>$cc_ge_foc,
                'hide_location'=>true
            ));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }


    /**
     * Create appointment from appointment view.
     * @param $inspector_list
     * @param $entityType
     * @param $auditType
     * @param $classifications
     * @param $company_id
     *
     * @return create view
     */
    private function createNewAppointmentFromAppointment($inspector_list, $entityType, $auditType, $classifications, $company_id, $group_id,$cc_ge_foc){
        $company_list = $this->company->findWhere(array('entity_type' => 2, 'status' => 2));
        $inspector = $inspector_list[0]->user;
        $user_id = Auth::user()->id;
        $card_existed = $this->company_card->isCompanyCardAdded($company_id);
        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        // if CC
        if($entityType==Config::get('simplifya.ComplianceCompany') && $group_id == Config::get('simplifya.CcMasterAdmin')){
            return view('appointment.create')->with(array(
                'page_title' => 'Create New Appointment',
                'company_list'=> $company_list,
                'auditType' => $auditType,
                'inspector' => $inspector,
                'classifications' => $classifications,
                'appointmentType' => 'create',
                'entity_type' => 'CC',
                'cc_data_added' => $cc_added,
                'cc_ge_foc'=>$cc_ge_foc,
                'manage'=>2
            ));
        }
        //if Government Entity
        else if($entityType==Config::get('simplifya.GovernmentEntity') && $group_id == Config::get('simplifya.GeMasterAdmin')){
            return view('appointment.create')->with(array(
                'page_title' => 'Create New Appointment',
                'company_list'=> $company_list,
                'auditType' => $auditType,
                'inspector' => $inspector,
                'classifications' => $classifications,
                'appointmentType' => 'create',
                'entity_type' => 'GE',
                'cc_data_added' => $cc_added,
                'cc_ge_foc'=>$cc_ge_foc,
                'manage'=>2
            ));
        }
        // if MJ Business
        else if(($entityType==Config::get('simplifya.MarijuanaBusiness') && $group_id == Config::get('simplifya.MjbMasterAdmin')) || ($entityType==Config::get('simplifya.MarijuanaBusiness') && $group_id == Config::get('simplifya.MjbManager'))){
            $companyDetails = $this->company->find($company_id, array("*"));
            if($group_id == Config::get('simplifya.MjbMasterAdmin')) {
                $companyLocations = $this->location->findWhere(array('company_id' => $companyDetails->id, 'status' => 1));
            } else {
                $companyLocations = $this->company->getUserLocations($user_id);
            }

            return view('appointment.create')->with(array(
                'page_title' => 'Create New Appointment',
                'company' => $companyDetails->name,
                'company_id' => $companyDetails->id,
                'company_locations' => $companyLocations,
                'classifications' => $classifications,
                'auditType' => $auditType,
                'appointmentType' => 'create',
                'entity_type' => 'MJ',
                'cc_data_added' => $cc_added,
                'cc_ge_foc'=>$cc_ge_foc
            ));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }


    /**
     * Edit appointment from appointment view.
     * @param $inspector_list
     * @param $entityType
     * @param $auditType
     * @param $classifications
     *
     * @return create view
     */
    private function editAppointment($inspector_list, $entityType, $auditType, $classifications,$cc_ge_foc){
        //declare and initialize variables
        $appointmentId = $_GET['appointmentId'];
        $appointment = $this->appointment->find($appointmentId, array("*"));
        $company = $this->company->find($appointment->to_company_id, array("*"));
        $location = $this->location->find($appointment->company_location_id, array("*"));
        $inspector = $inspector_list[0]->user;
        $licence = $this->appointmentClassification->where('appointment_id', $appointmentId)->where('entity_type', 'LICENCE')->get();
        $masterLicences = $this->masterLicences->findWhere(array('status' => 1));

        $selectedInspector = $this->user->find($appointment->assign_to_user_id, array("*"));

        $type = $appointment->from_company_id == $appointment->to_company_id ? 'in_house' : 'third_party';

        $card_existed = $this->company_card->isCompanyCardAdded(Auth::user()->company_id);

        if(isset($card_existed[0])){
            $cc_added = 1;
        }else{
            $cc_added = 0;
        }

        $selectedClassifications = $this->appointmentClassification
            ->where('appointment_id', $appointmentId)
            ->where('entity_type', '!=', 'AUDIT_TYPE')
            ->where('entity_type', '!=', 'COUNTRY')
            ->where('entity_type', '!=', 'STATE')
            ->where('entity_type', '!=', 'CITY')
            ->where('entity_type', '!=', 'LICENCE')
            ->get();

        // if MJ Business
        if($entityType==Config::get('simplifya.MarijuanaBusiness')){
            if($appointment->from_company_id == Auth::user()->company_id || $appointment->to_company_id == Auth::user()->company_id){
                return view('appointment.create')->with(array(
                    'page_title' =>'VIEW AUDIT REPORT',
                    'company' => $company->name,
                    'company_id' => $company->id,
                    'location' => $location->name,
                    'location_id' => $location->id,
                    'inspector' => $inspector,
                    'inspector_id' => $appointment->assign_to_user_id,
                    'classifications' => $classifications,
                    'comment' => $appointment->comment,
                    'reportStatus' => $appointment->report_status,
                    'dateTime' => $appointment->inspection_date_time,
                    'licenceTypes' => $licence,
                    'selectedClassifications' => $selectedClassifications,
                    'masterLicences' => $masterLicences,
                    'cost' => $appointment->amount,
                    'from_company_id' => $appointment->from_company_id,
                    'edit' => true,
                    'appointmentStatus' => $appointment->appointment_status,
                    'auditType' => $auditType,
                    'type' => $type,
                    'userType' => 'MJB',
                    'inspectorName' => $selectedInspector->name,
                    'cc_data_added' => $cc_added,
                    'cc_ge_foc'=>$cc_ge_foc

                ));
            }
            else{
                $message =  Config::get('messages.ACCESS_DENIED');
                return Redirect::to("/dashboard")->with('error', $message);
            }
        }
        // if CC or Government Entity
        else if($entityType==Config::get('simplifya.ComplianceCompany') || $entityType==Config::get('simplifya.GovernmentEntity')){
            if($appointment->from_company_id == Auth::user()->company_id){
                return view('appointment.create')->with(array(
                    'page_title' =>'VIEW AUDIT REPORT',
                    'company' => $company->name,
                    'company_id' => $company->id,
                    'location' => $location->name,
                    'location_id' => $location->id,
                    'inspector' => $inspector,
                    'inspector_id' => $appointment->assign_to_user_id,
                    'classifications' => $classifications,
                    'comment' => $appointment->comment,
                    'reportStatus' => $appointment->report_status,
                    'dateTime' => $appointment->inspection_date_time,
                    'licenceTypes' => $licence,
                    'selectedClassifications' => $selectedClassifications,
                    'masterLicences' => $masterLicences,
                    'cost' => $appointment->amount,
                    'edit' => true,
                    'appointmentStatus' => $appointment->appointment_status,
                    'auditType' => $auditType,
                    'type' => $type,
                    'userType' => '',
                    'inspectorName' => $selectedInspector->name,
                    'cc_data_added' => $cc_added,
                    'cc_ge_foc'=>$cc_ge_foc
                ));
            }
            else{
                $message =  Config::get('messages.ACCESS_DENIED');
                return Redirect::to("/dashboard")->with('error', $message);
            }
        }
        // if master Admin
        else if($entityType==Config::get('simplifya.Simplifya')){
            return view('appointment.create')->with(array(
                'page_title' =>'View Appointment',
                'company' => $company->name,
                'company_id' => $company->id,
                'location' => $location->name,
                'location_id' => $location->id,
                'inspector' => $inspector,
                'inspector_id' => $appointment->assign_to_user_id,
                'classifications' => $classifications,
                'comment' => $appointment->comment,
                'reportStatus' => $appointment->report_status,
                'dateTime' => $appointment->inspection_date_time,
                'licenceTypes' => $licence,
                'selectedClassifications' => $selectedClassifications,
                'masterLicences' => $masterLicences,
                'cost' => $appointment->amount,
                'appointmentStatus' => $appointment->appointment_status,
                'auditType' => $auditType,
                'type' => $type,
                'userType' => 'MasterAdmin',
                'inspectorName' => $selectedInspector->name,
                'cc_data_added' => $cc_added,
                'cc_ge_foc'=>$cc_ge_foc
            ));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }


    /**
     * Create appointment view.
     *
     * @return create view
     */
    public function createAppointment(){
        //declare and initialize variables
        $company_id = Auth::user()->company_id;
        $group_id = Auth::user()->master_user_group_id;
        $cc_ge_foc=$this->master_data->findWhere(array('name' => 'CC_GE_FREE_CHECKLIST'))->first()->value;


        $company_type = $this->company->getCompany($company_id);
        $entityType = $company_type[0]->entity_type;

        $classifications = $this->findClassifications($entityType);
        $auditType = 0;
        \Log::info(print_r($company_type,true));

        if($entityType ==3 || $entityType ==4){
            $auditType = 2;
        }
        else if($entityType==2){
            $auditType = 1;
        }

        if(isset($_GET['manage']))
        {
            // Create new appointment from request
            if($_GET['manage']== 1)
            {
                //change request status on make appointment button click
                $this->request->update(array('status' => 1), $_GET['id']);

                $request_id = $_GET['id']; //request id
                $id = $request_id;
                $req_details = $this->inspection->getRequestMJBCompanies($id);

                $location_id = $req_details[0]->companyLocation->id;

                $inspector_list = $this->company->getAllInspectors($company_id, $entityType, $location_id);

                return $this->createNewAppointmentFromRequest($req_details, $inspector_list, $entityType, $classifications, $auditType,$cc_ge_foc);
            }
            // create new appointment
            else if($_GET['manage']==2){
                $inspector_list = $this->company->getAllInspectors($company_id, $entityType, 0);
                return $this->createNewAppointmentFromAppointment($inspector_list, $entityType, $auditType, $classifications, $company_id, $group_id,$cc_ge_foc);
            }
            // edit an appointment
            else if($_GET['manage']==3){
                $inspector_list = $this->company->getAllInspectorsEdit($company_id, $entityType, 0, $auditType);
                return $this->editAppointment($inspector_list, $entityType, $auditType, $classifications,$cc_ge_foc);
            }else if($_GET['manage']==4){
                $manage_id=$_GET['manage'];
                $request_id = $_GET['id']; //request id
                $id = $request_id;
                $req_details = $this->company->getNonMJBCompany($id);
                $location_id = $req_details->companyLocation[0]->id;

                $inspector_list = $this->company->getAllInspectors($company_id, $entityType, $location_id);

                return $this->createNewAppointmentNonMjb($req_details, $inspector_list, $entityType, $classifications, $auditType,$cc_ge_foc);
            }
        }
    }
    /**
     * Create appointment view.
     *
     * @return create view
     */
    public function createAppointmentForNonMjb($company_id=null){
        if(isset($company_id)){
            $companyId=$company_id;
        }else{
            $companyId=0;
        }

        //declare and initialize variables
        return response()->view('appointment.addNonExistingMjb',array('company_id' => $companyId, 'page_title' => 'External Audit Manager'),200)
            ->header('Cache-Control','no-store, no-cache, must-revalidate');
    }

    /**
     * Return Countries for non mjb.
     *
     * @return array
     */
    public function getCountriesForNonMjb(){
        $countries = $this->masterCountry->all(array('*'));
        return response()->json(array('success' => 'true','data' => $countries), 200);
    }


    /**
     * Return States of non mjb for give country.
     * @param $countryId
     * @return array
     */
    public function getStatesForNonMjb(Request $request){
        $country_id=$request->countryId;
        $states = $this->masterCountry
            ->with([
                'masterStates' => function ($query){
                    $query->orderBy('name', 'ASC');
                }
            ])->find($country_id, array('*'));

        return response()->json(array('success' => 'true', 'data' => $states), 200);
    }

    /**
     * Return States of non mjb for give country.
     * @param $countryId
     * @return array
     */
    public function getCitiesForNonMjb(Request $request){
        $state_id=$request->stateId;
        $cities = $this->masterState
            ->with([
                'masterCity' => function ($query){
                    $query->orderBy('name', 'ASC');
                    $query->where('status', '=', 1);
                    $query->orderBy('name', 'ASC');
                }
            ])
            ->find($state_id, array('*'));
        return response()->json(array('success' => 'true', 'data' => $cities), 200);
    }
    /**
     * Return States of non mjb for give country.
     * @param $countryId
     * @return array
     */
    public function getLicensesForNonMjb(Request $request){
        $state_id=$request->stateId;
        if ($state_id != 0) {
            $licences = $this->masterState->with([
                'masterLicense' => function ($query) {
                    $query->where('status', '=', 1);
                }
            ])->find($state_id, array('*'));
        } else {
            $licences = $this->masterLicences->all(array("*"));
        }

        return Response()->json(array('success' => 'true', 'data' => $licences), 200);
    }

    /**
     * Return Non mjb details.
     * @param $countryId
     * @return array
     */
    public function getNonMjbDetails($company_id){
        try {
            list($company, $companyDetails) = array_values($this->company->getNonMJBCompanyWithLicense($company_id));

            $tempLicense=[];
            $companyLicenses =[];
            $company_new =[];
            foreach ($companyDetails as $companyDetail){
                $tempLicense['license']=$companyDetail->id;
                $tempLicense['licen_no']=$companyDetail->license_number;
                $tempLicense['license_id']=$companyDetail->license_id;
                array_push($companyLicenses,$tempLicense);
            }
                $company_new['company_id']=$company[0]->company_id;
                $company_new['company_location_id']=$company[0]->id;
                $company_new['name_of_business']=$company[0]->name;
                $company_new['address_line_1']=$company[0]->address_line_1;
                $company_new['address_line_2']=$company[0]->address_line_2;
                $company_new['state']=$company[0]->states_id;
                $company_new['city']=$company[0]->city_id;
                $company_new['contact_person']=$company[0]->contact_person;
                $company_new['country']=$company[0]->country_id;
                $company_new['zip_code']=$company[0]->zip_code;
                $company_new['phone_no']=$company[0]->phone_number;
                $company_new['contact_email']=$company[0]->contact_email;
            if (count($companyDetails)) {

                $company_new['choices'] = $companyLicenses;
            }
            return response()->json(array('success' => 'true', 'data' => array('company' => $company_new) ));
        }catch (\Exception $e) {
            \Log::debug("error retrieving coupon data " + $e->getMessage());
            \Log::debug($e->getTraceAsString());
            return response()->json(array('success' => 'false', 'message' => $e->getMessage()));
        }
    }


    /**
     * Find classifications.
     * @param $entityType
     *
     * @return array
     */
    private function findClassifications($entityType){
        $allocations = $this->masterClassiEntityAllo->findWhere(array('entity_type_id' => $entityType));
        $data = array();
        foreach($allocations as $key => $allocation){
            $allocData = $this->masterClassification->findWhere(array('id' => $allocation->classification_id))->first();
            if($allocData){
                $masterArray = array('id' =>$allocData->id, 'name' => $allocData->name, 'status' => $allocData->status);
                $options = $this->masterClassificationOption->where('classification_id', '=', $allocData->id)->get();
                $optionData[] = array();
                foreach($options as $index => $option){
                    $optionData = array('id' =>$option->id, 'name' => $option->name, 'status' => $option->status);
                    $masterArray['options'][$index] = $optionData;
                }
                array_push($data,$masterArray);
            }

        }
        return $data;
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
     * Store a newly created Appointment.
     * @param  $request
     *
     * @return json
     */
    public function store(AppointmentCreateRequest $request)
    {
        //generate random number
        $inspectionNo = rand(1000, 5000);
        $inspectionDateTime = date('Y-m-d H:i', strtotime($request->startDate));

        //declare and initialize variables
        $request_id = $request->request_id;
        $appointment_type = $request->appointment_type;
        $audit_type = $request->audit_type;
        $location = $request->company_location;
        $type = $request->license_types;
        $classifications = $request->classifications;
        $isFOC=$request->isFOC;
        $license_type = array();

        if ($type)
        {
            foreach ($type as $value)
            {
                array_push($license_type,$value);
            }
        }

        $dataset = array(
            'to_company_id'          => $request->to_company_id,
            'from_company_id'        => $request->from_company_id,
            'company_location_id'    => $location,
            'assign_to_user_id'      => $request->assign_to,
            'comment'                => $request->comment,
            'inspection_date_time'   => $inspectionDateTime,
            'amount'                 => $request->amount_cost,
            'inspection_number'      => $inspectionNo,
            'appointment_status'     => 1,
            'report_status'          => 0,
            'share_mjb'          => ($audit_type == 1) ? 1 : 0,
            'created_by'             => Auth::user()->id,
            'updated_by'             => Auth::user()->id
        );

        $get_questions = $this->selectAllQuestions($audit_type, $location, $license_type, $classifications);

        // if no questions found
        if(count($get_questions['questions']) == 0){
            $message = Config::get('messages.APPOINTMENT_NO_QUESTIONS');
            return response()->json(array('success' => 'true', 'message'=> $message, 'type' =>'not_found'));
        }
        else{
            // Start DB transaction
            DB::beginTransaction();
            try{
                // payment amount check with backend
                $amount = ($isFOC=='true'?0:$this->validateLicenceAmount($license_type, $audit_type));

                if($amount == $request->amount_cost){
                    //create new appointment
                    $save_appointment = $this->appointment->createAppointment($dataset);
                    if($save_appointment){

                        $this->createAppoinmentClassifications($get_questions['dataset'], $save_appointment->id, $license_type);
                        $this->createAppointmentQuestions($get_questions['questions'], $save_appointment->id);

                        $fromCompany = $this->company->find($request->from_company_id);
                        $toCompany = $this->company->find($request->to_company_id);
                        $companyLocation = $this->location->find($location);
                        $city = $this->masterCity->find($companyLocation->city_id);
                        $state = $this->masterState->find($companyLocation->states_id);
                        $country = $this->masterCountry->find($state->country_id);

                        if($request->amount_cost != 0){
                            $payment = app('App\Http\Controllers\Web\PaymentController')->paymentCommenHandler($request->amount_cost, Auth::user()->company_id, "Appointment", "USD");
                            if($payment['success'] == "true"){
                                $data = array('payment_id' => $payment['paymentId']);
                                $this->appointment->update($data, $save_appointment->id);
                                DB::commit();

                                // email send to MJ Business
                                $emails = array();
                                // find master admins of to companies
                                $users = $this->user->findWhere(array("company_id" => $request->to_company_id, "master_user_group_id" => Config::get('simplifya.MjbMasterAdmin')));
                                foreach($users as $user){
                                    array_push($emails, $user->email);
                                }


                                // email send to inspector
                                $inspector = $this->user->find($request->assign_to);
                                $inspectionDate   = date('m/d/Y', strtotime(str_replace('/', '-', $save_appointment->inspection_date_time)));
                                $inspectionTime   = date('g:i a', strtotime(str_replace('/', '-', $save_appointment->inspection_date_time)));

                                // If appointment from request
                                if($appointment_type == 1){
                                    // Send email
                                    $mail = new sendMail;
                                    $mail->mailSender('emails.mjb_appointment_request',
                                        $emails,
                                        Config::get('simplifya.COMPANY'),
                                        'Your Audit Request has been confirmed',
                                        array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                            'system' => 'Simplifya',
                                            'entity_type' => 'Compliance Company',
                                            'from_company' => $fromCompany->name,
                                            'to_company' => $toCompany->name,
                                            'location_name' => $companyLocation->name,
                                            'address_line_1' => $companyLocation->address_line_1,
                                            'address_line_2' => $companyLocation->address_line_2,
                                            'city' => $city->name,
                                            'state' => $state->name,
                                            'country' => $country->name,
                                            'comment' => $save_appointment->comment,
                                            'date_time' => $save_appointment->inspection_date_time,
                                            'company' => Config::get('simplifya.COMPANY'),
                                            'assign_to' => $inspector->name,
                                            'inspection_Date' => $inspectionDate,
                                            'inspection_Time' => $inspectionTime,
                                            'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                                        )
                                    );
                                }
                                else{
                                    // Send email
                                    $mail = new sendMail;
                                    $mail->mailSender('emails.mjb_appointment',
                                        $emails,
                                        Config::get('simplifya.COMPANY'),
                                        'Audit Appointment',
                                        array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                            'system' => 'Simplifya',
                                            'entity_type' => 'Compliance Company',
                                            'from_company' => $fromCompany->name,
                                            'to_company' => $toCompany->name,
                                            'location_name' => $companyLocation->name,
                                            'address_line_1' => $companyLocation->address_line_1,
                                            'address_line_2' => $companyLocation->address_line_2,
                                            'city' => $city->name,
                                            'state' => $state->name,
                                            'country' => $country->name,
                                            'comment' => $save_appointment->comment,
                                            'date_time' => $save_appointment->inspection_date_time,
                                            'company' => Config::get('simplifya.COMPANY'),
                                            'assign_to' => $inspector->name,
                                            'inspection_Date' => $inspectionDate,
                                            'inspection_Time' => $inspectionTime,
                                            'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                                        )
                                    );
                                }



                                // Send email
                                $mail = new sendMail;
                                $mail->mailSender('emails.inspector_appointment',
                                    $inspector->email,
                                    Config::get('simplifya.COMPANY'),
                                    'You’ve been assigned an audit',
                                    array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                        'system' => 'Simplifya',
                                        'entity_type' => 'Compliance Company',
                                        'to_company' => $toCompany->name,
                                        'location_name' => $companyLocation->name,
                                        'address_line_1' => $companyLocation->address_line_1,
                                        'address_line_2' => $companyLocation->address_line_2,
                                        'city' => $city->name,
                                        'state' => $state->name,
                                        'country' => $country->name,
                                        'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                                        'comment' => $save_appointment->comment,
                                        'date_time' => $save_appointment->inspection_date_time,
                                        'inspection_Date' => $inspectionDate,
                                        'inspection_Time' => $inspectionTime,
                                        'company' => Config::get('simplifya.COMPANY'),
                                    )
                                );


                                // Send push notifications
                                $this->sendCreateAppointmentPushNotification($save_appointment->id, $fromCompany->name);

                                $message = Config::get('messages.APPOINTMENT_ADD_SUCCESS');
                                return response()->json(array('success' => 'true', 'message'=> $message, 'type' =>'found'));
                            }
                            else{
                                DB::rollback();
                                if (isset($payment['message'])) {
                                    $message = $payment['message'];
                                }else {
                                    $message = Config::get('messages.APPOINTMENT_ADD_FAILED');
                                }
                                return array('success' => 'false', 'message'=> $message);
                            }
                        }
                        else{
                            $data = array('payment_id' => 0);
                            $this->appointment->update($data, $save_appointment->id);
                            DB::commit();

                            // email send to inspector
                            $inspector = $this->user->find($request->assign_to);

                            $inspectionDate   = date('m/d/Y', strtotime(str_replace('/', '-', $save_appointment->inspection_date_time)));
                            $inspectionTime   = date('g:i a', strtotime(str_replace('/', '-', $save_appointment->inspection_date_time)));

                            // Send email
                            $mail = new sendMail;
                            $mail->mailSender('emails.inspector_appointment',
                                $inspector->email,
                                Config::get('simplifya.COMPANY'),
                                'You’ve been assigned an audit',
                                array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                    'system' => 'Simplifya',
                                    'entity_type' => 'Compliance Company',
                                    'to_company' => $toCompany->name,
                                    'location_name' => $companyLocation->name,
                                    'address_line_1' => $companyLocation->address_line_1,
                                    'address_line_2' => $companyLocation->address_line_2,
                                    'city' => $city->name,
                                    'state' => $state->name,
                                    'country' => $country->name,
                                    'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                                    'comment' => $save_appointment->comment,
                                    'date_time' => $save_appointment->inspection_date_time,
                                    'inspection_Date' => $inspectionDate,
                                    'inspection_Time' => $inspectionTime,
                                    'company' => Config::get('simplifya.COMPANY'),
                                )
                            );

                            if ($audit_type == 1) {

                                $companyUser = $this->user->findBy('company_id', $toCompany->id);
                                $simplifya_name = Config::get('messages.COMPANY_NAME');

                                //send mjb-in-house-audit-support-email (company name, your_name, email)
                                $mjb_support_data = new \stdClass();
                                $mjb_support_data->name = $companyUser->name;
                                $mjb_support_data->email = $companyUser->email;
                                $mjb_support_data->businessName = $toCompany->name;
                                $mjb_support_data->reg_no = $toCompany->reg_no;
                                $mjb_support_data->companyname = $simplifya_name;
                                $mjb_support_data->layout = 'emails.mjb_in_house_audit_made_support';
                                $mjb_support_data->subject = 'NEW SELF-AUDIT - '. $toCompany->name;
                                $mjb_support_data->to_company = $toCompany->name;
                                $mjb_support_data->location_name = $companyLocation->name;
                                $mjb_support_data->address_line_1 = $companyLocation->address_line_1;
                                $mjb_support_data->address_line_2 = $companyLocation->address_line_2;
                                $mjb_support_data->city = $city->name;
                                $mjb_support_data->state = $state->name;
                                $mjb_support_data->country = $country->name;
                                $mjb_support_data->zip_code = ($companyLocation->zip_code == null)?'':$companyLocation->zip_code;
                                $mjb_support_data->contact = isset($companyLocation->phone_number)? $companyLocation->phone_number : null;
                                $mjb_support_data->comment = $save_appointment->comment;
                                $mjb_support_data->date_time = $save_appointment->inspection_date_time;
                                $mjb_support_data->inspection_Date = $inspectionDate;
                                $mjb_support_data->inspection_Time = $inspectionTime;
                                $mjb_support_data->inspector_name = $inspector->name;
                                $mjb_support_data->inspector_email = $inspector->email;

                                // Fire mjb sign up support event
                                event(new MjbInHouseAuditSupport( $mjb_support_data ));
                            }

                            // Send push notifications
                            $this->sendCreateAppointmentPushNotification($save_appointment->id, $fromCompany->name);

                            $message = Config::get('messages.APPOINTMENT_ADD_SUCCESS');
                            return response()->json(array('success' => 'true', 'message'=> $message, 'type' =>'found'));
                        }
                    }
                    else{
                        DB::rollback();
                        $message = Config::get('messages.APPOINTMENT_ADD_FAILED');
                        return array('success' => 'false', 'message'=> '1');
                    }
                }
                else{
                    DB::rollback();
                    $message = Config::get('messages.APPOINTMENT_ADD_FAILED');
                    return array('success' => 'false', 'message'=> '2');
                }
            }
            catch(exception $ex){
                DB::rollback();
                $message = Config::get('messages.APPOINTMENT_ADD_FAILED');
                return array('success' => 'false', 'message'=> '3');

            }
        }
    }

    /**
     * Send push notifications when commented on action items
     * @param $users
     * @param $action_item_id
     * @param $appointment_id
     * @param $user_name
     * @return array|null
     */
    public function sendCreateAppointmentPushNotification($appointment_id, $from_company_name){

        $users = $this->appointment->getAppointmentNotifiedUsers($appointment_id);

        // Send push notifications
        $data_pushnotif = new \stdClass();
        $data_pushnotif->users = array_values($users);
        $data_pushnotif->appointment_id = $appointment_id;
        $data_pushnotif->from_company_name = $from_company_name;

        return $status = event(new AddAppointmentNotifRequest($data_pushnotif));
    }


    /**
     * Validate licence amount
     * @param $audit_type
     * @param $license_type
     *
     * @return decimal
     */
    private function validateLicenceAmount($license_type, $audit_type){

        $validateAmounts = $this->licenseLocation->getLicenseAmount($license_type, $audit_type);
        $amount = 0;
        if($audit_type == 1){
            foreach ($validateAmounts as $validateAmount){
                $amount += $validateAmount->checklist_fee_inhouse;
            }
        }
        else{
            foreach ($validateAmounts as $validateAmount){
                $amount += $validateAmount->checklist_fee;
            }
        }

        return $amount;
    }


    /**
     * Search all Questions.
     * @param  $audit_type
     * @param  $location
     * @param  $license_type
     *
     * @return array
     */
    private function selectAllQuestions($audit_type, $location, $license_type, $classifications)
    {
        //search location details
        $getLocation = $this->location->getLocationByID($location);
        $dataset = array(
                'AUDIT_TYPE'    => $audit_type,
                'COUNTRY'       => $getLocation->masterStates->masterCountry->id,
                'STATE'         => $getLocation->masterStates->id,
                'CITY'          => $getLocation->masterCity->id,

                );

        if(!empty($classifications)){
            foreach($classifications as $classification){
                $dataset[$classification["classificationId"]] = $classification["value"];
            }
        }


        $getList = $this->questionClassification->getAllQuestionsList($dataset, $license_type);
        $questions = $this->question->findQuestionArray($getList);
        return array('getList' => $getList, 'questions' => $questions, 'dataset' => $dataset);
    }


    /**
     * Create Appointment Classifications.
     * @param  $dataset
     * @param  $appointmentId
     * @param  $license_type
     *
     * @return boolean
     */
    public function createAppoinmentClassifications($dataset, $appointmentId, $license_type){
        if(!empty($dataset)){
            foreach($dataset as $key => $value){
                $data = array(
                    'appointment_id' => $appointmentId,
                    'entity_type' => $key,
                    'option_value' => $value,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                    );
                $this->appointmentClassification->create($data);
            }
        }
        if(!empty($license_type)){
            foreach($license_type as $type){
                $data = array(
                    'appointment_id' => $appointmentId,
                    'entity_type' => 'LICENCE',
                    'option_value' => $type,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                );
                $this->appointmentClassification->create($data);
            }
        }

        return true;
    }


    /**
     * Create Appointment questions.
     * @param  $questions
     * @param  $appointmentId
     *
     * @return boolean
     */
    public function createAppointmentQuestions($questions, $appointmentId){
        if(!empty($questions)){
            foreach($questions as $question){
                $data = array(
                    'question_id' => $question->id,
                    'appointment_id' => $appointmentId,
                    'parent_question_id' => $question->parent_question_id,
                    'supper_parent_question_id' => $question->supper_parent_question_id,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                );
                $this->appointmentQuestion->create($data);
            }
            return true;
        }
    }


    /**
     * Update the appointment
     *
     * @return json
     */
    public function update()
    {
        //declare and initialize variables
        $appointmentId = $_POST['appointmentId'];
        $inspectorId = $_POST['inspectorId'];
        $date = date('y-m-d H:i', strtotime($_POST['date']));

        $data = array('assign_to_user_id' => $inspectorId, 'inspection_date_time' => $date);

        $response = $this->appointment->update($data,$appointmentId);
        if($response){
            return Response()->json(array('success' => 'true'), 200);
        }
        else{
            return Response()->json(array('success' => 'false'), 400);
        }
    }


    /**
     * Get Assign to users
     *
     * @return Json
     */
    public function getAssignTo(){
        //declare and initialize variables
        $locationId = $_GET['location_id'];
        $userCompanyId = Auth::user()->company_id;
        $company =  $this->company->find($userCompanyId);

        $inspector_list = $this->company->getAllInspectors($userCompanyId, $company->entity_type, $locationId);
        if($inspector_list){
            return Response()->json(array('success' => 'true', 'data' => $inspector_list[0]->user, 'entity_type' => $company->entity_type), 200);
        }
    }

    /**
     * Cancel appointment
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelAppointment(Request $request) {

        $company_id = Auth::user()->company_id;
        $group_id = Auth::user()->master_user_group_id;

        $company_type = $this->company->getCompany($company_id);
        $entityType = $company_type[0]->entity_type;

        $appointmentId = $request->input('appointmentId');
        // master_license_type_subscriptions
        $appointment = $this->appointment->find($appointmentId, array("*"));
        $fromCompany = $this->company->find($appointment->from_company_id);
        $toCompany = $this->company->find($appointment->to_company_id);
        $companyLocation = $this->location->find($appointment->company_location_id);
        $appointmentType = $this->appointmentClassification->where('appointment_id', $appointmentId)->where('entity_type', 'AUDIT_TYPE')->first();
        $city = $this->masterCity->find($companyLocation->city_id);
        $state = $this->masterState->find($companyLocation->states_id);
        $country = $this->masterCountry->find($state->country_id);

        $isRefundApplicable = false;

        if ($appointment->payment_id != 0) {
            $payment = $this->payment->find($appointment->payment_id);
            if (isset($payment)) {
                // if payment status is success
                if ($payment->tx_status == 1) {
                    $isRefundApplicable = true;
                    //refund appointment charge amount!
                    $refund = app('App\Http\Controllers\Web\PaymentController')->returnAppointmentPaymentFee($payment->id, $company_id);
                    if($refund['success'] == 'true') {
                        // Set appointment status to 2 which means canceled
                        $response = $this->appointment->update(['appointment_status' => 2],$appointmentId);
                        if (!$response) {
                            return Response()->json(array('success' => 'false'), 400);
                        }
                    } else {
                        return Response()->json(array('success' => 'false'), 400);
                    }
                }
            }
        }else {
            $response = $this->appointment->update(['appointment_status' => 2],$appointmentId);
            if (!$response) {
                return Response()->json(array('success' => 'false'), 400);
            }
        }

        // email send to inspector
        $inspector = $this->user->find($appointment->assign_to_user_id);
        $inspectionDate   = date('m/d/Y', strtotime(str_replace('/', '-', $appointment->inspection_date_time)));
        $inspectionTime   = date('g:i a', strtotime(str_replace('/', '-', $appointment->inspection_date_time)));


        if ($appointmentType->option_value != 1) {

            //todo if appointment type != 1 send cancellation emails to
            // $users (must take from appointment classification table)
            // find master admins of the companies
            $emails = array();
            $users = $this->user->findWhere(array("company_id" => $appointment->to_company_id, "master_user_group_id" => Config::get('simplifya.MjbMasterAdmin')));
            foreach($users as $user){
                array_push($emails, $user->email);
            }
            //mjb-super-admin - cancel email
            $this->sendAppointmentCancellationEmail($emails,
                array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                    'system' => 'Simplifya',
                    'entity_type' => 'Compliance Company',
                    'assign_to' => $inspector->name,
                    'to_company' => $toCompany->name,
                    'from_company' => $fromCompany->name,
                    'location_name' => $companyLocation->name,
                    'address_line_1' => $companyLocation->address_line_1,
                    'address_line_2' => $companyLocation->address_line_2,
                    'city' => $city->name,
                    'state' => $state->name,
                    'country' => $country->name,
                    'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                    'comment' => $appointment->comment,
                    'date_time' => $appointment->inspection_date_time,
                    'inspection_Date' => $inspectionDate,
                    'inspection_Time' => $inspectionTime,
                    'company' => Config::get('simplifya.COMPANY'),
                ),
                'Audit Cancellation Notice',
                'emails.mjb_appointment_cancelled');
        }

        $emails = array();
        $users = $this->user->getAllMasterAdminUsersBy($appointment->from_company_id,
            array(
                Config::get('simplifya.CcMasterAdmin'),//5
                Config::get('simplifya.GeMasterAdmin'),//7
                Config::get('simplifya.MjbMasterAdmin')//2
            ));
        foreach($users as $user){
            array_push($emails, $user->email);
        }

        //todo  send cancel email to from_company_id = $appointment->from_company_id and master_user_group_id = 5 | 7 | 2
        //compliance/govt-entity/mjb cancel email
        // send inspector appointment cancellation email
        $this->sendAppointmentCancellationEmail(
            $emails,
            array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                'system' => 'Simplifya',
                'entity_type' => 'Compliance Company',
                'to_company' => $toCompany->name,
                'assign_to' => $inspector->name,
                'from_company' => $fromCompany->name,
                'location_name' => $companyLocation->name,
                'address_line_1' => $companyLocation->address_line_1,
                'address_line_2' => $companyLocation->address_line_2,
                'city' => $city->name,
                'state' => $state->name,
                'country' => $country->name,
                'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                'comment' => $appointment->comment,
                'date_time' => $appointment->inspection_date_time,
                'inspection_Date' => $inspectionDate,
                'inspection_Time' => $inspectionTime,
                'company' => Config::get('simplifya.COMPANY'),
            ),
            'Audit Cancellation Notice',
            'emails.cc_ge_appointment_cancelled'
        );


        // send inspector appointment cancellation email
        $this->sendAppointmentCancellationEmail(
            $inspector->email,
            array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                'system' => 'Simplifya',
                'entity_type' => 'Compliance Company',
                'assign_to' => $inspector->name,
                'to_company' => $toCompany->name,
                'from_company' => $fromCompany->name,
                'location_name' => $companyLocation->name,
                'address_line_1' => $companyLocation->address_line_1,
                'address_line_2' => $companyLocation->address_line_2,
                'city' => $city->name,
                'state' => $state->name,
                'country' => $country->name,
                'zip_code' => ($companyLocation->zip_code == null)?'':$companyLocation->zip_code,
                'comment' => $appointment->comment,
                'date_time' => $appointment->inspection_date_time,
                'inspection_Date' => $inspectionDate,
                'inspection_Time' => $inspectionTime,
                'company' => Config::get('simplifya.COMPANY'),
            ),
            'Audit Cancellation Notice',
            'emails.inspector_appointment_cancelled'
        );

        if ($isRefundApplicable) {
            //todo send appointment cancel email notification with refund info
            //todo send refund email to from_company_id = $appointment->from_company_id and master_user_group_id = 5 | 7
            // cc/ge - master admin
        }

        return Response()->json(array('success' => 'true'), 200);
    }

    /**
     * Send appointment cancellation email to inspector
     * @param $email
     * @param $data
     * @param $subject
     * @param $emailTemplate
     */
    private function sendAppointmentCancellationEmail($email, $data, $subject, $emailTemplate) {

        $emails = array();
        if (is_array($email)) {
            $emails = $email;
        }else {
            $emails[] = $email;
        }

        $mail = new sendMail;
        $mail->mailSender(
            $emailTemplate,
            $emails,
            Config::get('simplifya.COMPANY'),
            $subject,
            $data
        );
    }
}
