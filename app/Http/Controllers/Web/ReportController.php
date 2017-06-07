<?php

namespace App\Http\Controllers\Web;

use App\Repositories\AppointmentActionItemCommentsRepository;
use App\Repositories\MasterUserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\AppointmentIDRequest;
use App\Http\Requests\AssginUsersRequest;
use App\Http\Requests\ActionCommentRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Aws\Laravel\AwsFacade as AWS;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\AppointmentRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\UsersRepository;
use App\Repositories\AppointmentClassificationRepository;
use App\Repositories\EntityTypeRepository;
use App\Repositories\AppointmentQuestionRepository;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\QuestionAnswerRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\MasterAnswerValueRepository;
use App\Repositories\CompanyUserRepository;
use App\Repositories\AppointmentActionItemUsersRepository;
use App\Repositories\AppointmentCommentsNotifyUsersRepository;
use App\Repositories\AppointmentActionItemClosedRepository;
use App\Repositories\QuestionActionItemRepository;
use App\Repositories\UploadRepository;
use App\Repositories\MasterEntityTypeRepository;
use App\Repositories\MasterCountryRepository;
use App\Repositories\MasterCityRepository;
use App\Repositories\MasterStateRepository;
use App\Repositories\QuestionCitationsRepository;
use Illuminate\Support\Facades\Session;
use App\Lib\sendMail;
use App\Events\AssignUserNotifRequest;
use App\Events\AddCommentNotifRequest;
use App\Lib\CsvGenerator;
use App\Lib\PdfReportGenerator;
use App\Lib\FPDFProtection;
use DB;
use Illuminate\Support\Facades\URL;

class ReportController extends Controller
{
    private $appointment;
    private $company;
    private $user;
    private $appointment_classification;
    private $appointment_question;
    private $location;
    private $question_answer;
    private $question;
    private $master_answer_value;
    private $company_users;
    private $action_users;
    private $comment;
    private $upload;
    private $notification;
    private $action_item;
    private $entity;
    private $entityType;
    private $country;
    private $state;
    private $city;
    private $appointment_comments;
    private $citation;
    private $appointment_action_item_closed;

