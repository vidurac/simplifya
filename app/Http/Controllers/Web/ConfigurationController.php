<?php

namespace App\Http\Controllers\Web;

use App\Events\SendReferralToken;
use App\Http\Requests\CouponCreateRequest;
use App\Lib\sendMail;
use App\Models\AppointmentClassification;
use App\Models\CompanyLocation;
use App\Models\MasterCity;
use App\Models\MasterClassification;
use App\Models\MasterCountry;
use App\Models\QuestionClassification;
use App\Repositories\CompanyRepository;
use App\Repositories\CouponsRepository;
use App\Repositories\MasterClasificationOptionRepository;
use App\Repositories\MasterClasificationRepository;
use App\Repositories\MasterClassificationEntityAllocationRepository;
use App\Repositories\MasterCountryRepository;
use App\Repositories\MasterStateRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\MasterReferralsRepository;
use App\Repositories\MasterReferralPaymentsRepository;
use App\Repositories\MasterApplicabilitiesRepository;
use App\Repositories\MasterLicenseApplicabilitiesRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\MasterCityRepository;
use App\Repositories\UserGroupesRepository;
use App\Repositories\CouponDetailsRepository;
use App\Repositories\MasterKeywordRepository;
use App\Repositories\QuestionKeywordRepository;
use App\Models\MasterEntityType;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\CityListUpdateRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\SubscriptionRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuestionCategoryRequest;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\StateRequest;
use App\Http\Requests\EditStateRequest;
use App\Http\Requests\EditStateStoreRequest;
use App\Http\Requests\CityRequest;
use App\Http\Requests\EditQuestionCategoryRequest;
use App\Http\Requests\MasterDataRequest;
use App\Http\Requests\UserGroupRequest;
use App\Http\Requests\SubscriptionEditRequest;
use App\Models\MasterData;

class ConfigurationController extends Controller
{
    private $country;
    private $company;
    private $city;
    private $state;
    private $subscription;
    private $question;
    private $classification;
    private $master_country;
    private $master_city;
    private $master_data;
    private $master_referral;
    private $master_referral_payment;
    private $master_applicability;
    private $master_license_applicability;
    private $user_group;
    private $classification_option;
    private $classification_option_allocation;
    private $entity_type;
    private $question_classification;
    private $coupon;
    private $coupon_detail;
    private $master_keyword;
    private $question_keyword;


    /**
     * ConfigurationController constructor.
     * @param MasterCountry $country
     * @param MasterCity $city
     * @param MasterStateRepository $state
     * @param SubscriptionRepository $subscription
     * @param QuestionRepository $question
     * @param MasterClasificationRepository $classification
     * @param MasterClasificationOptionRepository $classification_option
     * @param MasterCountryRepository $master_country
     * @param MasterCityRepository $master_city
     * @param MasterUserRepository $master_data
     * @param CompanyRepository $company
     * @param UserGroupesRepository $user_group
     * @param MasterClassificationEntityAllocationRepository $classification_option_allocation
     * @param MasterEntityType $entity_type
     * @param QuestionClassificationRepository $question_classification
     * @param CouponsRepository $couponsRepository
     * @param MasterReferralsRepository $master_referral
     * @param MasterReferralPaymentsRepository $master_referral_payment
     * @param MasterKeywordRepository $master_keyword
     * @param MasterApplicabilitiesRepository $master_applicability
     * @param CouponDetailsRepository $coupon_detail
     * @param MasterKeywordRepository $master_keyword
     * @param QuestionKeywordRepository $question_keyword
     */
    public function __construct(MasterCountry $country,
                                MasterCity $city,
                                MasterStateRepository $state,
                                SubscriptionRepository $subscription,
                                QuestionRepository $question,
                                MasterClasificationRepository $classification,
                                MasterClasificationOptionRepository $classification_option,
                                MasterCountryRepository $master_country,
                                MasterCityRepository $master_city,
                                MasterUserRepository $master_data,
                                CompanyRepository $company,
                                UserGroupesRepository $user_group,
                                MasterClassificationEntityAllocationRepository $classification_option_allocation,
                                MasterEntityType $entity_type,
                                QuestionClassificationRepository $question_classification,
                                CouponsRepository $couponsRepository,
                                MasterReferralsRepository $master_referral,
                                MasterReferralPaymentsRepository $master_referral_payment,
                                MasterKeywordRepository $master_keyword,
                                MasterApplicabilitiesRepository $master_applicability,
                                CouponDetailsRepository $coupon_detail,
                                QuestionKeywordRepository $question_keyword,
                                MasterLicenseApplicabilitiesRepository $master_license_applicability)
    {
        $this->country          = $country;
        $this->master_country   = $master_country;
        $this->master_city      = $master_city;
        $this->city             = $city;
        $this->state            = $state;
        $this->subscription     = $subscription;
        $this->question         = $question;
        $this->classification   = $classification;
        $this->classification_option = $classification_option;
        $this->master_data      = $master_data;
        $this->company          = $company;
        $this->user_group       = $user_group;
        $this->classification_option_allocation       = $classification_option_allocation;
        $this->entity_type      = $entity_type;
        $this->question_classification      = $question_classification;
        $this->coupon = $couponsRepository;
        $this->master_referral = $master_referral;
        $this->master_referral_payment = $master_referral_payment;
        $this->coupon_detail = $coupon_detail;
        $this->master_applicability = $master_applicability;
        $this->master_license_applicability = $master_license_applicability;
        $this->master_keyword = $master_keyword;
        $this->question_keyword = $question_keyword;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('configuration.configuration')->with('page_title', 'Configuration');
    }

