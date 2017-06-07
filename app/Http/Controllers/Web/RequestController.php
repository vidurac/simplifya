<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\InspectionRepository;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\UsersRepository;
use App\Repositories\RequestsRepository;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\MasterCityRepository;
use App\Repositories\MasterStateRepository;
use App\Repositories\MasterCountryRepository;

class RequestController extends Controller
{
    private $company;
    private $inspection;
    private $user;
    private $request;
    private $location;
    private $city;
    private $state;
    private $country;

    /**
     * RequestController constructor.
     * @param CompanyRepository $company
     * @param InspectionRepository $inspection
     * @param UsersRepository $user
     * @param RequestsRepository $request
     * @param CompanyLocationRepository $location
     * @param MasterCountryRepository $country
     * @param MasterCityRepository $city
     * @param MasterStateRepository $state
     */
    public function __construct(CompanyRepository $company,
                                InspectionRepository $inspection,
                                UsersRepository $user,
                                RequestsRepository $request,
                                CompanyLocationRepository $location,
                                MasterCountryRepository $country,
                                MasterCityRepository $city,
                                MasterStateRepository $state)
    {
        $this->company = $company;
        $this->inspection = $inspection;
        $this->user = $user;
        $this->request = $request;
        $this->location = $location;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*
         * Initialize and assign variables
         */
        $locations = '';
        $user_id = Auth::user()->id;
        $company_id = Auth::user()->company_id;
        $companies = $this->company->getComplianceCompanyList(); //get all compliance and government entity company list
        if(Auth::user()->master_user_group_id == 3) {
            $locations = $this->company->getUserLocations($user_id);
        } else {
            $locations = $this->company->with(['companyLocation' => function ($query) {
                $query->where('status', '=', 1);
            }])->getCompanyLocations($company_id); //get approved company details to get company location
        }

        return view('request.create')->with('company', $companies)->with('location', $locations)->with('page_title', 'REQUEST AN AUDIT FROM A 3RD PARTY AUDITOR')->with('user_group', Auth::user()->master_user_group_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
         * Initialize and assign variables
         */
        $company_id           = $request->company_name;
        $location             = $request->company_location;
        $message              = $request->message;
        $user_id              = Auth::user()->id;

        $company_name = $this->company->getComplianceCompanyByID($company_id);
        $sendInspectionReq = $this->inspection->sendInspectionRequest($company_id);

        $mjbCompany = $this->company->getComplianceCompanyByID(Auth::user()->company_id);
        $mjb_location = $this->location->find(array('id', $location));

        $city       =   $this->city->find(array('id', $mjb_location[0]->city_id));
        $state      =   $this->state->find(array('id', $city[0]->status_id));
        $country    =   $this->country->find(array('id', $state[0]->country_id));

        foreach ($sendInspectionReq as $item) {
            $compliance_user_mail = $item->email;

            $name = $company_name[0]->name;
            $email_template = 'emails.inspection_request';
            $subject = $name." received an Audit Request";
            $dataArray = array(
                'location' => $location,
                'message'  => $message,

                'mjb_name' => $mjbCompany[0]->name,
                'mjb_location_name' => $mjb_location[0]->name,
                'mjb_location_address_1' => $mjb_location[0]->address_line_1,
                'mjb_location_address_2' => $mjb_location[0]->address_line_2,
                'mjb_location_city' => $city[0]->name,
                'mjb_location_state' => $state[0]->name,
                'mjb_location_country' => $country[0]->name,
                'mjb_location_contact_no' => $mjb_location[0]->phone_number,
                'mjb_location_zip_code' => $mjb_location[0]->zip_code,
                'request_note' => $message,

                'name'     => $company_name[0]->name,
                'from'     => 'noreply@simplifya.com',
                'system'   => 'Simplifya',
            );

            $this->sendEmail($compliance_user_mail, $name, $email_template, $subject, $dataArray);

        }

        /*
         * Initialize and assign variables
         */
        $from_company          = Auth::user()->company_id;
        $to_company            = $company_name[0]->id;
        $from_company_location = $location;

        $dataset = array(
            'from_company_id'       => $from_company,
            'to_company_id'         => $to_company,
            'company_location_id'   => $from_company_location,
            'comment'               => $message,
            'status'                => 0,
            'created_by'            => $user_id
        );

        $create = $this->inspection->createInspectionRequest($dataset);
        
        if($create){
            return Response()->json(array('success' => 'true'), 200);
        }
    }

    /**
     * Request Manager
     * @return json
     */
    public function manage()
    {
        /*
         * Initialize and assign variables
         */
        $user = Auth::user();
        $c_companies = $this->company->getComplianceCompanyForRequest($user); //get all compliance company and government entity list
        $m_companies = $this->company->getMarijuanaCompanyForRequest($user); //get all marijuana company list
        return view('request.manage')->with('c_company', $c_companies)->with('m_company', $m_companies)->with('page_title', 'Manage Audit Requests');
    }

    /**
     * Search manager list
     * @param $request
     * @return json
     */
    public function searchRequests(Request $request)
    {
        /*
         * Initialize and assign variables
         */
        $user = Auth::user();
        $table = 'companies';
        $columns = array('id', 'created_at', 'to_company_id', 'from_company_id', 'status', 'manage');
       
        $response = $this->getFilteredCompanyList($table, $columns, $user->company_id, $user->master_user_group_id, $user->id);
        
        return response()->json($response);
    }

    /**
     * filter all companies
     * @param $table
     * @param $columns
     * @param string $company
     * @param string $group
     * @return array
     */
    public function getFilteredCompanyList($table, $columns, $company = '', $group = '', $user_id = '')
    {
        $index_column = "id";

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
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "companies.`name` ". $sortDir .", ";
                    } else if($_GET['iSortCol_0']==3) {
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "companies.`name` ". $sortDir .", ";
                    } else {
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'DESC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`id` ". $sortDir .", ";
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
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {

            $sWhere = "WHERE (";
            for ($i = 0; $i < count($columns); $i++) {

                //explode row date value
                $date = explode( "," , $_GET['sSearch_' . $i] );

                if($columns[$i]=="created_at" && $date[0]!=null){
                    $sWhere .= "requests.`" . $columns[$i] . "` between '%" . $date[0] . "%' and '%" . $date[1] . "%' ";
                }elseif($columns[$i]=="status"){
                    $sWhere .= "requests.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }elseif($columns[$i]=="to_company_id"){
                    $sWhere .= "requests.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }elseif($columns[$i]=="from_company_id"){
                    $sWhere .= "requests.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }
            }

            $sWhere = substr_replace($sWhere, "", -3);
        }
        else
        {
            //
        }

        // Individual column filtering
        for ($i = 0; $i < count($columns); $i++) {

            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }

                $date = explode( "," , $_GET['sSearch_' . $i] );

                if(isset($date) && isset($date[1])) {
                    $old_start_format = str_replace("\/", '/', $date[0]);
                    $new_start_date = date('Y-m-d', strtotime($old_start_format));

                    $old_end_format = str_replace("\/", '-', $date[1]);
                    $new_end_date = date('Y-m-d', strtotime($old_end_format));
                }

                if($columns[$i]=="created_at" && $date[0]!=null){
                    $sWhere .= "(requests.`" . $columns[$i] . "` between '" . $new_start_date . "' and '" . $new_end_date . "') ";
                }else if($columns[$i]=="status"){
                    $sWhere .= "requests.`" . $columns[$i] . "` = " . $_GET['sSearch_' . $i] . " ";
                }else if($columns[$i]=="to_company_id"){
                    $sWhere .= "requests.`". $columns[$i] . "` = " . $_GET['sSearch_' . $i] . " ";
                }else if($columns[$i]=="from_company_id"){
                    $sWhere .= "requests.`". $columns[$i] . "` = " . $_GET['sSearch_' . $i] . " ";
                }else{
                    $sWhere = "";
                }
            }
        }

        if ($group == Config::get('simplifya.MasterAdmin'))
        {
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS requests.id, requests.created_at, requests.to_company_id, requests.from_company_id, requests.`status` FROM `".$table."` "."JOIN requests ON companies.id=requests.from_company_id ".$sWhere." ".$sOrder." ".$sLimit;
        }
        else if ($group == Config::get('simplifya.MjbMasterAdmin'))
        {
            if ($company != '')
            {
                $sWhere .= " AND requests.from_company_id = $company";
            }
            // SQL queries get data to display
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS requests.id, requests.created_at, requests.to_company_id, requests.from_company_id, requests.`status` FROM `".$table."` "."JOIN requests ON companies.id=requests.from_company_id ".$sWhere." ".$sOrder." ".$sLimit;
        } else if($group == Config::get('simplifya.MjbManager')){
            $locations  = $this->request->getUserLocations($user_id);

            if(isset($locations[0])) {
                $locations_list = "'". implode("', '", $locations) ."'";
                $sWhere = "WHERE requests.company_location_id IN ($locations_list)";
            }
            if ($company != '')
            {
                $sWhere .= " AND requests.from_company_id = $company";
            }

            // SQL queries get data to display
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS requests.id, requests.created_at, requests.to_company_id, requests.from_company_id, requests.`status` FROM `".$table."` "."JOIN requests ON companies.id=requests.from_company_id ".$sWhere." ".$sOrder." ".$sLimit;
        } else if ($group == Config::get('simplifya.CcMasterAdmin'))
        {
            if ($company != '')
            {
                $sWhere .= " AND requests.to_company_id = $company";
            }
            // SQL queries get data to display
            $sQuery = "SELECT SQL_CALC_FOUND_ROWS requests.id, requests.created_at, requests.to_company_id, requests.from_company_id, requests.`status` FROM `".$table."` "."JOIN requests ON companies.id=requests.to_company_id ".$sWhere." ".$sOrder." ".$sLimit;    
        }

        $rResult =  $this->company->getInspectionRequests($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            $d = str_replace("\/",'/',$item->created_at);
            $Date = date('m-d-Y', strtotime($d));

            $to_company = $this->company->getCompany($item->to_company_id);
            $from_company = $this->company->getCompany($item->from_company_id);

            $status_txt = "";

            switch ($item->status){
                case 0:
                    $status_txt = "<span class=\"badge badge-warning\">Pending</span>";
                    break;
                case 1:
                    $status_txt =  "<span class=\"badge badge-success\">Accepted</span>";
                    break;
                case 2:
                    $status_txt = "<span class=\"badge badge-item \">Canceled</span>";
                    break;
                case 3:
                    $status_txt = "<span class=\"badge badge-danger\">Rejected</span>";
                    break;
            }

            $editInspection = "<a href=\"/request/edit/". $item->id ."\" class=\"btn btn-info btn-circle\"><i class=\"fa fa-paste\"></i></a>";

            //dataset array
            $dataset[] = array(
                'created_at'        => $Date,
                'to_company_id'     => $from_company[0]->name,
                'from_company_id'   => $to_company[0]->name,
                'status'            => $status_txt,
                'manage'            => $editInspection
            );

        }

        if ($group == Config::get('simplifya.MasterAdmin')){

            $FilteredTotal = $this->request->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->request->getTotalNumber();
            $iTotal = $total[0]->count;

        } else if (($group == Config::get('simplifya.MjbMasterAdmin')) || ($group == Config::get('simplifya.MjbManager'))){

            if ($company != '')
            {
                $companyId = " WHERE requests.from_company_id = $company";
            }else{
                $companyId = "";
            }

            $FilteredTotal = $this->request->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->request->getFilteredTotalNumber($companyId);
            $iTotal = $total[0]->count;

        } else if ($group == Config::get('simplifya.CcMasterAdmin')){

            if ($company != '')
            {
                $companyId = " WHERE requests.to_company_id = $company";
            }else{
                $companyId = "";
            }

            $FilteredTotal = $this->request->currentRow();
            $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

            // Get total number of rows in table
            $total = $this->request->getFilteredTotalNumber($companyId);
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
                if ( $columns[$i] == 'created_at' ) {
                    $row[] = $aRow['created_at'];
                }
                if ( $columns[$i] == 'to_company_id' ) {
                    $row[] = $aRow['to_company_id'];
                }
                if ($columns[$i] == 'from_company_id' ) {
                    $row[] = $aRow['from_company_id'];
                }
                if ( $columns[$i] == 'status' ) {
                    $row[] = $aRow['status'];
                }
                if( $columns[$i] == 'manage') {
                    $row[] = $aRow['manage'];
                }
            }
            $output['aaData'][] = $row;
        }
        return $output ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($request_id)
    {
        //declare and initialize variables
        $company_id = Auth::User()->company_id;
        $entity_type = Session('entity_type');
        $id = $request_id;
        $req_details = $this->inspection->getRequestDetails($id);

        if(count($req_details) > 0) {
            if($entity_type == Config::get('simplifya.MarijuanaBusiness')) {
                if($req_details[0]->from_company_id == $company_id) {
                    return view('request.editRequest')->with('req_details', $req_details)->with('page_title', 'Audit Request');
                } else {
                    $message =  Config::get('messages.ACCESS_DENIED');
                    return Redirect::to("/dashboard")->with('error', $message);
                }
            } elseif(($entity_type == Config::get('simplifya.ComplianceCompany')) || ($entity_type == Config::get('simplifya.GovernmentEntity'))) {
                if($req_details[0]->to_company_id == $company_id) {
                    return view('request.editRequest')->with('req_details', $req_details)->with('page_title', 'Audit Request');
                } else {
                    $message =  Config::get('messages.ACCESS_DENIED');
                    return Redirect::to("/dashboard")->with('error', $message);
                }
            } else {
                return view('request.editRequest')->with('req_details', $req_details)->with('page_title', 'Audit Request');
            }
        } else {
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }

    }