    //Construct method
    public function __construct(AppointmentRepository $appointment,
                                CompanyRepository $company,
                                UsersRepository $user,
                                AppointmentClassificationRepository $appointment_classification,
                                AppointmentQuestionRepository $appointment_question,
                                CompanyLocationRepository $location,
                                QuestionAnswerRepository $question_answer,
                                QuestionRepository $question,
                                MasterAnswerValueRepository $master_answer_value,
                                CompanyUserRepository $company_users,
                                AppointmentActionItemUsersRepository $action_users,
                                AppointmentActionItemCommentsRepository $comment,
                                UploadRepository $upload,
                                QuestionActionItemRepository $action_item,
                                AppointmentCommentsNotifyUsersRepository $notification,
                                MasterEntityTypeRepository $entityType,
                                MasterCountryRepository $country,
                                MasterStateRepository $state,
                                MasterCityRepository $city,
                                CsvGenerator $csv,
                                EntityTypeRepository $entity,
                                MasterUserRepository $master_data,
                                QuestionCitationsRepository $citation,
                                AppointmentActionItemClosedRepository $appointment_action_item_closed)
    {
        $this->appointment = $appointment;
        $this->company     = $company;
        $this->user        = $user;
        $this->appointment_classification = $appointment_classification;
        $this->appointment_question = $appointment_question;
        $this->location = $location;
        $this->question = $question;
        $this->question_answer = $question_answer;
        $this->master_answer_value = $master_answer_value;
        $this->company_users = $company_users;
        $this->action_users = $action_users;
        $this->comment = $comment;
        $this->upload = $upload;
        $this->notification = $notification;
        $this->action_item = $action_item;
        $this->entity = $entity;
        $this->entityType = $entityType;
        $this->country = $country;
        $this->city = $city;
        $this->state = $state;
        $this->csv = $csv;
        $this->master_data  = $master_data;
        $this->citation  = $citation;
        $this->appointment_action_item_closed  = $appointment_action_item_closed;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entityType = app('App\Http\Controllers\Web\UserController')->getUserEntitiyType(Auth::user()->id);
        $mjBusinesses = $this->company->findWhere(array('entity_type' => 2));
        $companies = $this->company->findCompanyByEntity(array(3,4));
        $user = $this->user->find(Auth::user()->id, array("*"));
        $isMjDisabled = false; $isCompanyDisabled = false;

        // if MJ User
        if($entityType->id == 2){
            $isMjDisabled = true;
            return view('report.index')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled,  'mjBusinesses' => $mjBusinesses,'companies' => $companies, 'companyId'=>$user->company_id, 'type'=> 'cc', 'page_title' => 'Audit Report Manager'));
        }
        // if Compliance company or Government entity
        else if($entityType->id == 3 || $entityType->id == 4){
            $isCompanyDisabled = true;
            return view('report.index')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled,  'mjBusinesses' => $mjBusinesses,'companies' => $companies, 'companyId'=>$user->company_id, 'type'=> 'cc', 'page_title' => 'Audit Report Manager'));
        }
        // if supper admin
        else if($entityType->id == 1){
            return view('report.index')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled,  'mjBusinesses' => $mjBusinesses, 'companies' => $companies, 'companyId'=> $user->company_id, 'type'=> 's_admin', 'page_title' => 'Audit Reports Manager'));
        }
    }

    /**
     * Search Appointments.
     *
     * @return json
     */
    public function getAllReports(){

        //declare and initialize valiables
        $data           = array();
        $fromDate       = $_GET['fromDate'];
        $toDate         = $_GET['toDate'];
        $mjBusiness     = $_GET['mjBusiness'];
        $companyName    = $_GET['companyName'];
        $status         = $_GET['status'];
        $audit_type     = $_GET['audit_type'];

        $fromDate = ($fromDate != "") ? date('Y-m-d H:i:s', strtotime($fromDate)) : "";
        $toDate = ($toDate != "") ? date('Y-m-d H:i:s', strtotime($toDate)) : "";

        //get all  appointments
        $reports = $this->appointment->searchAppointments($fromDate, $toDate, $mjBusiness, $companyName, $status, $audit_type);

        foreach($reports as $report_item) {
            $mj = $this->company->find($report_item['to_company_id'],array("*"));
            $bs = $this->company->find($report_item['from_company_id'],array("*"));
            $inspector = $this->appointment_classification->find($report_item['id'],array("*"));

            //data array
            $data[] = array(
                $report_item['id'],
                $report_item['inspection_date_time'],
                $report_item['inspection_number'],
                $mj->name,
                ($inspector->option_value==2)? "3rd Party": "In House",
                $bs->name,

                ($report_item['appointment_status'] == 1)?
                    $row[] =   "<a class='btn btn-success btn-circle' data-toggle='tooltip' title='Active'><i class='fa fa-thumbs-o-up'></i></a>":
                    $row[] =     "<a class='btn btn-warning btn-circle' data-toggle='tooltip' title='Inactive'><i class='fa fa-thumbs-o-down'></i></a>",
                $row[] =   "<a class='btn btn-info btn-circle' data-toggle='tooltip' title='View' data-question_id='".$report_item['id']."' onclick='viewAppointment({$report_item['id']})'><i class='fa fa-paste'></i></a>"
            );
        }
        return response()->json(["data" => $data]);
    }

    /**
     * Search reports list
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchReports(Request $request)
    {
        //declare and initialize valiables
        $user_locations = array();
        $user_appointment = array();
        $entity_type = Session('entity_type');
        $user_id = Auth::User()->id;
        $master_user_group_id = Auth::User()->master_user_group_id;
        $company_id = Auth::User()->company_id;
        $table = 'appointments';

        $columns = array('inspection_date_time', 'inspection_no', 'mjb_name', 'audit_type', 'company_name', 'status', 'manage','location','auditor','updated');
        $response = $this->getFilteredCompanyList($table, $columns, $entity_type, $master_user_group_id, $company_id, $user_id);
        return response()->json($response);

    }

    /**
     * filter all reports
     *
     * @param $table
     * @param $columns
     * @param $entity_type
     * @param $master_user_group_id
     * @param $company_id
     * @param $user_id
     *
     * @return $output
     */
    public function getFilteredCompanyList($table, $columns, $entity_type, $master_user_group_id, $company_id, $user_id)
    {
        //declare and initialize variables
        $bSearchable = false;
        $index_column = "id";
        $editInspection ="inspection_no";

        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
        }

        // Ordering
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) ) {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
                    if($_GET['iSortCol_0']==2){
                        $sortDir = "`to_company_id`" ? 'ASC' : 'DESC';
                        $sOrder .= "`to_company_id` ". $sortDir .", ";
                    }elseif($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`audit_type` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'DESC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`appointment_id` ". $sortDir .", ";
                    }
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" ) {
                $sOrder = "";
            }
        }
        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        $sGroupBy = " GROUP BY `appointments`.`id` ";

        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($columns); $i++) {

                $date = explode( "," , $_GET['sSearch_' . $i] );

                if($columns[$i]=="inspection_date_time" && $date[0]!=null) {
                    $sWhere .= "appointments.`inspection_date_time` between '%" . $date[0] . "%' and '%" . $date[1] . "%' ";
                } elseif($columns[$i]=="inspection_no") {
                    $sWhere .= "appointments.`inspection_number` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                } elseif($columns[$i]=="mj_bussiness_name") {
                    $sWhere .= "appointments.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                } elseif($columns[$i]=="company_name") {
                    $sWhere .= "appointments.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                } elseif($columns[$i]=="from_company_id") {
                    $sWhere .= "appointment_classifications.`option_value` = '" . $_GET['sSearch_' . $i] . "' ";
                }
            }
            $sWhere .= "AND (`appointments`.`appointment_status`= 0 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ) ";
            $sWhere = substr_replace($sWhere, "", -3);

        }
        else
        {
            $bSearchable = false;
            $sWhere .= "WHERE `appointments`.`appointment_status`= 1 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
        }

        // Individual column filtering
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {

                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }

                //explode date
                $date = explode( "," , $_GET['sSearch_' . $i] );

                if(isset($date) && isset($date[1])) {
                    $old_start_format = str_replace("\/", '/', $date[0]);
                    $new_start_date = date('Y-m-d', strtotime($old_start_format));

                    $old_end_format = str_replace("\/", '-', $date[1]);
                    $new_end_date = date('Y-m-d', strtotime($old_end_format));
                }

                if($columns[$i]=="inspection_date_time" && $date[0]!=null){
                    if($date[1] == null) {
                        $sWhere .= "(appointments.`" . $columns[$i] . "` >= '" . $new_start_date . "') ";
                    } else {
                        $sWhere .= "(appointments.`" . $columns[$i] . "` between '" . $new_start_date . "' and '" . $new_end_date . "') ";
                    }

                }elseif($columns[$i]=="inspection_no"){
                    $sWhere .= "";
                }elseif($columns[$i]=="mjb_name"){
                    $sWhere .= "appointments.`to_company_id` = " . $_GET['sSearch_' . $i] . " ";
                }elseif($columns[$i]=="company_name"){
                    $sWhere .= "appointments.`from_company_id` = " . $_GET['sSearch_' . $i] . " ";
                }elseif($columns[$i]=="status"){
                    $sWhere .= "appointments.`report_status` = " . $_GET['sSearch_' . $i] . " ";
                }elseif($columns[$i]=="audit_type"){
                    $sWhere .= "appointment_classifications.`option_value` = " . $_GET['sSearch_' . $i] . " ";
                }else{
                    $sWhere = "WHERE `appointments`.`appointment_status`= 1 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
                }

                $bSearchable = true;
            }
        }
        
        if ($entity_type == Config::get('simplifya.MarijuanaBusiness') && $master_user_group_id == Config::get('simplifya.MjbMasterAdmin')) {
            \Log::info("==== company id: 111111111");
            $sWhere .= "AND appointments.to_company_id = $company_id AND appointments.share_mjb = 1";
            //SQL queries get data to display
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status`, company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($entity_type == Config::get('simplifya.MarijuanaBusiness') && $master_user_group_id == Config::get('simplifya.MjbManager'))
        {
            \Log::info("==== company id: 222222222222");
            $sWhere .= "AND appointments.to_company_id = $company_id AND company_users.user_id = $user_id  AND appointments.share_mjb = 1";
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status`, company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` JOIN company_users ON company_users.location_id = appointments.company_location_id LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by  ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($entity_type == Config::get('simplifya.MarijuanaBusiness') && $master_user_group_id == Config::get('simplifya.MjbEmployee'))
        {
            \Log::info("==== company id: 33333333");
            $sWhere .= "AND appointments.to_company_id = $company_id AND appointment_action_item_users.user_id = $user_id";
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status`, company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` JOIN appointment_action_item_users ON `appointment_action_item_users`.`appointment_id` = `appointments`.`id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($entity_type == Config::get('simplifya.ComplianceCompany') && $master_user_group_id == Config::get('simplifya.CcMasterAdmin'))
        {
            \Log::info("==== company id: 444444444");
            $sWhere .= "AND appointments.from_company_id = $company_id";
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by    ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($entity_type == Config::get('simplifya.ComplianceCompany') && $master_user_group_id == Config::get('simplifya.CcInspector'))
        {
            \Log::info("==== company id: 555555555");
            $sWhere .= "AND appointments.from_company_id = $company_id AND appointments.assign_to_user_id = $user_id";
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by     ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($entity_type == Config::get('simplifya.GovernmentEntity') && $master_user_group_id == Config::get('simplifya.GeMasterAdmin'))
        {
            //\Log::info("==== company id: 666666666");
            $sWhere .= "AND appointments.from_company_id = $company_id";
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status`, company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by  ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($entity_type == Config::get('simplifya.GovernmentEntity') && $master_user_group_id == Config::get('simplifya.GeInspector'))
        {
            \Log::info("==== company id: 7777777777");
            $sWhere .= "AND appointments.from_company_id = $company_id AND appointments.assign_to_user_id = $user_id";
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        } else if ($master_user_group_id == Config::get('simplifya.MasterAdmin'))
        {
            \Log::info("==== company id: 88888888");
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        }

        //get query result
        $rResult =  $this->appointment->getInspectionRequests($sQuery);


        foreach($rResult as $item)
        {
            //\Log::info("==== company id: ....." . print_r($item,true));
            $d = str_replace("\/",'/',$item->inspection_date_time);
            //$Date = date('d-m-Y', strtotime($d));
            $Date = $item->inspection_date_time;

            //get to company list from company repository
            $to_company = $this->company->getCompany($item->mj_bussiness_name);
            //get from company list from company repository
            $from_company = $this->company->getCompany($item->company_name);

            $status_txt = "";
            $pdf_export_link = "";

            switch ($item->report_status){
                case Config::get('simplifya.REPORT_PENDING'):
                    $status_txt = "<span class=\"badge badge-warning\">". Config::get('simplifya.REPORT_PENDING_TXT') ."</span>";
                    $editInspection = "<button class=\"btn btn-info btn-circle pendingValidation\"><i class=\"fa fa-paste\"></i></button>";
                    break;
                case Config::get('simplifya.REPORT_COMPLETED'):
                    $status_txt =  "<span class=\"badge badge-info\">". Config::get('simplifya.REPORT_COMPLETED_TXT') ."</span>";
                    $editInspection = "<a href=\"/report/edit/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";
                    break;
                case Config::get('simplifya.REPORT_STARTED'):
                    $status_txt = "<span class=\"badge badge-item \">". Config::get('simplifya.REPORT_STARTED_TXT') ."</span>";
                    $editInspection = "<a href=\"/report/edit/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";
                    break;
                case Config::get('simplifya.REPORT_FINALIZED'):
                    $status_txt = "<span class=\"badge badge-success\">". Config::get('simplifya.REPORT_FINALIZED_TXT') ."</span>";
                    $editInspection = "<a href=\"/report/edit/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";
                    $pdf_export_link = "<a href=\"/report/export/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";

                    //\Log::info("==== company id: ..../////." . print_r($status_txt,true));
                    //\Log::info("==== company id: ..../////." . print_r($editInspection,true));
                    break;
            }

            //dataset array
            $dataset[] = array(
                'id'                    => $item->appointment_id,
                'inspection_date_time'  => $Date,
                'inspection_no'         => $item->inspection_no,
                'mjb_name'              => $to_company[0]->name,
                'company_name'          => $from_company[0]->name,
                'audit_type'            => $item->audit_type==1 ? "In-House" : "3rd Party",
                'status'                => $status_txt,
                'manage'                => $editInspection,
                'pdf_export_link'                => $pdf_export_link,
                'location'              => isset($item->location) ? $item->location : '',
                'auditor'               => isset($item->auditor) ? $item->auditor : '',
                'updated'               => isset($item->updated) ? $item->updated : '',
                'updated_by'            => isset($item->updated_by) ? $item->updated_by : ''
            );
            //\Log::info("==== data set: ....." . print_r($dataset,true));
        }

        if ($entity_type == Config::get('simplifya.MarijuanaBusiness') && $master_user_group_id == Config::get('simplifya.MjbMasterAdmin')) {
            $where = " WHERE appointments.to_company_id = $company_id AND appointments.share_mjb = 1";
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->appointment->getFilteredTotalNumber($where);
            if(isset($total[0])){
                $iTotal = $total[0]->count;
            }else{
                $iTotal = 0;
            }

        } else if ($entity_type == Config::get('simplifya.MarijuanaBusiness') && $master_user_group_id == Config::get('simplifya.MjbManager'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;
            if($bSearchable == false) {
                $where = " JOIN company_users ON company_users.location_id = appointments.company_location_id WHERE appointments.to_company_id = $company_id AND company_users.user_id = $user_id AND appointments.share_mjb = 1 ";


                // Get total number of rows in table
                $total = $this->appointment->getFilteredManagerTotalNumber($where);
                if(isset($total[0])){
                    $iTotal = $total[0]->count;
                }else{
                    $iTotal = 0;
                }
            } else {
                $countQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder;
                //get query result
                $allResult =  $this->appointment->getInspectionRequests($countQuery);
                // Get total number of rows in table
                $iTotal = count($allResult);
            }

        } else if ($entity_type == Config::get('simplifya.MarijuanaBusiness') && $master_user_group_id == Config::get('simplifya.MjbEmployee'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;
            if($bSearchable == false) {
                $where = " WHERE appointments.to_company_id = $company_id AND appointment_action_item_users.user_id = $user_id GROUP BY `appointments`.`id` ";

                // Get total number of rows in table
                $total = $this->appointment->getFilteredEmployeeTotalNumber($where);
                if(isset($total[0])){
                    $iTotal = $total[0]->count;
                }else{
                    $iTotal = 0;
                }
            } else {
                $countQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder;
                //get query result
                $allResult =  $this->appointment->getInspectionRequests($countQuery);
                // Get total number of rows in table
                $iTotal = count($allResult);
            }


        } else if ($entity_type == Config::get('simplifya.ComplianceCompany') && $master_user_group_id == Config::get('simplifya.CcMasterAdmin'))
        {
            if($bSearchable == false) {
                $where = " WHERE appointments.from_company_id = $company_id";
                // Get total number of rows in table
                $total = $this->appointment->getFilteredTotalNumber($where);
                if(isset($total[0])){
                    $iTotal = $total[0]->count;
                }else{
                    $iTotal = 0;
                }
            } else {
                $countQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder;
                //get query result
                $allResult =  $this->appointment->getInspectionRequests($countQuery);

                // Get total number of rows in table
                $iTotal = count($allResult);
            }

            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        } else if ($entity_type == Config::get('simplifya.ComplianceCompany') && $master_user_group_id == Config::get('simplifya.CcInspector'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;
            if($bSearchable == false) {
                $where = " WHERE appointments.from_company_id = $company_id AND appointments.assign_to_user_id = $user_id";

                // Get total number of rows in table
                $total = $this->appointment->getFilteredTotalNumber($where);
                if(isset($total[0])){
                    $iTotal = $total[0]->count;
                }else{
                    $iTotal = 0;
                }
            } else {

                $countQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder;
                //get query result
                $allResult =  $this->appointment->getInspectionRequests($countQuery);

                // Get total number of rows in table
                $iTotal = count($allResult);
            }

        } else if ($entity_type == Config::get('simplifya.GovernmentEntity') && $master_user_group_id == Config::get('simplifya.GeMasterAdmin'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;
            if($bSearchable == false) {
                $where = " WHERE appointments.from_company_id = $company_id";

                // Get total number of rows in table
                $total = $this->appointment->getFilteredTotalNumber($where);
                if(isset($total[0])){
                    $iTotal = $total[0]->count;
                }else{
                    $iTotal = 0;
                }
            } else {

                $countQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder;
                //get query result
                $allResult =  $this->appointment->getInspectionRequests($countQuery);
                // Get total number of rows in table
                $iTotal = count($allResult);
            }

        } else if ($entity_type == Config::get('simplifya.GovernmentEntity') && $master_user_group_id == Config::get('simplifya.GeInspector'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;
            if($bSearchable == false) {
                $where = " WHERE appointments.from_company_id = $company_id AND appointments.assign_to_user_id = $user_id";

                // Get total number of rows in table
                $total = $this->appointment->getFilteredTotalNumber($where);
                if(isset($total[0])){
                    $iTotal = $total[0]->count;
                }else{
                    $iTotal = 0;
                }
            } else {
                $countQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`report_status` as `report_status` , company_locations.name as location, u2.name as auditor, appointments.updated_at as updated, u3.name as updated_by FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` LEFT JOIN company_locations on company_locations.id = appointments.company_location_id LEFT JOIN users u2 ON u2.id = appointments.assign_to_user_id LEFT JOIN users u3 ON u3.id = appointments.updated_by   ".$sWhere." $sGroupBy ".$sOrder;
                //get query result
                $allResult =  $this->appointment->getInspectionRequests($countQuery);
                // Get total number of rows in table
                $iTotal = count($allResult);
            }

        } else if ($master_user_group_id == Config::get('simplifya.MasterAdmin'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->appointment->getTotaleNumber();
            if(isset($total[0])){
                $iTotal = $total[0]->count;
            }else{
                $iTotal = 0;
            }
        }else{
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->appointment->getTotaleNumber();
            if(isset($total[0])){
                $iTotal = $total[0]->count;
            }else{
                $iTotal = 0;
            }
        }

        // Output data array
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        //\Log::info("==== company id: ..../////." . print_r($columns,true));
        // Return array of values
        foreach($dataset as $aRow) {
            $row = array();
            for ( $i = 0; $i < count($columns); $i++ ) {
                if ( $columns[$i] == 'inspection_date_time' ) {
                    //$row[] = date('m-d-Y', strtotime($aRow['inspection_date_time']));
                    $row[] = date('m-d-Y h:i A', strtotime($aRow['inspection_date_time'])); //date('m-d-Y H:i A', strtotime($aRow['inspection_date_time']));
                }
                if ( $columns[$i] == 'inspection_no' ) {
                    $row[] = $aRow['inspection_no'];
                }
                if ( $columns[$i] == 'mjb_name' ) {
                    $row[] = $aRow['mjb_name'];
                }
                if ($columns[$i] == 'audit_type' ) {
                    $row[] = $aRow['audit_type'];
                }
                if ( $columns[$i] == 'company_name' ) {
                    $row[] = $aRow['audit_type'] == 'In-House' ? 'Self-Audit' : $aRow['company_name'];
                }
                if ( $columns[$i] == 'status' ) {
                    $row[] = $aRow['status'];
                }
                if( $columns[$i] == 'manage') {
                    $row[] = $aRow['manage'];
                }
                if( $columns[$i] == 'location') {
                    $row[] = $aRow['location'];
                }
                if( $columns[$i] == 'auditor') {
                    $row[] = $aRow['auditor'];
                }
                if( $columns[$i] == 'updated') {
                    $row[] = date('m-d-Y h:i A', strtotime($aRow['updated']));
                }
            }
            $show_pdf = "";
            if (strpos($aRow['status'], 'Finalized') !== false) {
                \Log::info("==== item?????: ..../////." . print_r($row,true));
                $show_pdf = $aRow['pdf_export_link'];
            }

            $row[] = $show_pdf;

            $output['aaData'][] = $row;
        }

        return $output ;
    }

    /**
     *  Export Questions
     */
    public function export($appointment_id,$pw){

        $result=$this->getQuestionsList($appointment_id);
        //$categories=$this->loadReportListData($appointment_id,1);
        $categories_formated=$this->loadReportListData($appointment_id,'','category');

        //echo json_encode($result); die;

        if(count($categories_formated['categories']) > 0)
        {
            $x = 0;
            foreach($categories_formated['categories'] as $category)
            {
                $compliantCount = 0;
                $nonCompliantCount = 0;
                $unknownCompliantCount = 0;

                if(count($categories_formated['questions']) > 0)
                {
                    foreach($categories_formated['questions'] as $question)
                    {
                        if($category['id'] == $question['category_id'] && $question['answer_value_id'] == 1)
                        {
                            $compliantCount++;
                            //echo json_encode($question); die;
                        }
                        if($category['id'] == $question['category_id'] && $question['answer_value_id'] == 2)
                        {
                            $nonCompliantCount++;
                        }
                        if($category['id'] == $question['category_id'] && $question['answer_value_id'] == 3)
                        {
                            $unknownCompliantCount++;
                        }
                    }
                }
                $pct = ($compliantCount/($compliantCount+$nonCompliantCount+$unknownCompliantCount) )*100;
                $categories_formated['categories'][$x]['percentage'] = $pct;
                $x++;
            }
        }

        $compliantCountT = 0;
        $nonCompliantCountT = 0;
        $unknownCompliantCountT = 0;

        //echo json_encode($categories['questions']); die;

        foreach($categories_formated['questions'] as $question)
        {
            //echo $question['answer_value_id']."<br>";
            if($question['answer_value_id'] == '1')
            {
                $compliantCountT++;
            }
            if($question['answer_value_id'] == '2')
            {
                $nonCompliantCountT++;
            }
            if($question['answer_value_id'] == '3')
            {
                $unknownCompliantCountT++;
            }
        }

        $pctT = 0;
        if(($compliantCountT+$nonCompliantCountT+$unknownCompliantCountT) > 0)
        {
            $pctT = ($compliantCountT/($compliantCountT+$nonCompliantCountT+$unknownCompliantCountT) )*100;
        }
        $categories_formated['all_percentage'] = $pctT;
        $categories_formated['compliantCount'] = $compliantCountT;
        $categories_formated['nonCompliantCount'] = $nonCompliantCountT;
        $categories_formated['unknownCompliantCount'] = $unknownCompliantCountT;
        //unset($categories['questions']);

        //echo "com".$compliantCountT." Ncom".$nonCompliantCountT." Uc:".$unknownCompliantCountT;
        //echo json_encode($categories_formated); die;
        $company_data = $this->appointment->getCompanyInfo($appointment_id);
        //print_r(json_encode($company));
        //\Log::info("==== company id:...........".print_r($company,true));
        //die;
        $pdf = new PdfReportGenerator();
        $pdf->SetProtection(array('print'),$pw);
        $pdf->ImprovedTable($result,$categories_formated,$company_data);
        $pdf->Output();


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getQuestionsList($id)
    {
        //declare and initialize valiables
        $user = Auth::user();
        $company_id = Auth::User()->company_id;
        $entity_type = Session('entity_type');
        $info = $this->appointment->find($id, array('*'));
        $allowed = true;



        if ($user->master_user_group_id == Config::get('simplifya.MjbEmployee'))
        {
            $allowed = false;
        }

        if($info) {
            $from_company = $this->company->find($info->from_company_id);
            $to_company = $this->company->find($info->to_company_id);
            $user = $this->user->find($info->assign_to_user_id);
            $location = $this->location->find($info->company_location_id);

            $audit_type = $this->appointment_classification->findWhere(array('entity_type' => 'AUDIT_TYPE', 'appointment_id' => $id));
            $licence_types = $this->appointment_classification->getLicenceDataByAppointmentId($id);

            $licence_type_list = collect();
            if($licence_types != null){
                foreach ($licence_types as $licence_type){

                    $licence_number = $this->appointment_classification->getLicenceNumberByAppointmentId($licence_type->id,$location->id);
                    //\Log::info("==== company id:...........".print_r($licence_number,true));
                    //$licence_data = array('name'=>$licence_type->name ,'licence_number' => $licence_number->license_number );
                    $license_no = isset($licence_number->license_number) ? $licence_number->license_number : '';
                    $licence_data = array('name'=>$licence_type->name ,'licence_number' => $license_no );
                    $licence_type_list->push($licence_data);
                }
            }


            $status_txt = "";


            switch ($info['report_status']) {
                case Config::get('simplifya.REPORT_PENDING'):
                    $status_txt = Config::get('simplifya.REPORT_PENDING_TXT');
                    break;
                case Config::get('simplifya.REPORT_COMPLETED'):
                    $status_txt = Config::get('simplifya.REPORT_COMPLETED_TXT');
                    break;
                case Config::get('simplifya.REPORT_STARTED'):
                    $status_txt = Config::get('simplifya.REPORT_STARTED_TXT');
                    break;
                case Config::get('simplifya.REPORT_FINALIZED'):
                    $status_txt = Config::get('simplifya.REPORT_FINALIZED_TXT');
                    break;
                default:
                    $status_txt = "";
                    break;
            }

            $d = str_replace("\/", '/', $info['inspection_date_time']);
            //$Date = date('m-d-Y h:m a', strtotime($d));
            $Date = date('m-d-Y h:i a', strtotime($d));

            //dataset array
            $dataset = array(
                'id' => $id,
                'cc' => $from_company['name'],
                'mjb' => $to_company['name'],
                'status' => $status_txt,
                'inspector' => $user['name'],
                'date_time' => $Date,
                'location' => $location['name'],
                'audit_type' => $audit_type[0]->option_value == 1 ? "In-house" : "3rd Party",
                'licence_names'=>$licence_type_list
            );

            return $dataset;
        }
    }


    /**
     * Search reports list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inspectionReport(Request $request)
    {
        //declare and initialize valiables
        $user_locations = array();
        $user_appointment = array();
        $entity_type = Session('entity_type');
        $user_id = Auth::User()->id;
        $master_user_group_id = Auth::User()->master_user_group_id;
        $company_id = Auth::User()->company_id;
        $table = 'appointments';

        $columns = array('inspection_date_time', 'inspection_time', 'mjb_name', 'company_name','audit_type','duration','status', 'appointment_status');
        $response = $this->getInspectionReportList($table, $columns, $entity_type, $master_user_group_id, $company_id, $user_id);
        return response()->json($response);

    }

    /**
     * get all inspection report list
     *
     * @param $table
     * @param $columns
     * @param $entity_type
     * @param $master_user_group_id
     * @param $company_id
     * @param $user_id
     * @return array
     */
    public function getInspectionReportList($table, $columns, $entity_type, $master_user_group_id, $company_id, $user_id)
    {
        //declare and initialize variables
        $index_column = "id";
        $editInspection ="inspection_no";

        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
        }

        // Ordering
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) ) {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
                    if($_GET['iSortCol_0']==2){
                        $sortDir = "`to_company_id`" ? 'ASC' : 'DESC';
                        $sOrder .= "`to_company_id` ". $sortDir .", ";
                    }elseif($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`audit_type` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'DESC') == 0) ? 'ASC' : 'DESC';
                        //$sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                        $sOrder .= "`appointment_id` ". $sortDir .", ";
                    }
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" ) {
                $sOrder = "";
            }
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        $sGroupBy = " GROUP BY `appointments`.`id` ";

        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($columns); $i++) {

                $date = explode( "," , $_GET['sSearch_' . $i] );

                if($columns[$i]=="inspection_date_time" && $date[0]!=null) {
                    $sWhere .= "appointments.`inspection_date_time` between '%" . $date[0] . "%' and '%" . $date[1] . "%' ";
                } elseif($columns[$i]=="inspection_no") {
                    $sWhere .= "appointments.`inspection_number` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                } elseif($columns[$i]=="mj_bussiness_name") {
                    $sWhere .= "appointments.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                } elseif($columns[$i]=="company_name") {
                    $sWhere .= "appointments.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                } elseif($columns[$i]=="from_company_id") {
                    $sWhere .= "appointment_classifications.`option_value` = '" . $_GET['sSearch_' . $i] . "' ";
                }
            }