    /**
     * Master data view
     *
     * @return view
     */
    public function masterData()
    {
        //declare and initialize variables
        $dataset = array();
        $cities_update = array();
        $countries = $this->country->all(array('*'));   //get all countries
        $cities    = $this->city->all(array('*'));      //get all cities
        $state     = $this->state->all(array('*'));     //get all states

        $check = $this->master_data->findWhere(array('name' => 'COMPANY NAME'));

        if(isset($check[0])){
            $get_all = MasterData::all();

            foreach ($get_all as $item){
                switch ($item->name){
                    case "COMPANY NAME":
                        $dataset['company_name'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "EMAIL":
                        $dataset['email'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "PHONE":
                        $dataset['phone'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "ADDRESS1":
                        $dataset['address1'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "ADDRESS2":
                        $dataset['address2'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "COUNTRY":
                        $dataset['country'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        $state_update = $this->state->findWhere(array('country_id'=> $item->value));     //get all states
                        break;
                    case "STATE":
                        $dataset['state'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        $cities_update    = $this->master_city->findWhere(array('status_id'=> $item->value));      //get all cities
                        break;
                    case "CITY":
                        $dataset['city'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "HEADER":
                        $dataset['header'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "SUBSFEE":
                        $dataset['subs_fee'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "FOOTER":
                        $dataset['footer'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "SUB QUESTION":
                        $dataset['sub_question'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "PAGINATION":
                        $dataset['pagination'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "SUBSCRIPTION":
                        $dataset['subscription'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "INSPECTION_REPORT_TREE_VIEW":
                        $dataset['insp_rpt_tree_view'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "MJB_FREE_SIGN_UP":
                        $dataset['mjb_free_sign_up'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "MJB_FREE_LICENSE":
                        $dataset['mjb_free_license'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "CC_GE_FREE_CHECKLIST":
                        $dataset['cc_ge_free_checklist'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "IOS_VERSION":
                        $dataset['ios_version'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "ANDROID_VERSION":
                        $dataset['android_version'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "ACTION_ITEMS_ON_OFF":
                        $dataset['action_items_on_off'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                    case "STATUS_INDICATOR_ON_OFF":
                        $dataset['status_indicator_on_off'] = [
                            'name'  => $item->name,
                            'value' => $item->value
                        ];
                        break;
                }

            }

            return view('configuration.masterDataEdit')->with('page_title', 'Master Data')->with('countries', $countries)->with('cities', $cities_update)->with('states', $state_update)->with('data', $dataset);
        }else{
            return view('configuration.masterData')->with('page_title', 'Master Data')->with('countries', $countries)->with('cities', $cities)->with('states', $state);
        }
    }

    /**
     * Subscription view
     * @param $title
     * @return view
     */
    public function subscription($title)
    {
        if($title=="mjb"){
            $title = "Subscription for MJB";
            $subscription_type = "mjb";
        }
        elseif($title="cc_ge"){
            $title = "Subscription for CC & GE";
            $subscription_type = "cc_ge";
        }
        else{
            $title ="";
            $subscription_type = "";
        }

        return view('configuration.subscription')->with('page_title', $title)->with('subscription_type', $subscription_type);
    }

    /**
     * Subscription - add new subscription
     *
     * @param $title
     * @return $this
     */
    public function addNewSubscription($title)
    {
        if($title=="mjb"){
            $title = "Subscription for MJB";
            $subscription_type = "mjb";
        }
        elseif($title="cc_ge"){
            $title = "Subscription for CC & GE";
            $subscription_type = "cc_ge";
        }
        else{
            $title ="";
            $subscription_type = "";
        }

        return view('configuration.add_new_subscription')->with('page_title', 'Add New Subscription - '.$title)->with('subscription_type', $subscription_type);
    }

    /**
     * Subscription - new subscription store
     * @param $title
     * @param SubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newSubscriptionStore($title, SubscriptionRequest $request)
    {
        //declare and initialize variables
        $subscription_name = $request->subscription_name;
        $validity_period   = $request->validity_period;
        $company_type      = $request->company_type;
        $price             = $request->price;
        $description             = $request->description;


        //$check = $this->subscription->findWhere(array('entity_type_id' => $company_type, 'status' => 1 ));
        $subscription_plans = $this->subscription->getSubscriptionPlans($company_type,$validity_period);
        //,'validity_period_id' => $validity_period

        if(count($subscription_plans) == 0) {
            $dataset = array(
                'name' => $subscription_name,
                'amount' => $price,
                'status' => 1,
                'description' => $description,
                'validity_period_id' => $validity_period,
                'entity_type_id' => $company_type,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id
            );

            //save subscription details
            $save = $this->subscription->insert($dataset); //save subscription

            if ($save) {
                $message = Config::get('messages.SUBSCRIPTION_INSERT_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => $message));
            } else {
                $message = Config::get('messages.SUBSCRIPTION_INSERT_FAILED');
                return Response()->json(array('success' => 'false', 'message' => $message));
            }
        }else{
            $message = Config::get('messages.SUBSCRIPTION_PERIOD_EXIST');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Main Question categories view
     * @return view
     */
    public function MainQuestionCategories()
    {
        return view('configuration.main_question_categories')->with('page_title', 'Question Categories');
    }

    /**
     * Question categories view
     * @return view
     */
    public function QuestionCategories()
    {
        return view('configuration.question_categories')->with('page_title', 'Question Categories');
    }
    /**
     * Question categories Options view
     * @return view
     */
    public function QuestionCategoriesOptions($question_category_id=1,$parent_id=0)
    {

        //get all question category information
        $get_question_category_info = $this->classification->getClassificationById($question_category_id,$parent_id);

        $arr = array();
        foreach($get_question_category_info[0]->masterClassificationOptions as $question_category_info) {

            $arr[] = array(
                "id"=>$question_category_info->id,
                "name" => $question_category_info->name,
                "parent_id" => $question_category_info->parent_id,
                "children" => $this->QuestionCategoriesOptions($question_category_id,$question_category_info->id),

            );
        }
        return view('configuration.question_categories_options')->with('page_title', 'Edit Question Category')->with('options',$get_question_category_info)->with('options2',$arr);
    }

    /**
     * new question categories view
     * @return $this
     */
    public function NewQuestionCategories()
    {
        return view('configuration.new_question_category')->with('page_title', 'Add New Question Category');
    }

    /**
     * Question Categories - Store question categories
     *
     * @param QuestionCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function StoreQuestionCategories(QuestionCategoryRequest $request)
    {
        //declare and initialize variables
        $option_set = array();
        $visible_on = array();

        $option_name = $request->data['option'];

        if(is_array($option_name)) {
            foreach ($option_name as $item) {
                $option_set[] = array(
                    'name' => $item
                );
            }
        }else{
            $option_set = [
                'name' => $option_name
            ];
        }

        if(isset($request->data['visible_to'])) {
            $visible_on = array(
                'visible_to' => $request->data['visible_to'],
            );
        }

        //dataset array
        $dataset = array(
            'is_required'    => $request->data['is_required'],
            'is_multiselect' => $request->data['is_multiselect'],
            'is_main'        => 0,
            'name'           => $request->data['category_name'],
            'status'         => 1,
        );

        //save dataset to category
        $save_to_category = $this->question->questionCategoryInsert($dataset);

        if($save_to_category)
        {
            $save_to_option = $this->question->categoryOptionInsert($option_set, $save_to_category);

            if($save_to_option)
            {
                if(count($visible_on)>0) {
                    foreach ($visible_on as $item)
                    {
                        $save_to_visible = $this->question->visibilityInsert($item, $save_to_category);

                        if($save_to_visible)
                        {
                            $message = Config::get('messages.QUESTION_CAT_INSERT_SUCCESS');
                            return Response()->json(array('success' => 'true', 'message' => $message), 200);
                        } else {
                            $message = Config::get('messages.QUESTION_CAT_INSERT_FAILED');
                            return Response()->json(array('success' => 'false', 'message' => $message));
                        }
                    }
                }
            }else{
                $message = Config::get('messages.QUESTION_CAT_INSERT_FAILED');
                return Response()->json(array('success' => 'false', 'message' => $message));
            }
        }else{
            $message = Config::get('messages.QUESTION_CAT_INSERT_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }

    }

    /**
     * subscription type datatable filtration
     *
     * @param $subscription_type
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter($subscription_type)
    {
        //declare and initialize variable
        $table = 'master_subscriptions';
        $columns = array('subscription_plan', 'price', 'status', 'manage');

        $response = $this->getFilteredSubscriptionList($table, $columns, $subscription_type);

        return response()->json($response);
    }

    /**
     * Question categories datatable filtration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterQuestionCategories($is_only_main='')
    {
        //declare and initialize variable
        $table = 'master_classifications';
        $columns = array('category_name', 'is_required', 'status', 'manage');

        $response = $this->getFilteredQuestionCategoryList($table, $columns,$is_only_main);

        return response()->json($response);
    }

    /**
     * User group datatable filtration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userGroupFilter() {
        //declare and initialize valiable
        $table = 'master_user_groups';
        $columns = array('group_name', 'company_name', 'manage');

        $response = $this->getFilteredUserGroupList($table, $columns);

        return response()->json($response);
    }

    // get states based on country
    public function getStates(LocationRequest $request){
        //declare and initialize variables
        $country_id = $request->id;
        $states = $this->country->with(array('masterStates'))->find($country_id, array('*'));

        return Response()->json(array('success' => 'true', 'data' => $states), 200);
    }

    /**
     * get states based on state
     * @param LocationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(LocationRequest $request){
        //declare and initialize variable
        $state_id = $request->id;
        $cities = $this->state->with(array('masterCity'))->find($state_id, array('*'));

        return Response()->json(array('success' => 'true', 'data' => $cities), 200);
    }

    /**
     * get subscription list datatable result
     *
     * @param $table
     * @param $columns
     * @param $subscription_type
     * @return array
     */
    public function getFilteredSubscriptionList($table, $columns, $subscription_type)
    {
        //declare and initialize variable
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
                    if($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`distance` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                    }
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
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

                $date = explode( "," , $_GET['sSearch_' . $i] );

                if($columns[$i]=="name"){
                    $sWhere .= "master_subscription.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }
            }

            $sWhere = substr_replace($sWhere, "", -3);
        }

        if($subscription_type=="mjb"){
            $sWhere .= "WHERE master_subscriptions.`entity_type_id` = 2 AND `status` = 1";
        }elseif ($subscription_type=="cc_ge"){
            $sWhere .= "WHERE (master_subscriptions.`entity_type_id` = 3 OR master_subscriptions.`entity_type_id` = 4) AND `status` = 1";
        }

        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `id`,`name`,`amount`,`status` FROM `".$table."` ".$sWhere." ".$sOrder." ".$sLimit;
        $rResult =  $this->subscription->getSubscriptionRequests($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            $status_txt = "";

            switch ($item->status){
                case 1:
                    $status_txt = "<span class=\"badge badge-success\">Active</span>";
                    break;
                case 2:
                    $status_txt =  "<span class=\"badge badge-item\">Inactive</span>";
                    break;
            }

            $editSubscription = "<a class=\"btn btn-info btn-circle\" onclick='editSubscription($item->id)'><i class=\"fa fa-paste\"></i></a> <a href=\"/configuration/subscription/remove/". $item->id ."\" id=\"change-subscription-state\" class=\"btn btn-danger btn-circle\"><i class=\"fa fa-trash-o\"></i></a>";

            $dataset[] = array(
                'subscription_plan' => $item->name,
                'price'             => $item->amount,
                'status'            => $status_txt,
                'manage'            => $editSubscription
            );

        }
        $FilteredTotal = $this->subscription->currentRow();
        $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        // Get total number of rows in table
        $total = $this->subscription->getTotaleNumber($subscription_type);
        $iTotal = $total[0]->count;

        // Output
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        // Return array of values
        foreach($dataset as $aRow) {
            $row = array();
            for ( $i = 0; $i < count($columns); $i++ ) {
                if ( $columns[$i] == 'subscription_plan' ) {
                    $row[] = $aRow['subscription_plan'];
                }
                if ( $columns[$i] == 'price' ) {
                    $row[] = $aRow['price'];
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
     * get question category list datatable result set
     *
     * @param $table
     * @param $columns
     * @return array
     */
    public function getFilteredQuestionCategoryList($table, $columns,$is_only_main)
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
                    if($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`distance` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                    }
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
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

                $date = explode( "," , $_GET['sSearch_' . $i] );

                if($columns[$i]=="name"){
                    $sWhere .= "master_subscription.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }
            }

            $sWhere = substr_replace($sWhere, "", -3);
        }
        if($is_only_main == 1)
        {
            $sWhere .= "WHERE (master_classifications.`status` = 1 OR master_classifications.`status` = 0) AND master_classifications.is_main = 1 ";
        }
        if($is_only_main == 0)
        {
            $sWhere .= "WHERE (master_classifications.`status` = 1 OR master_classifications.`status` = 0) AND master_classifications.is_main != 1";
        }

        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `id`,`name`,`status`, `is_required`, `is_main` FROM `".$table."` ".$sWhere." ".$sOrder." ".$sLimit;
        $rResult =  $this->subscription->getSubscriptionRequests($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            if($item->status==1){
                $status = "<i class=\"fa fa-thumbs-o-up\"></i>";
                $mark = "btn-success";
            }
            else if($item->status==0)
            {
                $status = "<i class=\"fa fa-thumbs-o-down\"></i>";
                $mark = "btn-warning";
            }

            if($item->is_main==1) {
                $editQuestionCategory = "<a class=\"btn btn-info btn-circle\" title='edit' href=\"/configuration/qcategory/options/$item->id\"><i class=\"fa fa-paste\"></i></a>";
            }else {
                //manage buttons
                $editQuestionCategory = "<a class=\"btn btn-info btn-circle\" title='edit' href=\"/configuration/qcategory/options/$item->id\"><i class=\"fa fa-paste\"></i></a> <a class=\"btn " . $mark . " btn-circle active-" . $item->id . "  \"  title='active' onclick=\"activeQuestionCategory(" . $item->status . "," . $item->id . ")\">" . $status . "</a> <a class=\"btn btn-danger btn-circle\" onclick=\"removeQuestionCategory(" . $item->id . ")\"><i class=\"fa fa-trash-o\"></i></a>";
            }

            $dataset[] = array(
                'category_name'     => $item->name,
                'is_required'       => $item->is_required==1?'<span class="badge badge-success">Yes</span>':'<span class="badge badge-warning">No</span>',
                'manage'            => $editQuestionCategory
            );
        }
        $FilteredTotal = $this->classification->currentRow();
        $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        // Get total number of rows in table
        $total = $this->classification->getTotaleNumber();
        $iTotal = $total[0]->count;
        // Output
        $output = array(

            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        // Return array of values
        foreach($dataset as $aRow) {
            $row = array();
            for ( $i = 0; $i < count($columns); $i++ ) {
                if ( $columns[$i] == 'category_name' ) {
                    $row[] = $aRow['category_name'];
                }
                if ( $columns[$i] == 'is_required' ) {
                    $row[] = $aRow['is_required'];
                }
                if( $columns[$i] == 'manage') {
                    $row[] = $aRow['manage'];
                }
            }
            $output['aaData'][] = $row;
        }

        return $output ;
    }

    /*
     * bootstrap table query to collect country list
     *
     * @param $table
     * @param $columns
     * @return $output
     */
    public function getFilteredCountryList($table, $columns)
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
                    if($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`distance` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                    }
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
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

                $date = explode( "," , $_GET['sSearch_' . $i] );

                if($columns[$i]=="name"){
                    $sWhere .= "master_country.`". $columns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }
            }

            $sWhere = substr_replace($sWhere, "", -3);
        }

        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `id`,`name`,`status` FROM `".$table."` ".$sWhere." ".$sOrder." ".$sLimit;
        $rResult =  $this->master_country->getAllCountries($sQuery);

        $dataset = array();
        $tip = "";
        $mark = "";
        foreach($rResult as $item)
        {
            if($item->status==1){
                $status = "<i class=\"fa fa-thumbs-o-up\"></i>";
                $mark = "btn-success";
                $tip = "title=\"active\"";
            }
            else if($item->status==2)
            {
                $status = "<i class=\"fa fa-thumbs-o-down\"></i>";
                $mark = "btn-warning";
                $tip = "title=\"inactive\"";
            }

            $action_btn = '<a class="btn '. $mark .' btn-circle active-'. $item->id .'  " '. $tip .' onclick="activeCountry('.$item->status.','.$item->id.')">'. $status .'</a>';


            $dataset[] = array(
                'country_name'  => $item->name,
                'manage'        => $action_btn
            );
        }
        $FilteredTotal = $this->master_country->currentRow();
        $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        // Get total number of rows in table
        $total = $this->master_country->getTotaleNumber();
        $iTotal = $total[0]->count;

        // Output
        $output = array(

            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        // Return array of values
        foreach($dataset as $aRow) {
            $row = array();
            for ( $i = 0; $i < count($columns); $i++ ) {
                if ( $columns[$i] == 'country_name' ) {
                    $row[] = $aRow['country_name'];
                }
                if ( $columns[$i] == 'manage' ) {
                    $row[] = $aRow['manage'];
                }
            }
            $output['aaData'][] = $row;
        }

        return $output ;
    }

    /*
     * bootstrap table query to collect country list
     *
     * @param $table
     * @param $columns
     * @return $output
     */
    public function getFilteredStateList($table, $columns)
    {
        //declare and initialize variables
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
                    if($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`distance` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                    }
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
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
                if($columns[$i]=="state_name"){
                    $sWhere .= "`master_states`.`id` LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }else if($columns[$i]=="country_name"){
                    $sWhere .= "`master_states`.`country_id` LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }else if($columns[$i]=="state_status"){
                    $sWhere .= "`master_states`.`status` LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }else if($columns[$i]=="manage"){
                    $sWhere .= "";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        // Individual column filtering
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {

                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }

                if($columns[$i]=="country_name"){
                    $sWhere .= "master_states.`country_id` = " . $_GET['sSearch_' . $i] . " ";
                }else if($columns[$i]=="state_name"){
                    $sWhere .= "master_states.`id` = " . $_GET['sSearch_' . $i] . " ";
                }else if($columns[$i]=="status"){
                    $sWhere .= "master_states.`". $columns[$i] . "` = " . $_GET['sSearch_' . $i] . " ";
                }else{
                    $sWhere .= "(master_states.`status` = 1) ";
                }
            }
        }

        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `master_states`.`id` as `state_id`, `master_states`.`name` as `state_name`, `master_countries`.`name` as `country_name`, `master_states`.`status` as `state_status` FROM `".$table."` JOIN `master_countries` ON `master_states`.`country_id`=`master_countries`.`id` ".$sWhere." ".$sOrder." ".$sLimit;
        $rResult =  $this->state->getAllStates($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            if($item->state_status==1){
                $status = "<i class=\"fa fa-thumbs-o-up\"></i>";
                $mark = "btn-success";
            }
            else if($item->state_status==2)
            {
                $status = "<i class=\"fa fa-thumbs-o-down\"></i>";
                $mark = "btn-warning";
            }

            $state_id = $item->state_id;
            $country = $this->state->getCountryByState($state_id);

            $manageBtn = "<a class=\"btn btn-info btn-circle\" onclick=\"editState(".$item->state_id.");\" title='edit'><i class=\"fa fa-paste\"></i></a> ";

            $dataset[] = array(
                'state_name'    => $item->state_name,
                'country_name'  => $country[0]->country_name,
                'state_status'  => $item->state_status==1?'<span class="badge badge-success">Active</span>':'<span class="badge badge-warning">Inactive</span>',
                'manage'        => $manageBtn
            );
        }
        $FilteredTotal = $this->state->currentRow();
        $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        // Get total number of rows in table
        $total = $this->state->getTotaleNumber();
        $iTotal = $total[0]->count;

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
                if ( $columns[$i] == 'state_name' ) {
                    $row[] = $aRow['state_name'];
                }
                if ( $columns[$i] == 'country_name' ) {
                    $row[] = $aRow['country_name'];
                }
                if ( $columns[$i] == 'state_status' ) {
                    $row[] = $aRow['state_status'];
                }
                if ( $columns[$i] == 'manage' ) {
                    $row[] = $aRow['manage'];
                }
            }
            $output['aaData'][] = $row;
        }

        return $output ;
    }

    /*
     * bootstrap table query to collect country list
     *
     * @param $table
     * @param $columns
     * @return $output
     */
    public function getFilteredCityList($table, $columns)
    {
        $index_column = "id";
        $countyId = '';
        $stateId = '';
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
                    if($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`distance` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                    }
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" ) {
                $sOrder = "";
            }
        } else {
            $sOrder = ' ORDER BY `master_states`.`name` ASC';
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
                if($columns[$i]=="city_name"){
                    $sWhere .= "`master_cities`.`name` LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }else if($columns[$i]=="state_name"){
                    $sWhere .= "";
                }else if($columns[$i]=="manage"){
                    $sWhere .= "";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        // Individual column filtering
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {

                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }

                if($columns[$i]=="city_name"){
                    $sWhere .= "master_cities.`status_id` = " . $_GET['sSearch_' . $i] . " ";
                } else if ($columns[$i]=="country_name"){
                    $countyId = $_GET['sSearch_' . $i];
                    $sWhere .= "master_countries.`id` = " . $_GET['sSearch_' . $i] . " ";
                } else if ($columns[$i]=="state_name"){
                    $stateId = $_GET['sSearch_' . $i];
                    $sWhere .= "master_states.`id` = " . $_GET['sSearch_' . $i] . " ";
                } else{
                    $sWhere .= "(master_cities.`status` = 1) ";
                }
            }
        }
        $sWhere .= ' AND master_cities.`status` = 1 ';
        $sGroupBy = ' GROUP BY `master_states`.`id`';

        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `master_countries`.`name` as `country_name`, `master_states`.`id` as `state_id`, `master_states`.`name` as `state_name`, `master_states`.`country_id`, `master_cities`.`id` as `city_id`, `master_cities`.`name` as `city_name`, `master_cities`.`status` as `city_status` FROM `".$table."` JOIN `master_states` ON `master_states`.`id`=`master_cities`.`status_id` JOIN `master_countries` ON `master_countries`.`id`=`master_states`.`country_id` ".$sWhere." ".$sGroupBy. " " .$sOrder." ".$sLimit;
        $rResult =  $this->state->getAllStates($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            $state_id = $item->state_id;
            $state = $this->state->getStateByStateId($state_id);

            $manage_btn = "<a class='btn btn-circle btn-info' onclick='editCity(". $item->city_id .",". $item->state_id .",". $item->country_id .")'><i class='fa fa-paste'></i></a>";

            $dataset[] = array(
                'country_name'  => $item->country_name,
                'state_name'    => $state[0]->state_name,
                'manage'        => $manage_btn
            );
        }

        $FilteredTotal = $this->master_city->currentRow();

        $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        // Get total number of rows in table
        //$total = $this->master_city->getFilteredTotaleNumber();
        //$total = $this->state->getFilteredTotaleNumber($countyId, $stateId);
        $total = count($dataset);
        //$iTotal = $total[0]->count;
        $iTotal = $total;

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
                if ( $columns[$i] == 'country_name' ) {
                    $row[] = $aRow['country_name'];
                }
                if ( $columns[$i] == 'state_name' ) {
                    $row[] = $aRow['state_name'];
                }
                if ( $columns[$i] == 'manage' ) {
                    $row[] = $aRow['manage'];
                }
            }
            $output['aaData'][] = $row;
        }

        return $output ;
    }

    /*
     * bootstrap table query to collect user group list
     *
     * @param $table
     * @param @columns
     *
     * @return $output
     */
    public function getFilteredUserGroupList($table, $columns)
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
                    if($_GET['iSortCol_0']==3){
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`distance` ". $sortDir .", ";
                    }else{
                        $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                        $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                    }
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
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
                if($columns[$i]=="city_name"){
                    $sWhere .= "`companies`.`name` LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }else if($columns[$i]=="group_name"){
                    $sWhere .= "";
                }else if($columns[$i]=="associated_users"){
                    $sWhere .= "";
                }else if($columns[$i]=="no_of_users"){
                    $sWhere .= "";
                }else if($columns[$i]=="manage"){
                    $sWhere .= "";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `master_entity_types`.`id` as `entity_id`, `master_entity_types`.`name` as `entity_name`, `master_user_groups`.`name` as `user_group_name`, `master_user_groups`.`id` as `user_group_id`, `master_user_groups`.`status` as `user_group_status` FROM `".$table."` JOIN `master_entity_types` ON `master_entity_types`.`id` = `master_user_groups`.`entity_type_id` ".$sWhere." ".$sOrder." ".$sLimit;
        $rResult =  $this->user_group->getAllUserGroups($sQuery);

        $dataset = array();
        foreach($rResult as $item)
        {
            if($item->user_group_status==1){
                $status = "<i class=\"fa fa-thumbs-o-up\"></i>";
                $mark = "btn-success";
            }
            else if($item->user_group_status==2)
            {
                $status = "<i class=\"fa fa-thumbs-o-down\"></i>";
                $mark = "btn-warning";
            }

            if($item->user_group_id!=1) {
                $action_btn = "<a class=\"btn btn-info btn-circle\" title='edit' onclick='editUserGroup($item->user_group_id);'><i class=\"fa fa-paste\"></i></a>";
            }else{
                $action_btn = "<a class=\"btn btn-info btn-circle\" title='edit' onclick='editUserGroup($item->user_group_id);'><i class=\"fa fa-paste\"></i></a>";
            }
            $dataset[] = array(
                'group_name'     => $item->user_group_name,
                'company_name'   => $item->entity_name,
                'user_group_id'  => $action_btn
            );
        }

        $FilteredTotal = $this->user_group->currentRow();
        $iFilteredTotal = $FilteredTotal[0]->FilteredTotal;

        // Get total number of rows in table
        $total = $this->user_group->getTotaleNumber();
        $iTotal = $total[0]->count;

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
                if ( $columns[$i] == 'group_name' ) {
                    $row[] = $aRow['group_name'];
                }
                if ( $columns[$i] == 'company_name' ) {
                    $row[] = $aRow['company_name'];
                }
                if ( $columns[$i] == 'no_of_users' ) {
                    $row[] = $aRow['no_of_users'];
                }
                if ( $columns[$i] == 'manage' ) {
                    $row[] = $aRow['user_group_id'];
                }
            }
            $output['aaData'][] = $row;
        }

        return $output ;
    }

    /**
     * Manage countries
     * manage countries list
     *
     * @return view
     */
    public function countryManager()
    {
        return view('configuration.countryManager')->with('page_title', 'Country Manager');
    }

    /**
     * Manage Coupons
     * manage coupons list
     *
     * @return view
     */
    public function couponManager()
    {
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.couponManager')->with('page_title', 'Discount Manager')->with('MJB_entity_type',$MJB_entity_type);
    }

    /**
     * Manage Referrals Manager
     * manage Referrals List
     *
     * @return view
     */
    public function referralManager()
    {
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        $content = view('configuration.referralManager', array('page_title' => 'Referral Manager', 'MJB_entity_type' => $MJB_entity_type));
//        return view('configuration.referralManager')->with('page_title', 'Referral Manager')->with('MJB_entity_type',$MJB_entity_type);
        return response($content)->header('Cache-Control','no-store, no-cache, must-revalidate');;
    }

    /**
     * list down all country list in the system
     *
     * @return json
     */
    public function countryFilter()
    {
        //declare and initialize variable
        $table = 'master_countries';
        $columns = array('country_name', 'status', 'manage');

        $response = $this->getFilteredCountryList($table, $columns);

        return response()->json($response);
    }

    /**
     * Add, active and inactive country list
     *
     * @return $this
     */
    public function addNewCountry()
    {
        return view('configuration.add_new_country')->with('page_title', 'Add New Country');
    }

    /**
     * Add, new coupon
     *
     * @return $this
     */
    public function addNewCoupon($coupon_id=null)
    {
        if(isset($coupon_id)){
            $page_title='Edit Discount Code';
            $couponId=$coupon_id;
        }else{
            $page_title='Add New Discount Code';
            $couponId=0;
        }
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.add_new_coupon')->with('page_title', $page_title)->with('MJB_entity_type',$MJB_entity_type)->with('coupon_id',$couponId);
    }
    /**
     * Add, new referrer
     *
     * @return $this
     */
    public function addNewReferrer($referrer_id=null)
    {
        if(isset($referrer_id)){
            $page_title='Edit Referrer';
            $referrerId=$referrer_id;
        }else{
            $page_title='Add New Referrer';
            $referrerId=0;
        }
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.add_new_referrer')->with('page_title', $page_title)->with('MJB_entity_type',$MJB_entity_type)->with('referrer_id',$referrerId)->with('read_only', 'false');
    }

    /**
     * View, new coupon
     *
     * @return $this
     */
    public function viewNewReferrer($referrer_id=null)
    {
        if(isset($referrer_id)){
            $page_title='View Referrer';
            $referrerId=$referrer_id;
        }else{
            $page_title='Add New Referrer';
            $referrerId=0;
        }
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.add_new_referrer')->with('page_title', $page_title)->with('MJB_entity_type',$MJB_entity_type)->with('referrer_id',$referrerId)->with('read_only', 'true');
    }

    /**
     * Add new country
     *
     * @param CountryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertCountry(CountryRequest $request)
    {
        $check = $this->master_country->findWhere(array('name' => $request->country_name));

        if(isset($check[0])){
            $message = Config::get('messages.COUNTRY_ALREADY_ADDED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }else{
            $dataset = array(
                'name'          => $request->country_name,
                'status'        => $request->visibility,
                'created_by'    => Auth::user()->id,
                'updated_by'    => Auth::user()->id
            );

            //insert country to database table
            $save_country = $this->master_country->insertCountry($dataset);

            if($save_country)
            {
                $message = Config::get('messages.COUNTRY_ADD_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => $message));
            }
            else{
                $message = Config::get('messages.COUNTRY_ADD_FAILED');
                return Response()->json(array('success' => 'false', 'message' => $message));
            }
        }

    }

    /**
     * Add, active and inactive state list
     *
     * @return view
     */
    public function addNewState()
    {
        $country_list = $this->master_country->getAllCountryList();
        return view('configuration.add_new_state')->with('page_title', 'Add New State')->with('country_list', $country_list);
    }

    /**
     * Add new state
     *
     * @param StateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertState(StateRequest $request)
    {

        $check = $this->state->findWhere(array('name'=> $request->state_name, 'country_id' => $request->country_id));

        if(isset($check[0])){
            $message = Config::get('messages.STATE_ALREADY_ADDED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }else {
            $dataset = array(
                'name' => $request->state_name,
                'country_id' => $request->country_id,
                'status' => $request->visibility,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id
            );

            //insert country to database table
            $save_state = $this->state->insertState($dataset);

            if ($save_state) {
                $message = Config::get('messages.STATE_ADD_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => $message));
            } else {
                $message = Config::get('messages.STATE_ADD_FAILED');
                return Response()->json(array('success' => 'false', 'message' => $message));
            }
        }
    }

    /**
     * Update edit state details
     * @param StateRequest $request
     */
    public function storeEditState(EditStateStoreRequest $request)
    {
        //declare and initialize variables
        $dataset = $request->data;

        $data = array(
            'name'          => $dataset['state_name'],
            'country_id'    => $dataset['country_id'],
            'status'        => $dataset['visibility']
        );

        $update_state = $this->state->update($data, $request->state_id);

        if($update_state)
        {
            $message = Config::get('messages.STATE_UPDATE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message));
        }
        else{
            $message = Config::get('messages.STATE_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * add, active and inactive states and add states to country
     * Manage States
     * @return $this
     */
    public function stateManager()
    {
        //get all country list
        $country_list = $this->master_country->getAllCountryList();
        return view('configuration.stateManager')->with('page_title', 'States Manager')->with('country_list', $country_list);
    }

    /**
     * List down all state list in the system
     *
     * @return json
     */
    public function stateFilter()
    {
        //declare and initialize variable
        $table = 'master_states';
        $columns = array('state_name', 'country_name', 'state_status', 'manage');

        $response = $this->getFilteredStateList($table, $columns);

        return response()->json($response);
    }

    /**
     * Edit state details by passing State ID
     * @param EditStateRequest $request
     */
    public function editState(EditStateRequest $request)
    {
        //declare and initialize variable
        $state_id = $request->state_id;

        //Get state details by state id
        $state_info = $this->state->getStateByStateId($state_id);

        //if state details are not null
        if($state_info)
        {
            return Response()->json(array('success' => 'true', 'data' => $state_info), 200);
        }
        else{
            $message = Config::get('messages.EDIT_STATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * add, active and inactive cities and add cities to country and state
     * Manage Cities
     * @return $this
     */
    public function cityManager()
    {
        //get all country list
        $country_list = $this->master_country->getAllCountryList();
        return view('configuration.cityManager')->with('page_title', 'City Manager')->with('country_list', $country_list);
    }

    /**
     * list down all city list in the system
     *
     * @return json
     */
    public function cityFilter()
    {
        //declare and initialize variable
        $table = 'master_cities';
        $columns = array('country_name','state_name', 'status', 'manage');

        $response = $this->getFilteredCityList($table, $columns);

        return response()->json($response);
    }

    /**
     * add, active and inactive city list
     *
     * @return $this
     */
    public function addNewCity()
    {
        //get all country list
        $country_list = $this->master_country->getAllCountryList();
        return view('configuration.add_new_city')->with('page_title', 'Add New City')->with('country_list', $country_list);
    }

    /**
     * Add new city
     * @param CityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertCity(CityRequest $request)
    {
        //get serialized form details
        $dataset = $request->dataset;

        if(count($dataset['city_id'])>1) {
            foreach ($dataset['city_id'] as $item) {

                $check = MasterCity::where(array('name' => $item, 'status' => 1))->get();

                if(isset($check[0])){
                    $message = Config::get('messages.CITY_ALREADY_EXIT');
                    return Response()->json(array('success' => 'false', 'message' => $message));
                }else{
                    $data = array(
                        'name' => $item,
                        'status_id' => $dataset['state_id'],
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id
                    );

                    //insert city to database table
                    $insert_city = $this->master_city->create($data);
                }

            }
        }else{

            $check = MasterCity::where(array('name' => $dataset['city_id'], 'status' => 1))->get();

            if(isset($check[0])){
                $message = Config::get('messages.CITY_ALREADY_EXIT');
                return Response()->json(array('success' => 'false', 'message' => $message));
            }else{
                $data = array(
                    'name' => $dataset['city_id'],
                    'status_id' => $dataset['state_id'],
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                );

                //insert city to database table
                $insert_city = $this->master_city->create($data);
            }
        }

        if($insert_city)
        {
            $message = Config::get('messages.CITY_ADD_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message));
        }
        else{
            $message = Config::get('messages.CITY_ADD_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Response $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptionEdit(SubscriptionEditRequest $request)
    {
        //declare and initialize variable
        $subscription_id = $request->dataset;

        $get_subscription_details = $this->subscription->getSubscriptionRequestById($subscription_id);

        if($get_subscription_details)
        {
            $message = Config::get('messages.SUBSCRIPTION_EDIT_DETAIL_SUCCESS');
            return Response()->json(array('success' => 'true', 'data' => $get_subscription_details, 'message' => $message), 200);
        }else{
            $message = Config::get('messages.SUBSCRIPTION_EDIT_DETAIL_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Subscription edit view - update
     *
     * @param SubscriptionEditRequest $request
     * @return mixed
     */
    public function subscriptionEditStore(SubscriptionEditRequest $request)
    {
        //declare and initialize variables
        $subscription_name = $request->dataset['subscription_name'];
        $validity_period   = $request->dataset['validity_period'];
        $company_type      = $request->dataset['company_type'];
        $price             = $request->dataset['price'];
        $subscription_id   = $request->dataset['subscription_id'];
        $subscription_description   = $request->dataset['subscription_description'];

        $dataset = array(
            'name'               => $subscription_name,
            'amount'             => $price,
            'status'             => 1,
            'validity_period_id' => $validity_period,
            'entity_type_id'     => $company_type,
            'description'     => $subscription_description,
            'created_by'         => Auth::user()->id,
            'updated_by'         => Auth::user()->id
        );

        $update = $this->subscription->update($dataset, $subscription_id);

        if($update)
        {
            $message = Config::get('messages.SUBSCRIPTION_UPDATE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message));
        }
        else{
            $message = Config::get('messages.SUBSCRIPTION_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Show the form for removing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove($id)
    {
        //remove subscription from database
        $remove_subscription = $this->subscription->remove_subscription($id);

        if($remove_subscription)
        {
            return redirect('configuration');
        }
        else{
            $message = Config::get('messages.SUBSCRIPTION_DELETE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function editQuestionCategory()
    {
        //get question category id
        $question_category_id = $_GET['categoryId'];


         $category_tree=$this->getCategoryTree($question_category_id,$parent_id=0);

        return Response()->json(array('success' => 'true','data'=>$category_tree[0],'category'=>$category_tree[1]));




    }

    private function getCategoryTree($question_category_id,$parent_id){
        //get all question category information
        $get_question_category_info = $this->classification->getClassificationById($question_category_id,$parent_id);


        $arr = array();
        foreach($get_question_category_info[0]->masterClassificationOptions as $question_category_info) {

            $arr[] = array(
                "id"=>$question_category_info->id,
                "name" => $question_category_info->name,
                "parent_id" => $question_category_info->parent_id,
                "children" => $this->getCategoryTree($question_category_id,$question_category_info->id),
            );
        }

        return array($get_question_category_info[0],$arr);
    }

    /**
     * Update edited question category information
     *
     * @param EditQuestionCategoryRequest $request
     */
    public function insertQuestionCategory(EditQuestionCategoryRequest $request)
    {
        //declare and initialize variable
        $visible_on = array();
        $option_name = array();
        $option_set = array();
        $visible_set = array();
        $option_withid = array();
        $option_whithoutid = array();
        $save_to_visible = array();

        if(isset($request->options)) {
            $option_name = $request->options;
        }

        if(is_array($option_name)) {
            foreach ($option_name as $item){
                if($item['id']==""){
                    $option_whithoutid[] = array(
                        'name' => $item['value'],
                        'parent_id' => $item['parent_id'],
                    );
                }else{
                    $option_withid[] = array(
                        'id' => $item['id'],
                        'name' => $item['value'],
                        'parent_id' => $item['parent_id'],
                    );
                }
            }
        }

        $question_category_id = $request->classification_id;

        if(isset($request->visible_to)) {
            $visible_on = $request->visible_to;
        }else{
            $visible_on = array('');
        }

        if(is_array($visible_on[0])){
            foreach ($visible_on as $item){
                $visible_set[] = array(
                    'name' => $item['value'],
                );
            }
        }else{
            $visible_set = "";
        }

        //dataset array
        $dataset = array(
            'is_required'    => $request->is_required,
            'is_multiselect' => $request->is_multiselect,
            'is_main'        => $request->is_main_cat==1? 1 : 0,
            'name'           => $request->category_name,
            'status'         => 1,
        );
        \Log::info("=================START UPDATE====================");
        $update_to_category = $this->classification->update($dataset, $question_category_id);

        if($update_to_category)
        {
            $save_to_option = $this->question->categoryOptionUpdate($option_withid, $option_whithoutid, $question_category_id);

            if($save_to_option)
            {
                $allocations = $this->classification_option_allocation->findWhere(array('classification_id' => $question_category_id));

                if(isset($allocations[0])) {
                    foreach ($allocations as $item) {
                        $this->classification_option_allocation->delete($item->id);
                    }
                }

                if(is_array($visible_set)) {

                    foreach ($visible_set as $item)
                    {
                        $save_to_visible = $this->question->visibilityInsert($item, $question_category_id);
                    }
                    if($save_to_visible)
                    {
                        $message = Config::get('messages.QUESTION_CAT_UPDATE_SUCCESS');
                    \Log::info("END UPDATE");
                        return Response()->json(array('success' => 'true', 'message' => $message));
                    } else {
                        $message = Config::get('messages.QUESTION_CAT_UPDATE_FAILED');
                        return Response()->json(array('success' => 'false', 'message' => $message));
                    }
                }else{
                    $message = Config::get('messages.QUESTION_CAT_UPDATE_SUCCESS');
                    \Log::info("END UPDATE 2");
                    return Response()->json(array('success' => 'true', 'message' => $message));
                }
            }else{
                $message = Config::get('messages.QUESTION_CAT_UPDATE_FAILED');
                return Response()->json(array('success' => 'false', 'message' => $message));
            }
        }else{
            $message = Config::get('messages.QUESTION_CAT_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Show the form for removing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function removeQuestionCategory()
    {
        //declare and initialize variable
        $id = $_GET['id'];
        $state = $_GET['status'];

        $remove_question_category = $this->classification->remove_question_category($id, $state);

        if($remove_question_category)
        {
            $message = Config::get('messages.QUESTION_CAT_DELETE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message));
        }
        else{
            $message = Config::get('messages.QUESTION_CAT_DELETE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Check if remove category option list items previously use in question classifications.
     * @return bool
     */
    public function checkMainQuestionCategory(){
        //declare and initialize variable
        $id = $_GET['classification_id'];
        $option_id = array();

        $option_set = $this->classification_option->findWhere(array('classification_id' => $id, 'status' => 1));

        foreach ($option_set as $item) {
            $option_id[] = $item['id'];
        }

        $check = $this->question_classification->checkOptionsExist($option_id);

        if(isset($check[0])){
            $message = Config::get('messages.QUESTION_OPTIONS_USED');
            return Response()->json(array('success' => 'true', 'status'=> 'true', 'message' => $message));
        }else{
            $message = Config::get('messages.QUESTION_OPTIONS_NOT_USED');
            return Response()->json(array('success' => 'true', 'status'=> 'false', 'message' => $message));
        }
    }

    /**
     * Remove main question category
     *
     * @return bool
     */
    public function removeMainQuestionCategory(){
        //declare and initialize variable
        $id = $_GET['classification_id'];

        $check = $this->classification->update(array('status' => 3), $id);

        if($check){
            $message = Config::get('messages.QUESTION_CLASSIFICATION_REMOVED_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message));
        }else{
            $message = Config::get('messages.QUESTION_CLASSIFICATION_REMOVED_FAIL');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Insert master data to the database table
     *
     * @param MasterDataRequest $request
     * @return json
     */
    public function storeMasterData(MasterDataRequest $request){
        //declare and initialize variable
        $index = "";
        //get all submitted form data
        $form_fillData = $request->dataset;

        //iterate form values as $key => $value pairs
        foreach ($form_fillData as $key => $value){
            switch ($key){
                case "company_name":
                    $index = "COMPANY NAME";
                    break;
                case "email":
                    $index = "EMAIL";
                    break;
                case "phone_no":
                    $index = "PHONE";
                    break;
                case "address1":
                    $index = "ADDRESS1";
                    break;
                case "address2":
                    $index = "ADDRESS2";
                    break;
                case "country_id":
                    $index = "COUNTRY";
                    break;
                case "state_id":
                    $index = "STATE";
                    break;
                case "city":
                    $index = "CITY";
                    break;
                case "header":
                    $index = "HEADER";
                    break;
                case "footer":
                    $index = "FOOTER";
                    break;
                case "subs_fee":
                    $index = "SUBSFEE";
                    break;
                case "sub_question_lvls":
                    $index = "SUB QUESTION";
                    break;
                case "pagination_lvl":
                    $index = "PAGINATION";
                    break;
                case "mjb_sub":
                    $index = "SUBSCRIPTION";
                    break;
                case "insp_rpt_tree_view":
                    $index = "INSPECTION_REPORT_TREE_VIEW";
                    break;
                case "mjb_free_sign_up":
                    $index = "MJB_FREE_SIGN_UP";
                    break;
                case "mjb_free_license":
                    $index = "MJB_FREE_LICENSE";
                    break;
                case "cc_ge_free_checklist":
                    $index = "CC_GE_FREE_CHECKLIST";
                    break;
                case "action_items_on_off":
                    $index = "ACTION_ITEMS_ON_OFF";
                    break;
                case "status_indicator_on_off":
                    $index = "STATUS_INDICATOR_ON_OFF";
                    break;
                case "ios_version":
                    $index = "IOS_VERSION";
                    break;
                case "android_version":
                    $index = "ANDROID_VERSION";
                    break;

            }

            if($index=="COMPANY NAME"){
                $check = $this->master_data->findWhere(array('value' => $value));

                if(isset($check[0])){
                    $message = Config::get('messages.MASTER_DATA_COMPANY_EXIST');
                    return Response()->json(array('success' => 'false', 'message' => $message));
                }
            }

            $data_set = array(
                'name'          => $index,
                'value'         => $value,
                'created_by'    => Auth::user()->id,
                'updated_by'    => Auth::user()->id,
            );

            $save_master_user_data = $this->master_data->create($data_set);
        }

        if($save_master_user_data){
            $message = Config::get('messages.MASTER_DATA_INSERT_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }else{
            $message = Config::get('messages.MASTER_DATA_INSERT_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }


    /**
     * Insert master data to the database table
     *
     * @param MasterDataRequest $request
     *
     * @return json
     */
    public function updateMasterData(MasterDataRequest $request){

        $index = "";
        //get all submitted form data
        $form_fillData = $request->dataset;
        $remove_all = MasterData::truncate();
        //iterate form values as $key => $value pairs
        foreach ($form_fillData as $key => $value){
            switch ($key){
                case "company_name":
                    $index = "COMPANY NAME";
                    break;
                case "email":
                    $index = "EMAIL";
                    break;
                case "phone_no":
                    $index = "PHONE";
                    break;
                case "address1":
                    $index = "ADDRESS1";
                    break;
                case "address2":
                    $index = "ADDRESS2";
                    break;
                case "country_id":
                    $index = "COUNTRY";
                    break;
                case "state_id":
                    $index = "STATE";
                    break;
                case "city":
                    $index = "CITY";
                    break;
                case "header":
                    $index = "HEADER";
                    break;
                case "footer":
                    $index = "FOOTER";
                    break;
                case "subs_fee":
                    $index = "SUBSFEE";
                    break;
                case "sub_question_lvls":
                    $index = "SUB QUESTION";
                    break;
                case "pagination_lvl":
                    $index = "PAGINATION";
                    break;
                case "mjb_sub":
                    $index = "SUBSCRIPTION";
                    break;
                case "insp_rpt_tree_view":
                    $index = "INSPECTION_REPORT_TREE_VIEW";
                    break;
                case "mjb_free_sign_up":
                    $index = "MJB_FREE_SIGN_UP";
                    break;
                case "mjb_free_license":
                    $index = "MJB_FREE_LICENSE";
                    break;
                case "cc_ge_free_checklist":
                    $index = "CC_GE_FREE_CHECKLIST";
                    break;
                case "action_items_on_off":
                    $index = "ACTION_ITEMS_ON_OFF";
                    break;
                case "status_indicator_on_off":
                    $index = "STATUS_INDICATOR_ON_OFF";
                    break;
                case "ios_version":
                    $index = "IOS_VERSION";
                    break;
                case "android_version":
                    $index = "ANDROID_VERSION";
                    break;
            }

            //dataset array
            $data_set = array(
                'name'          => $index,
                'value'         => $value,
                'created_by'    => Auth::user()->id,
                'updated_by'    => Auth::user()->id,
            );

            $save_master_user_data = $this->master_data->create($data_set);
        }

        if($save_master_user_data){
            $message = Config::get('messages.MASTER_DATA_UPDATE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }else{
            $message = Config::get('messages.MASTER_DATA_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Manage all user groups
     * @return mixed
     */
    public function userGroupManager(){
        return view('configuration.userGroupManager')->with('page_title', 'User Types Manager');
    }

    /**
     * Add new user group
     * @param UserGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertUserGroup(UserGroupRequest $request) {
        //get all user group form submitted data
        $user_group_details = $request->dataset;
        //get users entity type id
        $entity_type_id = app('App\Http\Controllers\Web\UserController')->getUserEntitiyType(Auth::user()->id);

        //dataset array
        $dataset = array(
            'name'          => $user_group_details['group_name'],
            'company_id'    => $user_group_details['company_id'],
            'status'        => $user_group_details['visibility'],
            'entity_type_id'=> $entity_type_id->id
        );

        $save_new_user_group = $this->user_group->create($dataset);

        if($save_new_user_group)
        {
            $message = Config::get('messages.USER_GROUP_INSERT_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }else{
            $message = Config::get('messages.USER_GROUP_INSERT_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Update User Group Details
     * @param UserGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEditUserGroup(UserGroupRequest $request) {
        //get all user group form submitted data
        $user_group_details = $request->dataset;

        $user_group_id = $request->dataset['user_group_id'];

        $dataset = array(
            'name'          => $user_group_details['group_name'],
            'entity_type_id'=> $user_group_details['edit_entity_id']
        );

        $update_user_group = $this->user_group->update($dataset, $user_group_id);

        if($update_user_group)
        {
            $message = Config::get('messages.USER_GROUP_UPDATE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }else{
            $message = Config::get('messages.USER_GROUP_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Add New User Group
     * @return mixed
     */
    public function addNewUserGroup() {
        //get all company list
        $company_list = $this->company->getAllCompanyList();
        return view('configuration.add_new_user_group')->with('page_title', 'Add New User Group')->with('company_list', $company_list);
    }

    /**
     * Edit user group details
     *
     * @param UserGroupRequest $request
     * @return json
     */
    public function editUserGroup(UserGroupRequest $request) {
        //declare and initialize variable
        $user_group_id = $request->dataset;

        $get_user_group_details = $this->user_group->find($user_group_id, array('*'));
        $entity_list = MasterEntityType::all();
        return Response()->json(array('success' => 'true', 'user_group' => $get_user_group_details, 'company_list' => $entity_list));
    }

    /**
     * Change user group status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatusUserGroup() {
        //declare and initialize variables
        $status = $_GET['status'];
        $id     = $_GET['id'];

        //change user group status
        $change_user_group_status = $this->user_group->update(array('status' => $status), $id);

        if($change_user_group_status)
        {
            $message = Config::get('messages.USER_GROUP_STATUS_CHANGE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }
        else{
            $message = Config::get('messages.USER_GROUP_STATUS_CHANGE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * Change country list item status
     * @return \Illuminate\Http\JsonResponse
     */
    public function ManageAddedCountry()
    {
        //declare and initialize variables
        $status = $_GET['status'];
        $id     = $_GET['id'];

        //change country status
        $change_country_status = $this->master_country->update(array('status' => $status), $id);

        if($change_country_status)
        {
            $message = Config::get('messages.COUNTRY_STATUS_CHANGE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message), 200);
        }
        else{
            $message = Config::get('messages.COUNTRY_STATUS_CHANGE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * get all country list
     * @return mixed
     */
    public function getAllCountryList(){
        return $this->master_country->getAllCountryList();
    }

    /**
     * get all state list
     * @return mixed
     */
    public function getAllStateListByCountry(){
        //declare and initialize variable
        $country_id = $_GET['country_id'];
        return $this->state->getAllStatesByCountry($country_id);
    }


    /*
     * Get Sub Question Level
     *
     * return array
     */
    public function getSubQuestionLevel(){
        $masterData =  $this->master_data->findWhere(array('name' => 'SUB QUESTION'))->first();
        if($masterData){
            return Response()->json(array('success' => 'true', 'value' => $masterData->value), 200);
        }
        else{
            return Response()->json(array('success' => 'true', 'value' => 0), 200); // return default value
        }

    }

    /**
     * Update city
     * @param CityListUpdateRequest $request
     * @return mixed
     */
    public function updateCity(CityListUpdateRequest $request){
        //declare and initialize variable
        $city_whithoutid = array();
        $city_withid = array();

        $city_list = $request->city_list;
        $state_id  = $request->state_id;

        if(is_array($city_list)) {
            foreach ($city_list as $item){
                if($item['id']==""){
                    $city_whithoutid[] = array(
                        'name' => $item['value'],
                    );
                }else{
                    $city_withid[] = array(
                        'id' => $item['id'],
                        'name' => $item['value'],
                    );
                }
            }
        }

        $save_city_list = $this->master_city->cityListUpdate($city_withid, $city_whithoutid, $state_id);

        if($save_city_list==1)
        {
            $message = Config::get('messages.CITY_LIST_UPDATE_SUCCESS');
            return Response()->json(array('success' => 'true', 'message' => $message));
        } else if($save_city_list==0) {
            $message = Config::get('messages.CITY_ALREADY_EXIT');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }else{
            $message = Config::get('messages.CITY_LIST_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * check occupied cities
     * @return mixed
     */
    public function checkCitiesOccupied(){
        //declare and initialize variable
        $id = $_GET['city_id'];

        //check if city allocated in the system
        $question_classification = QuestionClassification::where('entity_tag','CITY')->where('option_value', $id)->get();
        $appointment_classification = AppointmentClassification::where('entity_type', 'CITY')->where('option_value', $id)->get();
        $company_location = CompanyLocation::where('city_id' , $id)->get();

        if(isset($question_classification[0]) || isset($appointment_classification[0]) || isset($company_location[0])){
            $checkCity = 2;
        }else{
            $checkCity = 1;
        }

        if($checkCity==1){
            $message = Config::get('messages.CITY_OCCUPIED');
            return Response()->json(array('success' => 'true', 'status'=> 1, 'message' => $message));
        }elseif($checkCity==2){
            $message = Config::get('messages.CITY_NOT_OCCUPIED');
            return Response()->json(array('success' => 'true', 'status'=> 2, 'message' => $message));
        }
    }

    /**
     * create Coupon with coupon details
     * @param CouponCreateRequest $request
     * @return mixed
     */
    public function createCoupon(CouponCreateRequest $request) {

        $couponData = $request->all();

        if (!isset($couponData['id'])) {
            $coupon_check= \Validator::make($couponData,[
                'code' =>'unique:coupons'
            ]);
            $message='Coupon has been successfully created';
            if (isset($couponData['type'])) {
                if ($couponData['type'] == 'referral') {
                    $message='Referral code has been successfully created';
                }
            }
            $couponData['id']=0;
        }else {
            $coupon_check = \Validator::make($couponData, [
                'code' => 'unique:coupons,code,' . $couponData['id']
            ]);
            $message='Coupon has been successfully updated';
            if (isset($couponData['type'])) {
                if ($couponData['type'] == 'referral') {
                    $message='Referral code has been successfully updated';
                }
            }
        }


        $coupon_status = $coupon_check->passes();
        if($coupon_status==true){
            try {
                $savedCoupon = $this->coupon->saveOrEdit($couponData);

                if (isset($couponData['type']) && isset($couponData['master_referral_id']) && $couponData['id'] == 0 ) {
                        $referrer = $this->master_referral->find($couponData['master_referral_id']);

                        $coupon_detail = $this->coupon_detail->findWhere(array('coupon_id' => $savedCoupon->id));

                        if($coupon_detail[0]->type == 'fixed') {
                            $amount = '$ '.$coupon_detail[0]->amount;
                        } else {
                            $amount = $coupon_detail[0]->amount.'%';
                        }

                        if ($referrer) {

                            $token_email_data = new \stdClass();
                            $token_email_data->name = $referrer->name;
                            $token_email_data->email  = $referrer->email;
                            $token_email_data->from  = Config::get('simplifya.SIMPLIFIYA_EMAIL');
                            $token_email_data->link  =  url('company/mjb-register/' . $savedCoupon->token);
                            $token_email_data->code  =  $savedCoupon->code;
                            $token_email_data->amount  = $amount;
                            $token_email_data->company  =  Config::get('messages.COMPANY_NAME');
                            $token_email_data->system  =  Config::get('messages.COMPANY_NAME');
                            // send referral sign up link
                            event(new SendReferralToken($token_email_data));
                        }

                }
                return response()->json(array('success' => 'true', 'message' => $message));
            }catch (\Exception $e) {
                 \Log::debug("error saving coupon code ");
                \Log::debug($e->getTraceAsString());
                \Log::debug($e->getMessage());
                $exceptionCode = $e->getCode();
                $message = 'Coupon creation failed.';
                if ($exceptionCode == 10) {
                    $message = $e->getMessage();
                }
                return response()->json(array('success' => 'false', 'message' => $message));
            }
        }else {
            $coupon='Coupon already exists.';
            return response()->json( array('success' => 'false', 'message' => $coupon));
        }


    }

    /**
     * delete referral
     * @param ReferralCreateRequest $request
     * @return mixed
     */
    public function deleteReferrer(Request $request) {

        $referrerData = $request->all();
        $referral_id = $referrerData[0];

        try {
            $coupon_count = $this->coupon->getCouponsForReferral($referral_id);
            //\Log::debug("hit........................ ".print_r($coupon,true));
            if($coupon_count == 0)
            {
                $this->master_referral->deleteReferral($referral_id);
                return response()->json(array('success' => 'true', 'message' => 'Referrer deleted'));
            }
            else
            {
                return response()->json(array('success' => 'false', 'message' => 'Referrer already assigned to Coupon.'));
            }
        }catch (\Exception $e) {
            $message = 'Error on referrer deletion process';
            return response()->json(array('success' => 'false', 'message' => $message));
        }
    }

    /**
     * create Referrer with referrer details
     * @return mixed
     */
    public function createReferrer(Request $request) {

        $referrerData = $request->all();
        if (!isset($referrerData['id'])) {
            $message='Referrer has been successfully created';
            $referrerData['id']=0;
        }else {
            $message='Referrer has been successfully updated';
        }

        $planData=json_encode($referrerData['plan_details'],true);

        unset($referrerData['plan_details']);
        if (!array_key_exists("plan_details",$referrerData)){
            $referrerData['commission_rates']=$planData;
        }

        try {
            $this->master_referral->saveOrEdit($referrerData);
            return response()->json(array('success' => 'true', 'message' => $message));
        }catch (\Exception $e) {
            \Log::debug("error saving referrer ");
            \Log::debug($e->getTraceAsString());
            \Log::debug($e->getMessage());
            $exceptionCode = $e->getCode();
            $message = 'Referrer creation failed.';
            if ($exceptionCode == 10) {
                $message = $e->getMessage();
            }
            return response()->json(array('success' => 'false', 'message' => $message));
        }



    }

    /**
     * Returns all referral details for the list
     * @return mixed
     */
    public function getAllReferrals() {
        $referrals = $this->master_referral->getAllReferrals();
        \Log::debug("data....... :".print_r($referrals,true) );
        return response()->json(array('success' => 'true', 'data' => $referrals ));
    }

    /**
     * Returns all ref details for the list
     * @return mixed
     */
    public function referralCodes() {
        $coupons = $this->coupon->allCoupons(1);
        $couponData =  array_map(function ($item) {
            return [
                'id' => (INT)$item['id'],
                'code' => $item['code'],
                'description' => $item['description'],
                'start_date' => $item['start_date'],
                'start_date_timestamp' => Carbon::parse($item['start_date'])->getTimestamp(),
                'end_date' => $item['end_date'],
                'end_date_timestamp' => Carbon::parse($item['end_date'])->getTimestamp(),
                'master_subscription_id' => (INT)$item['master_subscription_id'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'master_subscription_name' => $item['master_subscription_name'],
                'used' => (INT)$item['used'],
            ];
        }, $coupons->toArray());
        return response()->json(array('success' => 'true', 'data' => $couponData ));
    }

    /**
     * Returns all coupon details for the list
     * @return mixed
     */
    public function allCoupons() {
        $coupons = $this->coupon->allCoupons();
        $couponData =  array_map(function ($item) {
            return [
                'id' => (INT)$item['id'],
                'code' => $item['code'],
                'description' => $item['description'],
                'start_date' => date("m/d/Y", strtotime($item['start_date'])),
                'start_date_timestamp' => Carbon::parse($item['start_date'])->getTimestamp(),
                'end_date' => date("m/d/Y", strtotime($item['end_date'])),
                'end_date_timestamp' => Carbon::parse($item['end_date'])->getTimestamp(),
                'master_subscription_id' => (INT)$item['master_subscription_id'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'master_subscription_name' => $item['master_subscription_name'],
                'used' => (INT)$item['used'],
            ];
        }, $coupons->toArray());
        return response()->json(array('success' => 'true', 'data' => $couponData ));
    }

    /**
     * Returns referrer details
     * @param $id
     * @return mixed
     */
    public function getReferrerDetails($id) {
        try {

            list($referrer,$referrerCodeDetails)=array_values($this->master_referral->getReferrerById($id));
            if(isset($referrer['commission_rates'])){
               $referrer['plan_details']=json_decode($referrer['commission_rates'],true);
                unset($referrer['commission_rates']);
            }
            if(count($referrerCodeDetails)){
                $referrer['referrer_code_details']=$referrerCodeDetails;
            }
            return response()->json(array('success' => 'true', 'data' => array('referrer' => $referrer) ));
        }catch (\Exception $e) {
            \Log::debug("error retrieving coupon data " + $e->getMessage());
            \Log::debug($e->getTraceAsString());
            return response()->json(array('success' => 'false', 'message' => $e->getMessage()));
        }
    }
    /**
     * Returns referrer details
     * @param $id
     * @return mixed
     */
    public function getReferrerCommissionDetails($id) {
        try {
            $commissions = $this->coupon->allCommissions($id);
            \Log::info(print_r($commissions,true));
            $commission_data =  array_map(function ($item) {

                $status=(isset($item['referral_payment_id']) &&$item['referral_payment_id']!=0)?'1':'0';


                return [
                    'id' => (INT)$item['id'],
                    'referral_payment_id' => $item['referral_payment_id'],
                    'commission' => $item['commission'],
                    'mjb_name' => $item['mjb_name'],
                    'plan' => $item['plan'],
                    'created_at' => Carbon::parse($item['created_at'])->toDateString(),
                    'company_subscription_id' => (INT)$item['company_subscription_id'],
                    'status'=>$status
                ];
            }, $commissions->toArray());
            return response()->json(array('success' => 'true', 'data' => array('commissions' => $commission_data) ));
        }catch (\Exception $e) {
            \Log::debug("error retrieving coupon data " + $e->getMessage());
            \Log::debug($e->getTraceAsString());
            return response()->json(array('success' => 'false', 'message' => $e->getMessage()));
        }
    }

    /**
     * Returns referrer commission details
     * @param $id
     * @return mixed
     */
    public function getReferrerCommissionPayments($id){
        try {
            $commission_payments = $this->master_referral_payment->allCommissionPaymentsByReferrer($id);
            $commission_payment_data =  array_map(function ($item) {

                return [
                    'id' => (INT)$item['id'],
                    'comment' => $item['comment'],
                    'amount' => $item['amount'],
                    'created_at' => Carbon::parse($item['created_at'])->toDateString(),
                ];
            }, $commission_payments->toArray());
            return response()->json(array('success' => 'true', 'data' => array('commission_payments' => $commission_payment_data) ));
        }catch (\Exception $e) {
            \Log::debug("error retrieving coupon data " + $e->getMessage());
            \Log::debug($e->getTraceAsString());
            return response()->json(array('success' => 'false', 'message' => $e->getMessage()));
        }
    }

    public function getCouponDetails($id) {
        try {
            list($coupon, $couponDetails) = array_values($this->coupon->getCouponById($id));
            if (count($couponDetails)) {
                $coupon['coupon_details'] = $couponDetails;
            }
            return response()->json(array('success' => 'true', 'data' => array('coupon' => $coupon) ));
        }catch (\Exception $e) {
            \Log::debug("error retrieving coupon data " + $e->getMessage());
            \Log::debug($e->getTraceAsString());
            return response()->json(array('success' => 'false', 'message' => $e->getMessage()));
        }
    }

    /**
     * Get referral coupon by id
     * @param $id
     * @return mixed
     */
    public function getCouponReferralDetails($id) {
        try {
            list($coupon, $couponDetails) = array_values($this->coupon->getReferralCouponById($id));
            if (count($couponDetails)) {
                $coupon['coupon_details'] = $couponDetails;
            }
            return response()->json(array('success' => 'true', 'data' => array('coupon' => $coupon) ));
        }catch (\Exception $e) {
            \Log::debug("error retrieving coupon data " + $e->getMessage());
            \Log::debug($e->getTraceAsString());
            return response()->json(array('success' => 'false', 'message' => $e->getMessage()));
        }
    }

    /**
     * Manage referral codes
     * @return view
     */
    public function referralCodeManager()
    {
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.referralCodeManager')->with('page_title', 'Referral Code Manager')->with('MJB_entity_type',$MJB_entity_type);
    }

    /**
     * Add new referral code
     * @param null $referral_id
     * @return
     */
    public function addNewReferralCode($referral_id = null) {

        if(isset($referral_id)) {
            $page_title='Edit Referral Code';
            $couponId=$referral_id;
        } else {
            $page_title='Add New Referral Code';
            $couponId=0;
        }
        // get the minimum subscription plan amount and pass that to view
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        $maxPlanAmount = $this->subscription->getMinimumSubscriptionAmountByEntityType($MJB_entity_type)->amount;
        return view('configuration.add_new_referral_code')->with('page_title', $page_title)->with('MJB_entity_type',$MJB_entity_type)->with('coupon_id',$couponId)->with('maxPlanAmount', $maxPlanAmount)->with('read_only', 'false');

    }

    public function viewNewReferralCode($referral_id = null) {

        if(isset($referral_id)) {
            $page_title='View Referral Code';
            $couponId=$referral_id;
        } else {
            $page_title='Add New Referral Code';
            $couponId=0;
        }
        // get the minimum subscription plan amount and pass that to view
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        $maxPlanAmount = $this->subscription->getMinimumSubscriptionAmountByEntityType($MJB_entity_type)->amount;
        return view('configuration.add_new_referral_code')->with('page_title', $page_title)->with('MJB_entity_type',$MJB_entity_type)->with('coupon_id',$couponId)->with('maxPlanAmount', $maxPlanAmount)->with('read_only', 'true');

    }

    /**
     * Add referrer Commission
     * @param Request $request
     * @return mixed
     */

    public function saveReferrerCommissions(Request $request){


        $commissionData=$request->all();
        try {
            $this->master_referral_payment->createReferralPaymentAndUpdateCompanySubscriptions($commissionData);
            return response()->json(array('success' => 'true', 'message' => 'Successfully Updated'));
        }catch (\Exception $e) {
            \Log::debug("error creating referrer payment ");
            \Log::debug($e->getTraceAsString());
            \Log::debug($e->getMessage());
            $exceptionCode = $e->getCode();
            $message = 'Referrer payment creation failed.';
            if ($exceptionCode == 10) {
                $message = $e->getMessage();
            }
            return response()->json(array('success' => 'false', 'message' => $message));
        }


    }

    /**
     * Manage Applicabilities
     * @return view
     */
    public function applicabilityManager()
    {
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.applicabilityManager')->with('page_title', 'Applicability Manager')->with('MJB_entity_type',$MJB_entity_type);
    }

    /**
     * Add, new applicability
     *
     * @return $this
     */
    public function addNewApplicability($applicability_id=null)
    {
        if(isset($applicability_id)){
            $page_title='Edit Applicability';
            $applicabilityId=$applicability_id;
        }else{
            $page_title='Add New Applicability';
            $applicabilityId=0;
        }
        return view('configuration.add_new_applicability')->with('page_title', $page_title)->with('applicability_id',$applicabilityId);
    }


    /**
     * Get applicability types and groups
     *
     * @return $this
     */
    public function getApplicabilityTypesAndGroups(){
        $applicability_types=Config::get('simplifya.APPLICABILITY_TYPES');
        $applicability_groups=Config::get('simplifya.APPLICABILITY_GROUPS');
        return response()->json(array('success' => 'true', 'types' => $applicability_types,'groups'=>$applicability_groups));
    }

    /**
     * create Applicability with applicability details
     * @return mixed
     */
    public function createApplicability(Request $request) {

        $applicabilityData = $request->all();
        if (!isset($applicabilityData['id'])) {
            $message='Applicability has been successfully created';
            $applicabilityData['id']=0;
        }else {
            $message='Applicability has been successfully updated';
        }



        try {
            $this->master_applicability->saveOrEdit($applicabilityData);
            return response()->json(array('success' => 'true', 'message' => $message));
        }catch (\Exception $e) {
            \Log::debug("error saving referrer ");
            \Log::debug($e->getTraceAsString());
            \Log::debug($e->getMessage());
            $exceptionCode = $e->getCode();
            $message = 'Referrer creation failed.';
            if ($exceptionCode == 10) {
                $message = $e->getMessage();
            }
            return response()->json(array('success' => 'false', 'message' => $message));
        }



    }

    /**
     * Returns all applicability details for the list
     * @return mixed
     */
    public function applicabilityList() {
        $applicabilities = $this->master_applicability->allApplicabilities();
        $applicability_types=Config::get('simplifya.APPLICABILITY_TYPES');
        $applicability_groups=Config::get('simplifya.APPLICABILITY_GROUPS');
        $applicabilityData =  array_map(function ($item) use ($applicability_types,$applicability_groups) {
            return [
                'id' => (INT)$item['id'],
                'name' => $item['name'],
                'country' => $item['country'],
                'types' => $applicability_types[$item['type']],
                'groups'=>$applicability_groups[$item['group_id']],
                'status'=>$item['status']
            ];
        }, $applicabilities->toArray());

        return response()->json(array('success' => 'true', 'data' => $applicabilityData ));
    }


    public function getApplicabilityById($id){
        $applicability= $this->master_applicability->getApplicabilityById($id);
        return response()->json(array('success' => 'true', 'data' => $applicability ));
    }

    public function keywordManager()
    {
        $MJB_entity_type=Config::get('simplifya.MarijuanaBusiness');
        return view('configuration.keywordManager')->with('page_title', 'Keywords Manager')->with('MJB_entity_type',$MJB_entity_type);

    }

    public function getKeywords()
    {
        $keywords = $this->master_keyword->getAllKeywords();
        return response()->json(array('success' => 'true', 'data' => $keywords ));
    }

    public function deleteKeywordsById($keywordId)
    {
        $keywords = $this->master_keyword->deleteKeyword($keywordId);
        return response()->json(array('success' => $keywords ));
    }

    /**
     * Edit Question Comment (note)
     *
     * @return mixed
     */
    public function editKeywordName(Request $request)
    {
        //get specific appointment question
        $getKeyword = $this->master_keyword->find($request->id);

        if($getKeyword){
            //update relevant question comment
            $update = $this->master_keyword->updateKeyword($request->tempName, $getKeyword['id']);
            if($update){
                $message = Config::get('messages.KEYWORD_UPDATE_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => $message), 200);
            } else {
                $message = Config::get('messages.KEYWORD_UPDATE_FAILED');
                return Response()->json(array('success' => 'false', 'data' => $message));
            }
        }else{
            $message = Config::get('messages.KEYWORD_UPDATE_FAILED');
            return Response()->json(array('success' => 'false', 'data' => $message));
        }
    }

    public function getQuestionKeywordById($keywordId)
    {
        $question_keyword = $this->question_keyword->findWhere(array('keyword_id' => $keywordId));
        if(isset($question_keyword[0])) {
            $message = Config::get('messages.KEYWORD_ALREADY_USED');
            return response()->json(array('success' => 'false', 'message' => $message ));
        } else {
            return response()->json(array('success' => 'true'));
        }
    }

    public function getApplicabilityTypes(){
        $applicability_types=Config::get('simplifya.APPLICABILITY_TYPES');
    }

    /**
     * delete applicability
     * @param $applicability_id
     * @return mixed
     */
    public function deleteApplicability($applicability_id) {

        try {
            $license_applicability_count = count($this->master_license_applicability->findAllBy('master_applicability_id',$applicability_id));


            if($license_applicability_count == 0)
            {
                $this->master_applicability->deleteApplicability($applicability_id);
                return response()->json(array('success' => 'true', 'message' => 'Applicability deleted'));
            }
            else
            {
                return response()->json(array('success' => 'false', 'message' => 'Applicability already assigned to License.'));
            }
        }catch (\Exception $e) {
            $message = 'Error on applicability deletion process';
            return response()->json(array('success' => 'false', 'message' => $message));
        }
    }
    /**
     * change the status of  applicability
     * @return mixed
     */
    public function changeApplicabilityStatus(Request $request) {
        $applicability_id=$request->id;
        $status=$request->status;

        try {
            $this->master_applicability->changeApplicabilityStatus($applicability_id,$status);
            if($status==1){

                return response()->json(array('success' => 'true', 'message' => 'Applicability Activated'));
            }else{

                return response()->json(array('success' => 'true', 'message' => 'Applicability Deactivated'));
            }

        }catch (\Exception $e) {
            $message = 'Error on applicability status process';
            return response()->json(array('success' => 'false', 'message' => $message));
        }
    }
}