    /**
     * Manage individual requests
     *
     * @return mixed
     */
    public function process()
    {
        //declare and initialize variables
        $req_id = $_GET['id'];
        $status = $_GET['manage'];
        $manage_req = $this->inspection->manageRequest($req_id, $status);

        if($manage_req)
        {
            $req_details = $this->inspection->getRequestDetails($req_id);
            $created_user_id = $req_details[0]->complianceCompany->id;
            $user_details = $this->user->getCompanyDetails($created_user_id);

            foreach ($user_details as $item) {
                $compliance_user_mail = $item->email;
                $subject = "Audit Request Received";
                $name = $item->company->name;
                $email_template = 'emails.inspection_request';
                $message = "Test Email";
                $dataArray = array(
                    'message'  => $message,
                    'name'     => $item->company->name,
                    'from'     => 'noreply@simplifya.com',
                    'system'   => 'Simplifya',
                );
                $this->sendEmail($compliance_user_mail, $name, $email_template, $subject, $dataArray);
            }
            return Redirect('request/manage');
        }
    }

    /**
     * Send email
     *
     * @param $email
     * @param $name
     * @param $email_template
     * @param $subject
     * @param $dataArray
     * @return mixed
     */
    public function sendEmail($email, $name, $email_template, $subject, $dataArray)
    {
        $send_mail = new sendMail();

        $data =  $dataArray;
        $layout = $email_template;
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }

}