//            $sWhere .= "AND (`appointments`.`appointment_status`= 0 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ) ";
            $sWhere .= "AND (`appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ) ";
            $sWhere = substr_replace($sWhere, "", -3);

        }
        else
        {
//            $sWhere .= "WHERE `appointments`.`appointment_status`= 1 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
            $sWhere .= "WHERE `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
        }

        // Individual column filtering
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {

                //explode date
                $date = explode( "," , $_GET['sSearch_' . $i] );

                if(isset($date) && isset($date[1])) {
                    $old_start_format = str_replace("\/", '/', $date[0]);
                    $new_start_date = date('m-d-Y', strtotime($old_start_format));

                    $old_end_format = str_replace("\/", '-', $date[1]);
                    $new_end_date = date('m-d-Y', strtotime($old_end_format));
                }

                if($columns[$i]=="inspection_date_time" && $date[0]!=null){
                    if ($sWhere == "") {
                        $sWhere .= "WHERE (appointments.`" . $columns[$i] . "` between '" . $new_start_date . "' and '" . $new_end_date . "') ";
                    } else {
                        $sWhere .= " AND (appointments.`" . $columns[$i] . "` between '" . $new_start_date . "' and '" . $new_end_date . "') ";
                    }

                }
                elseif($columns[$i]=="inspection_time"){
                    $sWhere .= "";
                }
                elseif($columns[$i]=="mjb_name"){
                    if ($sWhere == "") {
                        $sWhere .= " WHERE appointments.`to_company_id` = " . $_GET['sSearch_' . $i] . " ";
                    } else {
                        $sWhere .= " AND appointments.`to_company_id` = " . $_GET['sSearch_' . $i] . " ";
                    }

                }
                elseif($columns[$i]=="company_name"){
                    if ($sWhere == "") {
                        $sWhere .= "WHERE appointments.`from_company_id` = " . $_GET['sSearch_' . $i] . " ";
                    } else {
                        $sWhere .= " AND appointments.`from_company_id` = " . $_GET['sSearch_' . $i] . " ";
                    }

                }
                elseif($columns[$i]=="status"){
                    if ($sWhere == "") {
                        $sWhere .= "WHERE appointments.`report_status` = " . $_GET['sSearch_' . $i] . " ";
                    } else {
                        $sWhere .= " AND appointments.`report_status` = " . $_GET['sSearch_' . $i] . " ";
                    }
                }
                elseif($columns[$i]=="audit_type"){
                    if ($sWhere == "") {
                        $sWhere .= "WHERE appointment_classifications.`option_value` = " . $_GET['sSearch_' . $i] . " ";
                    } else {
                        $sWhere .= " AND appointment_classifications.`option_value` = " . $_GET['sSearch_' . $i] . " ";
                    }


                }elseif($columns[$i]=="duration"){


                }elseif($columns[$i]=="compliant_rate"){


                }else{
//                    $sWhere = "WHERE `appointments`.`appointment_status`= 1 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
                    $sWhere = "WHERE `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
                }
            }
        }

        if ($master_user_group_id == Config::get('simplifya.MasterAdmin'))
        {
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`appointment_status` as `appointment_status`, `appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`start_inspection` as `start_time`, `appointments`.`finish_inspection` as `end_time`,  `appointments`.`report_status` as `report_status` FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` ".$sWhere." $sGroupBy ".$sOrder." ".$sLimit;
        }

        //get query results
        $rResult =  $this->appointment->getInspectionRequests($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            $d = str_replace("\/",'/',$item->inspection_date_time);
            $Date = date('d-m-Y', strtotime($d));
            $time = date('h:i:s A', strtotime($d));
            $to_company = $this->company->getCompany($item->mj_bussiness_name);
            $from_company = $this->company->getCompany($item->company_name);

            $date1=date_create($item->start_time);
            $date2=date_create($item->end_time);
            $diff=date_diff($date1,$date2);
            $day = '';
            if($diff->d != 0) {
                $day .= $diff->d.' days';
            }

            if($diff->h != 0) {
                if($day != '') {
                    $day .= 'and '.$diff->h.' hours ';
                } else {
                    $day .= $diff->h.' hours ';
                }

            }
            if($diff->m != 0) {
                if($day != '') {
                    $day .='and'. $diff->m.' minutes';
                } else {
                    $day .= $diff->m.' minutes';
                }

            }

            $status_txt = "";

            switch ($item->report_status){
                case Config::get('simplifya.REPORT_PENDING'):
                    $status_txt = "<span class=\"badge badge-warning\">". Config::get('simplifya.REPORT_PENDING_TXT') ."</span>";
                    $editInspection = "<button class=\"btn btn-info btn-circle pendingValidation\"><i class=\"fa fa-paste\"></i></button>";
                    break;
                case Config::get('simplifya.REPORT_COMPLETED'):
                    $status_txt =  "<span class=\"badge badge-info\">". Config::get('simplifya.REPORT_COMPLETED_TXT') ."</span>";
                    $editInspection = "<a href=\"/report/edit/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";
                    break;
                case Config::get('simplifya.REPORT_STARTED'):
                    $status_txt = "<span class=\"badge badge-item \">". Config::get('simplifya.REPORT_STARTED_TXT') ."</span>";
                    $editInspection = "<a href=\"/report/edit/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";
                    break;
                case Config::get('simplifya.REPORT_FINALIZED'):
                    $status_txt = "<span class=\"badge badge-success\">". Config::get('simplifya.REPORT_FINALIZED_TXT') ."</span>";
                    $editInspection = "<a href=\"/report/edit/". $item->appointment_id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";
                    break;
            }

            $appointment_status = '';
            if ($item->appointment_status == 1) {
                $appointment_status  = "<span class='badge badge-success'>" . Config::get('simplifya.APPOINTMENT_ACTIVE_TXT') . "</span>";
            }else if ($item->appointment_status == 2) {
                $appointment_status  = "<span class='badge badge-danger'>" . Config::get('simplifya.APPOINTMENT_CANCELED_TXT') . "</span>";
            }


            //dataset array
            $dataset[] = array(
                'id'                    => $item->appointment_id,
                'inspection_date_time'  => $Date,
                'inspection_time'         => $time,
                'mjb_name'              => $to_company[0]->name,
                'company_name'          => $from_company[0]->name,
                'audit_type'            => $item->audit_type==1 ? "In-House" : "3rd Party",
                'duration'              => ($day != '') ?$day :'-',
                'status'                => $status_txt,
                'appointment_status'    => $appointment_status,
            );

        }

        if ($master_user_group_id == Config::get('simplifya.MasterAdmin'))
        {
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->appointment->getTotaleNumber();
            $iTotal = $total[0]->count;
        }else{
            $FilteredTotal = $this->appointment->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->appointment->getTotaleNumber();
            $iTotal = $total[0]->count;
        }

        // Output
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );

        // Return array of values
        foreach($dataset as $aRow) {
            $row = array();
            for ( $i = 0; $i < count($columns); $i++ ) {
                if ( $columns[$i] == 'inspection_date_time' ) {
                    $row[] = date('m-d-Y', strtotime($aRow['inspection_date_time']));
                }
                if ( $columns[$i] == 'inspection_time' ) {
                    $row[] = $aRow['inspection_time'];
                }
                if ( $columns[$i] == 'mjb_name' ) {
                    $row[] = $aRow['mjb_name'];
                }
                if ( $columns[$i] == 'company_name' ) {
                    $row[] = $aRow['company_name'];
                }
                if ($columns[$i] == 'audit_type' ) {
                    $row[] = $aRow['audit_type'];
                }
                if ($columns[$i] == 'duration' ) {
                    $row[] = $aRow['duration'];
                }
                if ( $columns[$i] == 'status' ) {
                    $row[] = $aRow['status'];
                }
                if ( $columns[$i] == 'appointment_status' ) {
                    $row[] = $aRow['appointment_status'];
                }
            }
            $output['aaData'][] = $row;
        }
        return $output ;
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
    public function reportsEdit($id)
    {
        //declare and initialize valiables
        $user = Auth::user();
        $company_id = Auth::User()->company_id;
        $entity_type = Session('entity_type');
        $info = $this->appointment->find($id, array('*'));
        $allowed = true;



        if ($user->master_user_group_id == Config::get('simplifya.MjbEmployee'))
        {
            $allowed = false;
        }

        if($info) {
            $from_company = $this->company->find($info->from_company_id);
            $to_company = $this->company->find($info->to_company_id);
            $user = $this->user->find($info->assign_to_user_id);
            $location = $this->location->find($info->company_location_id);

            $audit_type = $this->appointment_classification->findWhere(array('entity_type' => 'AUDIT_TYPE', 'appointment_id' => $id));
            $licence_types = $this->appointment_classification->getLicenceDataByAppointmentId($id);

            $licence_type_list = collect();
            if($licence_types != null){
                foreach ($licence_types as $licence_type){

                    $licence_number = $this->appointment_classification->getLicenceNumberByAppointmentId($licence_type->id,$location->id);
                    //\Log::info("==== company id:...........".print_r($licence_number,true));
                    //$licence_data = array('name'=>$licence_type->name ,'licence_number' => $licence_number->license_number );
                    $license_no = isset($licence_number->license_number) ? $licence_number->license_number : '';
                    $licence_data = array('name'=>$licence_type->name ,'licence_number' => $license_no );
                    $licence_type_list->push($licence_data);
                }
            }


            $status_txt = "";


            switch ($info['report_status']) {
                case Config::get('simplifya.REPORT_PENDING'):
                    $status_txt = Config::get('simplifya.REPORT_PENDING_TXT');
                    break;
                case Config::get('simplifya.REPORT_COMPLETED'):
                    $status_txt = Config::get('simplifya.REPORT_COMPLETED_TXT');
                    break;
                case Config::get('simplifya.REPORT_STARTED'):
                    $status_txt = Config::get('simplifya.REPORT_STARTED_TXT');
                    break;
                case Config::get('simplifya.REPORT_FINALIZED'):
                    $status_txt = Config::get('simplifya.REPORT_FINALIZED_TXT');
                    break;
                default:
                    $status_txt = "";
                    break;
            }

            //$d = str_replace("\/", '/', $info['inspection_date_time']);
            //$Date = date('m-d-Y h:m a', strtotime($d));
            $Date = date('m-d-Y g:i a', strtotime(str_replace('/', '-', $info['inspection_date_time'])));

            //dataset array
            $dataset = array(
                'id' => $id,
                'cc' => $from_company['name'],
                'mjb' => $to_company['name'],
                'status' => $status_txt,
                'inspector' => $user['name'],
                'date_time' => $Date,
                'location' => $location['name'],
                'audit_type' => $audit_type[0]->option_value == 1 ? "In-house" : "3rd Party",
                'licence_names'=>$licence_type_list
            );
            if ($entity_type == Config::get('simplifya.MarijuanaBusiness')) {
                if ($company_id == $info->to_company_id && $info->share_mjb == 1) {
                    return view('report.editReport')->with(array('page_title' => 'VIEW AUDIT REPORT', 'info' => $dataset, 'allowed' => $allowed, 'finalise' => $info->report_status, 'entity_type' => $entity_type));
                } else {
                    $message = Config::get('messages.ACCESS_DENIED');
                    return Redirect::to("/dashboard")->with('error', $message);
                }
            } elseif (($entity_type == Config::get('simplifya.ComplianceCompany')) || ($entity_type == Config::get('simplifya.GovernmentEntity'))) {
                if ($info->from_company_id == $company_id) {
                    return view('report.editReport')->with(array('page_title' => 'VIEW AUDIT REPORT', 'info' => $dataset, 'allowed' => $allowed, 'finalise' => $info->report_status, 'entity_type' => $entity_type));
                } else {
                    $message = Config::get('messages.ACCESS_DENIED');
                    return Redirect::to("/dashboard")->with('error', $message);
                }
            } else {
                return view('report.editReport')->with(array('page_title' => 'VIEW AUDIT REPORT', 'info' => $dataset, 'allowed' => $allowed));
            }
        } else {
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }

    function deactivateActionItem()
    {
        $action_id = $_REQUEST['action_id'];
        $appointment_id = $_REQUEST['appointment_id'];

        $user_group_id = Auth::user()->master_user_group_id;

        $action_item_details = $this->action_item->find($action_id);
        $users = $this->action_users->getNotifiedUsers($appointment_id, $action_id);
        foreach($users as $user)
        {
            if($user != Auth::user()->id)
            {
                $users_detail = $this->user->getUserEmailById(array($user));
                $layout = 'emails.action_item_remove';
                $subject = 'Action item closed';
                $base_url = '/report/edit/'.$appointment_id.'?aid='.$action_id.'#/step3';
                $url = URL::to($base_url);

                foreach ($users_detail as $user_detail) {
                    $email_data = array(
                        'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                        'system' => 'Simplifya',
                        'company' => 'Simplifya',
                        'action_item' => $action_item_details->name,
                        'url' => $url
                    );
                    $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject,$email_data);
                }
                \Log::debug("==== question tree data count " . print_r($users_detail,true));
            }
        }

        $res = $this->appointment_action_item_closed->createDeletedActionItem(array('action_item_id' => $action_id, 'appointment_id' => $appointment_id));
        if($res)
        {
            return Response()->json(array('success' => true, 'data' => $res, 'user_group_id' => $user_group_id));
        }
        //\Log::debug("==== question tree data count " . print_r($res,true));
    }

    function reopen_action_item()
    {
        $action_id = $_REQUEST['action_id'];
        $appointment_id = $_REQUEST['appointment_id'];
        $comment = $_REQUEST['comment'];

        $data = array(
                'appointment_id' => $appointment_id,
                'question_action_item_id' => $action_id,
                'content' => $comment,
                'type' => 1
        );

        DB::beginTransaction();

        try{
            $res_comment = $this->comment->createActionItemComment($data);
            $res = $this->appointment_action_item_closed->deleteDeletedActionItem($action_id,$appointment_id);

            $action_item_details = $this->action_item->find($action_id);
            $users = $this->action_users->getNotifiedUsers($appointment_id, $action_id);
            foreach($users as $user)
            {
                if($user != Auth::user()->id)
                {
                    $users_detail = $this->user->getUserEmailById(array($user));
                    $layout = 'emails.action_item_reopen';
                    $subject = 'Action item reopened';
                    $base_url = '/report/edit/'.$appointment_id.'?aid='.$action_id.'#/step3';
                    $url = URL::to($base_url);

                    foreach ($users_detail as $user_detail) {
                        $email_data = array(
                            'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                            'system' => 'Simplifya',
                            'company' => 'Simplifya',
                            'action_item' => $action_item_details->name,
                            'comment' => $comment,
                            'url' => $url
                        );
                        $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject,$email_data);
                    }
                    \Log::debug("==== question tree data count " . print_r($users_detail,true));
                }
            }

            DB::commit();

            return Response()->json(array('success' => true, 'data' => $res));

        }catch(Exception $ex){
            // Someting went wrong
            \Log::info("=============error===============");
            DB::rollback();

            return Response()->json(array('success' => 'false'), 200);

        }

        //\Log::debug("==== question tree data count " . print_r($res,true));
    }

    /**
     * Get all active questions with answers according to appointment ID
     *
     * @return \Illuminate\Http\Response
     */
    public function reportQuestions(AppointmentIDRequest $request)
    {
        //declare and initialize variables
        $dataset = "";
        $appointment_id = $request->appointment_id;
        $answer_value_id = $request->answer_id;
        $question_id = "";
        $category_id = "";

        $question_list = app('App\Http\Controllers\Api\QuestionController')->otherAppointmentQuestions($appointment_id,$answer_value_id, $category_id, $question_id);

        return $question_list;
    }

    /**
     * Get all action items for appointment question IDs
     *
     * @return \Illuminate\Http\Response
     */
    public function reportActionItems(AppointmentIDRequest $request)
    {
        //declare valiables
        $user = Auth::user();
        $dataset = "";
        $action_item_arr = array();
        $comment_arr = "";
        $comments = array();
        $master_answer_arr = array();

        //get appointment id
        $appointment_id = $request->appointment_id;
        $answer_id = $request->answer_id;
        $category_id=$request->category;


        $getMasterAnswers = $this->appointment_question->findAnsweredAppointmentQuestions($appointment_id);

        foreach ($getMasterAnswers as $item)
        {
            $master_answer_arr[] = $item->master_answer_id;
        }

        //get all active answered questions with non-compliance answers from the appointment questions list
        $nonComplianceActionItems = $this->appointment_question->getAllNonComplianceQuestions($appointment_id, $master_answer_arr, $answer_id, $user->id, $user->master_user_group_id,$category_id, 'category');

        foreach ($nonComplianceActionItems as $item)
        {
            $action_item_arr[] = $item->action_item_id;
        }

        // get unread records
        $unread_records = $this->appointment_question->getUnreadRecords($appointment_id, $action_item_arr, $user->id);

        $unread_data = [];
        $total_data = [];

        foreach($unread_records as $rec){
            $unread_data[$rec->question_action_item_id][] = $rec->appointment_action_item_comments_id;
        }

        $nonComplianceActionItemsWithCount = [];

        //comment_arr array
        $comment_arr = array(
            'appointment_id' => $appointment_id,
            'status'        => 1,
            'question_action_item_id'   => $action_item_arr
        );

        //get all action item comments
        $get_comment = $this->comment->getAllActionItemComments($comment_arr);

        foreach ($get_comment as $item) {
            //declare and initialize variables
            $comments[] = array(
                'date_time'                 =>  date('m/d/Y h:i A', strtotime($item['created_at'])),
                'content'                   =>  $item['content'],
                'comment_id'                =>  $item['id'],
                'question_action_item_id'   =>  $item['question_action_item_id'],
                'username'                  =>  $item['user']['name'],
                'image_path'                =>  Config::get('simplifya.BUCKET_IMAGE_PATH').Config::get('aws.ACTION_COMMENT_IMG_DIR'),
                'image'                     =>  $item['image'],
                'location'                  =>  ($item['location'] == 'NULL')?'':$item['location']
            );

            $total_data[$item->question_action_item_id][] = $item->appointment_id;
        }

        $questionTreeData = $this->loadReportListData($request->appointment_id, '', 'category');
        \Log::debug("==== question tree data count " . count($questionTreeData['questions']));

        // Attach unread count
        foreach($nonComplianceActionItems as $data){
            \Log::debug("===== data question id : " . $data->question_id);

            $key = array_search($data->question_id, array_column($questionTreeData['questions'], 'question_id'));
            //\Log::debug("==== question found : " . print_r($questionTreeData['questions'][$key], true));
            \Log::debug("==== question level : " . $questionTreeData['questions'][$key]['level']);
            $level = '';
            if (false !== $key) {
                if (isset($questionTreeData['questions'][$key])) {
                    $level = $questionTreeData['questions'][$key]['level'];
                }
            }else {
                \Log::debug("==== key not found " . $key);
            }
            $temp_data = $data;
            $temp_data->total_count = isset($total_data[$data->action_item_id]) ? count($total_data[$data->action_item_id]): 0;
            $temp_data->unread_count = isset($unread_data[$data->action_item_id]) ? count($unread_data[$data->action_item_id]) : 0;
            $temp_data->level = $level;

            $appointment_action_item_closed_count = $this->appointment_action_item_closed->isRowExist($request->appointment_id,$data->action_item_id);
            $temp_data->appointment_action_item_closed_count = $appointment_action_item_closed_count > 0 ? $appointment_action_item_closed_count : 0;

            $nonComplianceActionItemsWithCount[] = $temp_data;
        }

        if($nonComplianceActionItems)
        {
            $check_status = $this->appointment->find(array('id' => $appointment_id));

            if(isset($check_status[0])){
                if($check_status[0]->report_status==3){
                    $status = "true";
                }else{
                    $status = "false";
                }
                $inspection_number = $check_status[0]->inspection_number;
            }else{
                $status = "false";
            }

            return Response()->json(array('success' => 'true', 'status'=> $status, 'inspection_number' => $inspection_number, 'data' => $nonComplianceActionItemsWithCount, 'comments' => $comments, 'user' => $user));
        }
        else{
            $message = "";
            return Response()->json(array('success' => 'false', 'data' => $message));
        }
    }

    /**
     * Get all action items assignee for appointments
     *
     * @return \Illuminate\Http\Response
     */
    public function reportActionItemsAssignee(Request $request)
    {
        //declare valiables
        $user = Auth::user();
        $action_id=$request['action_id'];
        $appointmentId=$request['appointmentId'];


        //get all active answered questions with non-compliance answers from the appointment questions list
        $nonComplianceActionItems = $this->appointment_question->getActionItemsAssignee($action_id, $appointmentId,$user->id, $user->master_user_group_id);

        $nonComplianceActionItemsWithCount = [];
        // Attach unread count
        foreach($nonComplianceActionItems as $data){
            $temp_data = $data;

            $nonComplianceActionItemsWithCount[] = $temp_data;
        }

        if($nonComplianceActionItems)
        {


            return Response()->json(array('success' => 'true', 'data' => $nonComplianceActionItemsWithCount));
        }
        else{
            $message = "";
            return Response()->json(array('success' => 'false', 'data' => $message));
        }
    }

    /**
     * read all action item comments
     */
    public function readAllActionItemComments(){
        $action_id = $_GET['action_id'];
        $user_id   = Auth::user()->id;
        $appointment_id = $_GET['appointment_id'];

        $read_comments = $this->comment->readComments($appointment_id, $action_id, $user_id);

        if($read_comments){
            $message = "Action Item comments read successfully!";
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }else{
            $message = "Action Item comments read unsuccessful";
            return Response()->json(array('success' => 'false', 'message' => $message), 400);
        }
    }

    /**
     * get all marijuana business user list by location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByLocation()
    {
        //declare and initialize variables
        $appointment_id = $_GET['appointment_id'];
        $action_item_id = $_GET['action_item_id'];
        $user_arr = [];
        $dataset = [];
        $status = "";
        $company_id = Auth::user()->company_id;

        //get appointment details by appointment id
        $appointment = $this->appointment->find($appointment_id);
        $company_users = $this->company_users->findWhere(array('location_id' => $appointment->company_location_id));

        //get user list
        foreach ($company_users as $user)
        {
            $user_arr[] = $user->user_id;
        }

        /**
         * Fetch master admin users from users entity
         */
        $companyMasterAdminUsers = $this->user->findWhere(array('company_id' => $company_id, 'master_user_group_id' => Config::get('simplifya.MjbMasterAdmin')));
        foreach ($companyMasterAdminUsers as $user)
        {
            $user_arr[] = $user->id;
        }

        //get user details from user reporsitory
        $user_details = $this->user->getLocationUsers($user_arr, $company_id);
        //check user details from user repository
        $check_users = $this->user->checkUserAssigned($user_arr, $appointment_id, $action_item_id);

        foreach ($user_details as $detail){
            $status = false;
            foreach ($check_users as $check_user){
                if($detail->id == $check_user->user_id){
                    $status = true;
                    break;
                }
            }

            //dataset array
            $dataset[] = array(
                'id' => $detail->id,
                'name' => $detail->name,
                'status' => $status
            );
        }
        return Response()->json(array('success' => 'true', 'data' => $dataset));
    }

    /**
     * Assign Users to action item
     *
     * @param AssginUsersRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignUsers(AssginUsersRequest $request)
    {
        //declare and initialize variables
        $dataset = $request->dataset;
        $appointment_id = $dataset['appointmentId'];
        $action_item_id = $dataset['action_id'];
        $inspection_no = $dataset['inspection_no'];

        $data_arr = [];
        $users_arr = [];
        $email_user = [];
        $action_user = array();
        $action_item_details = $this->action_item->find($action_item_id);
        \Log::debug("==== assignUsers");
        if(!isset($dataset['location_user'])) {
            \Log::debug("==== assignUsers if true");
            //remove all users in specified user and appointment
            $removeAll = $this->user->removeAssignedUser($appointment_id, $action_item_id);
            if($removeAll) {
                $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => $message), 200);
            } else {
                $message = Config::get('messages.ACTION_ITEM_NO_USERS_SAVED');
                return Response()->json(array('success' => 'true', 'message' => $message), 200);
            }
        } else {
            \Log::debug("==== assignUsers if false");
            $action_item_users = $this->action_users->findWhere(array('appointment_id' => $appointment_id, 'question_action_item_id' => $action_item_id));
            foreach($action_item_users as $action_item_user) {
                $action_user[] =  $action_item_user['user_id'];
            }
            \Log::debug("==== action user " . print_r($action_user, true));
            if(count($action_user) > 0) {
                \Log::debug("==== assignUsers action user > 0");
                \Log::debug("==== location user " . print_r($dataset,true));
                $user1 = array_diff((array)$dataset['location_user'], $action_user);
                $user2 = array_diff($action_user, (array)$dataset['location_user']);

                \Log::debug("==== user 1 " . print_r($user1, true));
                \Log::debug("==== user 2 " . print_r($user2, true));

                if (!empty($user1) && !empty($user2)) {
                    $this->addActionItemUser($user1, $dataset, $action_item_details);
                    foreach ($user2 as $user) {
                        $data[] = array(
                            'appointment_id' => $dataset['appointmentId'],
                            'question_action_item_id' => $dataset['action_id'],
                            'user_id' => $user
                        );
                    }
                    $this->action_users->deleteActionItemUsers($data);
                    $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');

                    // Send push notifications
//                    $this->sendActionItemAssignedPushNotification($user1, $action_item_id, $appointment_id);

                    return Response()->json(array('success' => 'true', 'message' => $message), 200);
                } else {
                    \Log::debug("==== assignUsers action user > 0 false");
                    if (!empty($user1)) {
                        \Log::debug("==== assignUsers user1 not empty");
                        $response = $this->addActionItemUser($user1, $dataset, $action_item_details);
                        if($response['success'] == 'true') {
                            // Send push notifications
                            $this->sendActionItemAssignedPushNotification($user1, $action_item_id, $appointment_id);

                            return Response()->json(array('success' => 'true', 'message' => $response['message']), 200);
                        } else {
                            return Response()->json(array('success' => 'true', 'message' => $response['message']), 200);
                        }
                    } else if (!empty($user2)) {
                        \Log::debug("==== assignUsers user2 not empty");
                            $data = array();
                            foreach ($user2 as $user) {
                                $data[] = array(
                                    'appointment_id' => $dataset['appointmentId'],
                                    'question_action_item_id' => $dataset['action_id'],
                                    'user_id' => $user
                                );
                                $delete_user[] = $user;
                            }
                            $response = $this->action_users->deleteActionItemUsers($data);

                            if ($response) {
                                $users_detail = $this->user->getUserEmailById($delete_user);
                                $layout = 'emails.action_item_remove';
                                $subject = 'Action item email';
                                foreach ($users_detail as $user_detail) {
                                    $email_data = array(
                                        'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                        'system' => 'Simplifya',
                                        'company' => 'Simplifya',
                                        'action_item' => $action_item_details->name,
                                        'inspection_no' => $inspection_no
                                    );
                                    $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject,
                                        $email_data);
                                }
                                $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                                return Response()->json(array('success' => 'true', 'message' => $message), 200);
                            }
                        }else{
                            $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                            return Response()->json(array('success' => 'true', 'message' => $message), 200);
                        }
                    }
            } else {
                foreach ((array)$dataset['location_user'] as $item) {
                    $data_arr[] = array(
                        'appointment_id' => $dataset['appointmentId'],
                        'question_action_item_id' => $dataset['action_id'],
                        'user_id' => $item,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    );
                    $email_user[] = $item;
                }
                //insert record
                $save_action_users = $this->action_users->insertUsers($data_arr);
                if ($save_action_users) {

                    // Send push notifications
//                    $this->sendActionItemAssignedPushNotification($dataset['location_user'], $action_item_id, $appointment_id);
                    $appointment = $this->appointment->find($appointment_id);

                    // Send email notifications
                    $users_detail = $this->user->getUserEmailById($email_user);
                    $layout = 'emails.action_item_assign';
                    $subject = 'You\'ve been assigned an Action Item';

                    foreach($users_detail as $user_detail) {
                        $email_data = array(
                            'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                            'system' => 'Simplifya',
                            'company' => 'Simplifya',
                            'action_item' => $action_item_details->name,
                            'inspection_no' =>$inspection_no
                        );
                        // we uncomment this line due to the ticket SWA-239
                        $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject, $email_data);
                    }
                    $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_SUCCESS');
                    return Response()->json(array('success' => 'true', 'message' => $message), 200);
                } else {
                    $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_FAILED');
                    return Response()->json(array('success' => 'false', 'data' => $message));
                }
            }
        }
    }

    /**
     * Sending push notifications to action item assiged users
     * @param $users
     * @param $action_item_id
     * @param $appointment_id
     * @return array|null
     */
    public function sendActionItemAssignedPushNotification($users, $action_item_id, $appointment_id){
        // Send push notifications
        $data_pushnotif = new \stdClass();
        $users = is_array($users) ? $users : [$users];
        $data_pushnotif->users = array_values($users);
        $data_pushnotif->action_item_id = $action_item_id;
        $data_pushnotif->appointment_id = $appointment_id;

        return $status = event(new AssignUserNotifRequest($data_pushnotif));
    }

    /**
     * Send push notifications when commented on action items
     * @param $users
     * @param $action_item_id
     * @param $appointment_id
     * @param $user_name
     * @return array|null
     */
    public function sendAddCommentPushNotification($users, $action_item_id, $appointment_id, $user_name){
        // Send push notifications
        $data_pushnotif = new \stdClass();
        $data_pushnotif->users = array_values($users);
        $data_pushnotif->action_item_id = $action_item_id;
        $data_pushnotif->appointment_id = $appointment_id;
        $data_pushnotif->commented_users_name = $user_name;

        return $status = event(new AddCommentNotifRequest($data_pushnotif));
    }

    /**
     * Add actopm items for users
     * @param $user1
     * @param $dataset
     * @param $action_item_details
     * @return array
     */
    public function addActionItemUser($user1, $dataset, $action_item_details)
    {
        $data = array();
        foreach ($user1 as $user) {
            $data[] = array(
                'appointment_id' => $dataset['appointmentId'],
                'question_action_item_id' => $dataset['action_id'],

                'user_id' => $user,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            );
            $email_user[] = $user;
        }
        //insert record
        $save_action_users = $this->action_users->insertUsers($data);
        if ($save_action_users) {
            //declare and initialize variables
            $users_detail = $this->user->getUserEmailById($email_user);
            //get inspection no
            $inspection_no = $this->appointment->findWhere(array('id' => $dataset['appointmentId']));
            $layout = 'emails.action_item_assign';
            $subject = 'Action item email';
            foreach ($users_detail as $user_detail) {
                $email_data = array(
                    'from' => 'noreply@simplifya.com',
                    'system' => 'Simplifya',
                    'company' => 'Simplifya',
                    'action_item' => $action_item_details->name,
                    'inspection_no' => $inspection_no[0]->inspection_number
                );
                $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject,
                    $email_data);
            }
            $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_SUCCESS');
            return array('success' => 'true', 'message' => $message);
        } else {
            $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_FAILED');
            return array('success' => 'false', 'data' => $message);
        }
    }

    /**
     * send action item user mails
     *
     * @param $email
     * @param $name
     * @param $layout
     * @param $subject
     * @param $data
     */
    public function sendActionItemMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
    /**
     * Insert comment to action item on the action item list
     *
     * @param ActionCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertComment(ActionCommentRequest $request)
    {
        //declare and initialize valiables
        $appointment_id = $request->appointment_id;
        $action_id  = $request->action_id;
        $comment = $request->comment;
        $entity_tag = $request->entity_tag;
        $notifyData = "";
        $user_id = Auth::user()->id;
        $user_name = Auth::user()->name;
        $filepath = array();
        $comment_data = "";
        $filename = "";
        //dataset array
        $dataset = [
            'appointment_id'            => $appointment_id,
            'question_action_item_id'   => $action_id,
            'content'                   => $comment,
            'status'                    => 1,
            'user_id'                   => $user_id,
            'created_by'                => $user_id,
            'updated_by'                => $user_id
        ];

        $save_comment = $this->comment->create($dataset);
        $entity_id = $save_comment['id'];

        $user_group_id = Auth::user()->master_user_group_id;

        if($user_group_id == 2 || $user_group_id == 3 || $user_group_id == 4) {
            //insert notification mapping to each user
            $users = $this->action_users->getNotifiedUsers($appointment_id, $action_id);
        }
        else{
            $users = array();
            $notifyData = array (
                'user_id' => "",
                'appointment_action_item_comments_id' => "",
                'type' => "",
                'created_by' => "",
                'updated_by' => "",
                'created_at' => "",
                'updated_at' => "",
            );
        }

        $notified_users = [];

        foreach ($users as $key => $user)
        {
            if ($user != $user_id)
            {
                $notifyData = array (
                'user_id' => $user,
                'appointment_action_item_comments_id' => $entity_id,
                'type' => 1,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                );
                $notified_users[] = $user;
                $this->notification->create($notifyData);
            }
        }

        //Get Amazon API instance
        $s3 = AWS::createClient('s3');

        $pic_id = 0;
        $files = $request->imgInp;

        $image_formats = array('jpeg', 'jpg', 'png');

        foreach($files as $file){

         if(!empty($file)) {
             // Create name for file
             $filename = $file->getClientOriginalExtension();
             $path = $file->getRealPath();
             $fileExt = $filename;

             try{
                 $generatedName = uniqid().$user_id.uniqid();
                 $filename = $generatedName.'.'.$filename;

                 // Upload an object to Amazon S3
                 $result = $s3->putObject(array(
                     'Bucket'        => Config::get('aws.bucket'),
                     'Key'           => Config::get('aws.ACTION_COMMENT_IMG_DIR').$filename,
                     'SourceFile'    => $path,
                     'body'          => $path,
                     'ContentType'   => $file->getClientMimeType(),
                     'ACL'           => 'public-read'
                 ));

                 if(isset($result['ObjectURL'])) {
                     // Save comment image in photo table
                     $pic_id = $this->upload->setFile($user_id, $filename, Config::get('simplifya.UPD_TYPE_ACTION_COMMENT_PIC'), $entity_tag, $entity_id, $filename, $appointment_id);
                 }

             } catch (Exception $ex) {
                 $messages = Config::get("messages.FILE_UPLOAD_ERROR");
                 return response()->json(array('success' => 'false', 'message' => $messages));
             }
         }

         if($save_comment){
             if($filename!=""){
                 $filepath[] = Config::get('simplifya.BUCKET_URL').Config::get('aws.ACTION_COMMENT_IMG_DIR').$filename;
             }else{
                 $filepath[] = "";
             }
         }

        }

        // Send push notifications
        $this->sendAddCommentPushNotification($notified_users, $action_id, $appointment_id, $user_name);

        if ($save_comment) {
            //declare and initialize comment_data variable
            $comment_data[] = array(
                'date' => date('m/d/Y h:i A', strtotime($notifyData['created_at'])),
                'username' => $user_name,
                'comment' => $comment,
            );
            $message = Config::get('messages.ACTION_ITEM_USER_COMMENT_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message, 'comment_data' => $comment_data, 'image' => $filepath), 200);
        } else {
            $message = Config::get('messages.ACTION_ITEM_USER_COMMENT_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message, 'data' => $message));
        }

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
     * I don't know section question listing with comments and images
     * @param AppointmentIDRequest $request
     * @return mixed
     */
    public function getUnknownComplianceAnswers(AppointmentIDRequest $request)
    {
        //declare and initialize valiables
        $dataset = "";

        //get appointment id
        $appointment_id = $request->appointment_id;
        $answer_value_id = $request->answer_id;
        $question_id = "";
        $category_id = "";

        $question_list = app('App\Http\Controllers\Api\QuestionController')->otherAppointmentQuestions($appointment_id, $answer_value_id, $category_id, $question_id);

        return $question_list;
    }

    /**
     * get all category based questions list
     * @return mixed
     */
    public function getCategoryBasedQuestions(){
        //declare and initialize variables
        $appointment_id = $_GET['appointment_id'];
        $category_id = $_GET['category_id'];
        $question_id = $_GET['question_id'];
        $answer_value_id = "";

        $question_list = app('App\Http\Controllers\Api\QuestionController')->otherAppointmentQuestions($appointment_id, $answer_value_id, $category_id, $question_id);

        return $question_list;
    }

    /**
     * View Question Comment (note)
     *
     * @return mixed
     */
    public function viewReportQuestionComment()
    {
        //declare and initialize variables
        $question_id      = $_GET['question_id'];
        $appointment_id   = $_GET['appointment_id'];

        $getAppointmentQuestion = $this->appointment_question->findWhere(array('appointment_id' => $appointment_id, 'question_id' => $question_id));

        if($getAppointmentQuestion){
            $data = $getAppointmentQuestion[0]->comment;
            return Response()->json(array('success' => 'true', 'data' => $data), 200);
        } else {
            $message = Config::get('messages.QUESTION_COMMENT_NOT_EXIST');
            return Response()->json(array('success' => 'false', 'data' => $message));
        }
    }

    /**
     * Edit Question Comment (note)
     *
     * @return mixed
     */
    public function editReportQuestionComment()
    {
        //declare and initialize variables
        $question_id      = \Input::get('question_id');
        $question_comment = strip_tags(\Input::get('comment'));
        $appointment_id   = \Input::get('appointment_id');

        //get specific appointment question
        $getAppointmentQuestion = $this->appointment_question->findWhere(array('appointment_id' => $appointment_id, 'question_id' => $question_id));

        if($getAppointmentQuestion){
            //update relevant question comment
            $update = $this->appointment_question->update(array('comment' => $question_comment), $getAppointmentQuestion[0]->id);

            if($update){
                $message = Config::get('messages.QUESTION_COMMENT_UPDATE_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => $message), 200);
            } else {
                $message = Config::get('messages.QUESTION_COMMENT_UPDATE_FAILED');
                return Response()->json(array('success' => 'false', 'data' => $message));
            }
        }else{
            $message = Config::get('messages.QUESTION_COMMENT_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'data' => $message));
        }
    }

    /**
     * get specific action item comment
     *
     * @return mixed
     */
    public function getReportActionItemsComment(){
        //declare and initialize variables
        $action_item_id = $_GET['action_item_id'];
        $comment_id = $_GET['comment'];
        $appointment_id = $_GET['appointment_id'];

        $getActionComment = $this->comment->findWhere(array('id' => $comment_id));


        if($getActionComment){
            $data = $getActionComment[0]->content;
            return Response()->json(array('success' => 'true', 'data' => $data), 200);
        } else {
            $message = Config::get('messages.ACTION_ITEM_COMMENT_NOT_EXIST');
            return Response()->json(array('success' => 'false', 'data' => $message));
        }

    }

    /**
     * update and store action item comment
     * @param ActionCommentRequest $request
     *
     * @return mixed
     */
    public function updateReportActionItems(ActionCommentRequest $request){
        $action_item_id = $request->action_item_id;
        $comment_id = $request->comment_id;
        $appointment_id = $request->appointment_id;
        $comment = $request->comment;

        $updateActionComment = $this->comment->update(array('content' => $comment), $comment_id);

        if($updateActionComment){
            $message = Config::get('messages.ACTION_ITEM_COMMENT_UPDATE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        } else {
            $message = Config::get('messages.ACTION_ITEM_COMMENT_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'data' => $message));
        }
    }

    public function loadReportListDataAjax($appointment_id, $tree='',$order_by='category') {
        return $this->loadReportListData($appointment_id, $tree, $order_by);
    }

    /**
     * Load and prepare reports list
     * @param $appointment_id
     * @param $tree
     * @return $dataset_all
     */
    public function loadReportListData($appointment_id, $tree='',$order_by=''){
       // Get data from DB
      $result = $this->appointment_question->getAppointmentReportList($appointment_id,$order_by);
      //declare valiables
      $dataset = [];
      $categories = [];
      //  \Log::debug("==== dataset ........" . print_r($result, true));
        
      foreach($result as $data){
         // Questions
         $dataset[$data->question_id]['question_id'] = $data->question_id;
         $dataset[$data->question_id]['parent_question_id'] = $data->parent_question_id;
         $dataset[$data->question_id]['question'] = $data->question;
         $dataset[$data->question_id]['explanation'] = $data->explanation;
         $dataset[$data->question_id]['appointment_id'] = $data->appointment_id;
         $dataset[$data->question_id]['category_id'] = "" . $data->category_id;
         $dataset[$data->question_id]['category_name'] = $data->category_name;
         $dataset[$data->question_id]['comment'] = $data->comment;
         $dataset[$data->question_id]['option_value'] = $data->option_value;
         $dataset[$data->question_id]['report_status'] = $data->report_status;

         // Categories
         $categories[$data->category_id]['id'] = $data->category_id;
         $categories[$data->category_id]['name'] = $data->category_name;

         // Action items
         if($data->answer_value_id == 2 or $data->answer_value_id == 3){
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['id'] = $data->action_item_id;
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['name'] = $data->action_item_name;
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['status'] = $data->action_item_status;
         }

         // Set users
         if($data->action_item_user_id != ''){
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['assigned_users'][$data->action_item_user_id]['id'] = $data->action_item_user_id;
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['assigned_users'][$data->action_item_user_id]['name'] = $data->user_name;
         }

         // Answers
         $dataset[$data->question_id]['answers'][$data->answer_id]['answer_id'] = "" . $data->answer_id;
         $dataset[$data->question_id]['answers'][$data->answer_id]['answer_value_name'] = "" . $data->answer_value_name;
         $dataset[$data->question_id]['answers'][$data->answer_id]['answer_value_id'] = "" . $data->answer_value_id;

         $dataset[$data->question_id]['answer_value_name'] = "" . $data->answer_value_name;
         $dataset[$data->question_id]['answer_value_id'] = "" . $data->answer_value_id;

         // Set images
         if($data->image_name != ''){
            $dataset[$data->question_id]['images'][] = Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.ACTION_COMMENT_IMG_DIR'), "/"). '/' . $data->image_name;
         }

         //set citations
          $citations = $this->citation->getCitations($data->question_id)->toArray();
          $citation_list = "";
          foreach($citations as $citation)
          {
              $citation_list .= " ".$citation['citation'].",";
          }
          //$dataset[$data->question_id]['citations'] = $citations;
          $citation_list = substr($citation_list, 0, -1);
          $dataset[$data->question_id]['citation_list'] = $citation_list != false ? "References : ".$citation_list : '';
      }
      //\Log::debug("==== dataset ........" . print_r($dataset, true));
      // Init formated dataset
      $dataset_fotmated = [];

      // Format dataset
      foreach($dataset as $key => $data){
         // Set undefined images
         $data['images'] = isset($data['images']) ? array_values(array_unique(array_values($data['images']))) : [];

         // Set undefined action item users
         if(isset($data['action_items'])){
            foreach($data['action_items'] as $action_item){
               $data['action_items'][$action_item['id']]['assigned_users'] = isset($data['action_items'][$action_item['id']]['assigned_users']) ? array_values($data['action_items'][$action_item['id']]['assigned_users']) : [];
            }
         }
         // Set undefined answers
         $data['answers'] = isset($data['answers']) ? array_values($data['answers']) : [];
         // Set undefined action items
         $data['action_items'] = isset($data['action_items']) ? array_values($data['action_items']) : [];

         $dataset_fotmated[$key] = $data;
      }

      // Get inspection report tree view configuration from master config
        $master_data_tree_view = $this->master_data->findBy('name', 'INSPECTION_REPORT_TREE_VIEW');
        $enable_tree_view = false;
        if (isset($master_data_tree_view)) {
            $enable_tree_view = ($master_data_tree_view->value == 1)? true : false;
        }

        // Build question tree
      if($tree != '' && $enable_tree_view){
         $dataset_fotmated = $this->buildTree($dataset_fotmated, 0);
      }else {
          /*$index = 1;
          foreach ($dataset_fotmated as &$dataSet){
              $dataSet['level'] = $index;
              $index++;
          }*/
          $tree_fotmated = $this->buildTree($dataset_fotmated, 0);
          $dataset_fotmated = $this->flattenQuestionList($tree_fotmated);
          foreach ($dataset_fotmated as &$dataSet){
              $dataSet['treeview'] = (int) $enable_tree_view;
              if (isset($dataSet['questions'])) {
                  unset($dataSet['questions']);
              }
          }
      }

      $dataset_all = [
          'categories' => array_values($categories),
          'questions' => array_values($dataset_fotmated),
      ];


      return $dataset_all;
    }

    public function flattenQuestionList($dataset_fotmated,&$flatten_data=array()){
        foreach ($dataset_fotmated as $dataset){

            array_push($flatten_data,$dataset);

            if (isset($dataset['questions'])){
                foreach ($dataset['questions'] as $question){
                    $sub_questions=array();
                    $sub_questions=[$question];
                    $this->flattenQuestionList($sub_questions,$flatten_data);
                }
            }

        }


        return $flatten_data;

    }
    /**
     * Load and prepare Category List on NavigationloadNavigationCategories
     * @return $dataset_all
     */
    public function loadNavigationCategories(){
    //declare valiables
        $user = Auth::user();

        $master_answer_arr = array();

        //get appointment id
        $appointment_id = $_GET['appointment_id'];
        $answer_id = $_GET['answer_id'];

        $getMasterAnswers = $this->appointment_question->findAnsweredAppointmentQuestions($appointment_id);

        foreach ($getMasterAnswers as $item)
        {
            $master_answer_arr[] = $item->master_answer_id;
        }


        $result = $this->appointment_question->getAllNavigationCategories($appointment_id, $master_answer_arr, $answer_id, $user->id, $user->master_user_group_id);



      $categories = [];

      foreach($result as $data){


         // Categories
         $categories[$data->id]['id'] = $data->id;
         $categories[$data->id]['name'] = $data->name;


      }


      $dataset_all = [
          'categories' => array_values($categories)
      ];

      return $dataset_all;
    }

    /**
     * Build questions tree
     * @param array $elements
     * @param $parent_id
     * @return $branch
     */
    function buildTree(array &$elements, $parent_id = 0, $level=false) {

      $branch = array();$i = 0;
      foreach($elements as &$element) {


         if($element['parent_question_id'] == $parent_id){
             if ($level) {
                 $level_name = $level. '.'.++$i;
             }
             else {
                 $level_name = $level.++$i;
             }
             $element[ 'level' ] = $level_name;
            $children = $this->buildTree($elements, $element['question_id'], $level_name);
            if($children){
               $element['questions'] = $children;
            }
            $branch[$element['question_id']] = $element;
            unset($element);
         }
      }
      return $branch;
    }

    /**
     * Finalize report
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizeReport(){
        //declare and initialize variables
        $appointmentId = $_POST['id'];
        $status = $_POST['status'];
        $shareMjb = $_POST['shareMjb'];
        $entityType = $_POST['entityType'];

        $data = array('report_status' => $status, 'share_mjb' => $shareMjb);

        $response = $this->appointment->update($data, $appointmentId);
        if($response){
            $appointment = $this->appointment->find($appointmentId);

            // email send to MJ Business
            $emails = array();

            if($entityType ==  Config::get('simplifya.MarijuanaBusiness')){
                $users = $this->user->findWhere(array("company_id" => $appointment->to_company_id, "master_user_group_id" => Config::get('simplifya.MjbMasterAdmin')));
                foreach($users as $user){
                    array_push($emails, $user->email);
                }
            }
            else if($entityType == Config::get('simplifya.ComplianceCompany')){
                $users = $this->user->findWhere(array("company_id" => $appointment->from_company_id, "master_user_group_id" => Config::get('simplifya.CcMasterAdmin')));
                foreach($users as $user){
                    array_push($emails, $user->email);
                }

                $mjUsers = $this->user->findWhere(array("company_id" => $appointment->to_company_id, "master_user_group_id" => Config::get('simplifya.MjbMasterAdmin')));
                foreach($mjUsers as $user){
                    array_push($emails, $user->email);
                }
            }
            else if($entityType == Config::get('simplifya.GovernmentEntity') && $shareMjb == 0){
                $users = $this->user->findWhere(array("company_id" => $appointment->from_company_id, "master_user_group_id" => Config::get('simplifya.GeMasterAdmin')));
                foreach($users as $user){
                    array_push($emails, $user->email);
                }
            }
            else if($entityType == Config::get('simplifya.GovernmentEntity') && $shareMjb == 1){
                $users = $this->user->findWhere(array("company_id" => $appointment->from_company_id, "master_user_group_id" => Config::get('simplifya.GeMasterAdmin')));
                foreach($users as $user){
                    array_push($emails, $user->email);
                }

                $mjUsers = $this->user->findWhere(array("company_id" => $appointment->to_company_id, "master_user_group_id" => Config::get('simplifya.MjbMasterAdmin')));
                foreach($mjUsers as $user){
                    array_push($emails, $user->email);
                }
            }

            $location = $this->location->getLocationByID($appointment->company_location_id);
            $locationName = isset($location)? $location->name : '';
            $auditor = $this->user->find($appointment->assign_to_user_id);
            $auditorCompany = $this->company->find($auditor->company_id);

            $auditDate   = date('m/d/Y', strtotime(str_replace('/', '-', $appointment->inspection_date_time)));
            $auditTime   = date('g:i a', strtotime(str_replace('/', '-', $appointment->inspection_date_time)));
            $allLicenses  = $this->appointment_classification->getLicenceDataWithLicenseNumberByAppointmentId($appointment->id, $appointment->company_location_id);
            $licenses = [];
            foreach ($allLicenses as $l) {
                $licenseNumber = (!empty($l->license_number))? "($l->license_number)" : '';
                $licenses[] = $l->name . ' - ' . $licenseNumber;
            }
            $auditType = $this->appointment_classification->getAuditTypeByAppointmentId($appointment->id);
            \Log::debug("==== audit type " . print_r($auditType->toArray(), true));
            // Send email
            $mail = new sendMail;
            $mail->mailSender('emails.inspection_report_finalize',
                $emails,//$emails,
                Config::get('simplifya.COMPANY'),
                'Your audit report is ready to view',
                array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                    'system' => 'Simplifya',
                    'appointmentId' => $appointment->id,
                    'inspectionNumber' => $appointment->inspection_number,
                    'company' => Config::get('simplifya.COMPANY'),
                    'locationName' => $locationName,
                    'auditorName' => $auditor->name,
                    'auditorCompany' => $auditorCompany->name,
                    'inspection_Date' => $auditDate,
                    'inspection_Time' => $auditTime,
                    'allLicenses' => implode(", ", $licenses),
                    'auditType' => $auditType->name
                )
            );

            return Response()->json(array('success' => 'true'), 200);
        }
        else{
            return Response()->json(array('success' => 'false'), 200);
        }
    }

    /**
     * Get answers count
     *
     * @param string $appointment_id
     * @param string $category_id
     * @return array
     */
    public function getAnswersCount($appointment_id='', $category_id=''){
      // Get data set from DB
      $results = $this->question->getAnswersCount($appointment_id, $category_id);

        //declare dataset array
      $dataset = [];

      foreach($results as $data){
         if($data->master_answer_id == 0){
            $dataset[$data->category_id][($data->answer_value_id == '') ? 'no' : $data->answer_value_id]['noAnswer'][] = $data->master_answer_id;
         }else{
            $dataset[$data->category_id][($data->answer_value_id == '') ? 'no' : $data->answer_value_id]['hasAnswer'][] = $data->master_answer_id;
         }
      }

      return $dataset;

    }

    /**
     * get report type view
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReportTypes()
    {
        return view('report.reportsType')->with(array('page_title' => 'Reports Manager'));
    }

    /**
     * get company list view
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyList()
    {
        $entity = $this->entity->getPublicEntities();
        return view('report.companyList')->with(array('page_title' => 'Company List', 'entities' => $entity));
    }

    /**
     * get all inspection reports view
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInspectionReport()
    {
        //declare and initialize valiables
        $entityType = app('App\Http\Controllers\Web\UserController')->getUserEntitiyType(Auth::user()->id);
        $mjBusinesses = $this->company->findWhere(array('entity_type' => 2));
        $companies = $this->company->findCompanyByEntity(array(3,4));
        $user = $this->user->find(Auth::user()->id, array("*"));
        $isMjDisabled = false; $isCompanyDisabled = false;

        return view('report.inspectionReport')->with(array('isMjDisabled' => $isMjDisabled, 'isCompanyDisabled' => $isCompanyDisabled,  'mjBusinesses' => $mjBusinesses, 'companies' => $companies, 'companyId'=> $user->company_id, 'type'=> 's_admin', 'page_title' => 'Audit Reports'));
    }

    /**
     * get user list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserList()
    {
        //declare and initialize variables
        $group_id = Auth::user()->master_user_group_id;

        // if simplifiya supper admin
        if($group_id == Config::get('simplifya.MasterAdmin')) {
            $entityTypes = $this->entityType->all();
            $companies = $this->company->all();

            return view('report.userList')->with(array('entityTypes' => $entityTypes, 'companies' => $companies, 'page_title' => 'Company Users'));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }

    }

    /**
     * get company locations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyLocations()
    {
        $entity = $this->entity->getPublicEntities();
        $country = $this->country->findWhere(array('status' => 1));
        $state = $this->state->findWhere(array('status' => 1));
        //$city = $this->city->findWhere(array('status' => 1));
        $city = $this->city->getAllCitiesByStatus(1);

        return view('report.companyLocationList')->with(array('page_title' => 'Company Location List',
            'entities' => $entity,
            'countries' => $country,
            'states' => $state,
            'cities' => $city
        ));
    }

    /**
     * get company locations
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function CompanyLocations(Request $request)
    {
        //declare and initialize variables
        $country_data = array();
        $data = array();
        $business_name = $request->business_name;
        $entity_type = $request->entity_type;
        $country = $request->country;
        $state = $request->state;
        $city = $request->city;
        $countries = $this->country->all(array('*'));
        foreach($countries as $value) {
            $country_data[] = array('id'=>$value->id, 'name'=>$value->name);
        }
        $company_locations = $this->location->getCompanyLocations($business_name, $entity_type, $country, $state, $city);

        foreach($company_locations as $company_location) {
            $key = $this->searchCountryId($company_location->country_id, $country_data);
            if($entity_type == '') {
                $data[] = array(
                    $company_location->company_name,
                    $company_location->location_name,
                    $company_location->city_name,
                    $company_location->state_name,
                    $country_data[$key]['name'],
                    $company_location->phone_number
                );
            } else {
                if ($entity_type == $company_location->entity_type) {
                    $data[] = array(
                        $company_location->company_name,
                        $company_location->location_name,
                        $company_location->city_name,
                        $company_location->state_name,
                        $country_data[$key]['name'],
                        $company_location->phone_number
                    );
                }
            }

        }
        return response()->json(["data" => $data]);
    }

    /**
     * search by country id
     *
     * @param $id
     * @param $country_data
     * @return int|null|string
     */
    private function searchCountryId($id, $country_data) {
        foreach ($country_data as $key => $val) {
            if ($val['id'] === $id) {
                return $key;
            }
        }
        return null;
    }

    /**
     * export company locations
     *
     * @param Request $request
     * @return json
     */
    public function exportCompanyLocations(Request $request)
    {
        $country_data = array();
        $data = array();
        $business_name = $request->company_name;
        $entity_type = $request->entity_type;
        $country = $request->country;
        $state = $request->state;
        $city = $request->city;
        $countries = $this->country->findWhere(array('status' => 1));
        foreach($countries as $value) {
            $country_data[] = array('id'=>$value->id, 'name'=>$value->name);
        }
        $company_locations = $this->location->getCompanyLocations($business_name, $entity_type, $country, $state, $city);
        foreach($company_locations as $company_location) {
            $key = $this->searchCountryId($company_location->country_id, $country_data);

            if($entity_type == $company_location->entity_type) {
                $data[] = array(
                    $company_location->company_name,
                    $company_location->location_name,
                    $company_location->city_name,
                    $company_location->state_name,
                    $country_data[$key]['name'],
                    $company_location->phone_number
                );
            } else {
                $data[] = array(
                    $company_location->company_name,
                    $company_location->location_name,
                    $company_location->city_name,
                    $company_location->state_name,
                    $country_data[$key]['name'],
                    $company_location->phone_number
                );
            }
        }

        // Define headers
        $headers = ['business/Entity Name', 'Location Name', 'City', 'State', 'Country', 'Phone Number'];
        // Define file name
        $filename = "CompanyLocationList.csv";
        // Create CSV file
        return $this->csv->create($data, $headers, $filename);
    }

    /**
     * get state cities by country ids
     *
     * @param $country_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStateCityByCountry($country_id)
    {
        $states = $this->state->findWhere(array('country_id' => $country_id, 'status' => 1));
        $city = $this->city->getCityByCompanyId($country_id);
        return Response()->json(array('success' => 'false','state' => $states, 'city' => $city), 200);
    }

    /**
     * get country cities by state ids
     *
     * @param $state_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountryCityByState($state_id)
    {
        $city = $this->city->getCityByStatus($state_id, 1);
        $country = $this->state->getCountryByStateId($state_id);

        return Response()->json(array('success' => 'false','country' => $country, 'city' => $city), 200);
    }

    /**
     * get country states by city ids
     *
     * @param $city_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountryStateByCity($city_id)
    {
        //get states by city id from city repository
        $state = $this->city->getStateByCityId($city_id);
        //get countries by city id from city repository
        $country = $this->city->getCountryByCityId($city_id);

        return Response()->json(array('success' => 'false','country' => $country, 'state' => $state), 200);
    }

    /**
     * Search reports list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportInspectionReport(Request $request)
    {
        //declare and initialize variables
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $audit_type = $request->audit_type;
        $status = $request->status;
        $entity_type = Session('entity_type');
        $user_id = Auth::User()->id;
        $master_user_group_id = Auth::User()->master_user_group_id;
        $company_id = Auth::User()->company_id;
        $table = 'appointments';

        $sWhere = "";
        $sGroupBy = " GROUP BY `appointments`.`id` ";

        if (($startDate != "") || ($audit_type != "") || ($status != '')) {
            $sWhere = "";

            if($startDate != '') {
                if ($sWhere == "") {
                    $sWhere .= "WHERE ( appointments.`inspection_date_time` between '%" . $startDate . "%' and '%" . $endDate . "%' ";
                } else {
                    $sWhere .= " AND appointments.`inspection_date_time` between '%" . $startDate . "%' and '%" . $endDate . "%' ";
                }

            }
            if($status !=""){
                if ($sWhere == "") {
                    $sWhere .= "WHERE ( appointments.`report_status` = " . $status . " ";
                } else {
                    $sWhere .= " AND appointments.`report_status` = " . $status . " ";
                }

            }
            if($audit_type !="") {
                if ($sWhere == "") {
                    $sWhere .= "WHERE ( appointment_classifications.`option_value` = " . $audit_type . " ";
                } else {
                    $sWhere .= " AND appointment_classifications.`option_value` = " . $audit_type . " ";
                }
            }

//            $sWhere .= "AND (`appointments`.`appointment_status`= 1 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ))";
            // modified above stmt by removing `appointments`.`appointment_status` for the ticket SWA-30
            $sWhere .= "AND (`appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ))";

        }
        else
        {
//            $sWhere .= "WHERE `appointments`.`appointment_status`= 1 AND `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
            // modified above stmt by removing `appointments`.`appointment_status` for the ticket SWA-30
            $sWhere .= "WHERE `appointment_classifications`.`entity_type` = 'AUDIT_TYPE' ";
        }

        if ($master_user_group_id == Config::get('simplifya.MasterAdmin'))
        {
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS `appointments`.`id` as `appointment_id`, `appointments`.`appointment_status` as `appointment_status`,`appointments`.`from_company_id` as `company_name`, `appointments`.`to_company_id` as `mj_bussiness_name`, `appointments`.`inspection_number` as `inspection_no`, `appointments`.`inspection_date_time` as `inspection_date_time`, `appointment_classifications`.`option_value` as `audit_type`, `appointments`.`appointment_status` as `status`, `appointments`.`start_inspection` as `start_time`, `appointments`.`finish_inspection` as `end_time`,  `appointments`.`report_status` as `report_status` FROM `".$table."` "."JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` ".$sWhere;
        }

        $rResult =  $this->appointment->getInspectionRequests($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            //declare and initialize valiables
            $d = str_replace("\/",'/',$item->inspection_date_time);
            $Date = date('d-m-Y', strtotime($d));
            $time = date('h:i:s A', strtotime($d));
            $to_company = $this->company->getCompany($item->mj_bussiness_name);
            $from_company = $this->company->getCompany($item->company_name);

            $date1=date_create($item->start_time);
            $date2=date_create($item->end_time);
            $diff=date_diff($date1,$date2);
            $day = '';
            if($diff->d != 0) {
                $day .= $diff->d.' days and ';
            }

            if($diff->h != 0) {
                $day .= $diff->h.' hours and ';
            }
            if($diff->m != 0) {
                $day .= $diff->m.' minutes';
            }

            $status_txt = "";

            switch ($item->report_status){
                case Config::get('simplifya.REPORT_PENDING'):
                    $status_txt = Config::get('simplifya.REPORT_PENDING_TXT');
                    break;
                case Config::get('simplifya.REPORT_COMPLETED'):
                    $status_txt = Config::get('simplifya.REPORT_COMPLETED_TXT');
                    break;
                case Config::get('simplifya.REPORT_STARTED'):
                    $status_txt =  Config::get('simplifya.REPORT_STARTED_TXT');
                    break;
                case Config::get('simplifya.REPORT_FINALIZED'):
                    $status_txt =  Config::get('simplifya.REPORT_FINALIZED_TXT');
                    break;
            }

            $appointment_status = '';
            if ($item->appointment_status == 1) {
                $appointment_status  = Config::get('simplifya.APPOINTMENT_ACTIVE_TXT');
            }else if ($item->appointment_status == 2) {
                $appointment_status  = Config::get('simplifya.APPOINTMENT_CANCELED_TXT');
            }

            //dataset array
            $dataset[] = array(
                $Date,
                $time,
                $to_company[0]->name,
                $from_company[0]->name,
                $item->audit_type==1 ? "In-House" : "3rd Party",
                ($day != '') ?$day :'-',
                '--',
                $status_txt,
                $appointment_status
            );

        }
        // Define headers
        $headers = ['Audit Date', 'Audit Time', 'Mjb Name', 'Audit Party', 'Audit Type', 'Duration', 'Compliant Rate', 'Status', 'Appointment Status'];
        // Define file name
        $filename = "InspectionList.csv";
        // Create CSV file
        return $this->csv->create($dataset, $headers, $filename);
    }


}

