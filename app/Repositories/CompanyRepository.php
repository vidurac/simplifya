<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/9/2016
 * Time: 9:48 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Auth;


class CompanyRepository extends Repository
{
    public function model()
    {
        return 'App\Models\Company';
    }

    /**
     * Get all active companies list
     * @return mixed
     */
    public function getAllCompanyList()
    {
        return $this->model
            ->where('status', '=', '2')
            ->get();
    }

    /**
     * get all active compliance company list
     * @return mixed
     */
    public function getComplianceCompanyList()
    {
        return $this->model
                ->where('status', '=', '2')
                ->where('entity_type', '=', '3')
                ->get();
    }

    /**
     * get all active compliance company list
     * @return mixed
     */
    public function getComplianceCompanyForRequest($user)
    {
        if ($user->master_user_group_id == Config::get('simplifya.CcMasterAdmin'))
        {
            return $this->model
                ->where('status', '=', '2')
                ->where('entity_type', '=', '3')
                ->where('id', '=', $user->company_id)
                ->get();
        } else
        {
            return $this->model
                ->where('status', '=', '2')
                ->where('entity_type', '=', '3')
                ->get();    
        }
    }

    /**
     * get all active compliance and government entity company list
     * @return mixed
     */
    public function getCCAndGEList()
    {
        return $this->model
            ->where('status', '=', '2')
            ->whereIn( 'entity_type', ['3','4'])
            ->get();
    }

    /**
     * get all active marijuana company list
     * @return mixed
     */
    public function getMarijuanaCompanyList()
    {
        return $this->model
            ->where('status', '=', '2')
            ->where('entity_type', '=', '2')
            ->get();
    }

     /**
     * get all active marijuana company list
     * @return mixed
     */
    public function getMarijuanaCompanyForRequest($user)
    {
        if ($user->master_user_group_id == Config::get('simplifya.MjbMasterAdmin'))
        {
            return $this->model
                ->where('status', '=', '2')
                ->where('entity_type', '=', '2')
                ->where('id', '=', $user->company_id)
                ->get();
        } else
        {
            return $this->model
                ->where('status', '=', '2')
                ->where('entity_type', '=', '2')
                ->get();   
        }
    }

    /**
     * get active compliance company by ID
     * @param $company_id
     * @return mixed
     */
    public function getComplianceCompanyByID($company_id)
    {
        return $this->model
            ->where('id', '=', $company_id)
            ->where('status', '=', '2')
            ->get();
    }

    /**
     * get active compliance company by ID
     * @param $company_id
     * @return mixed
     */
    public function getMarijuanaCompanyByID($company_id)
    {
        return $this->model
            ->where('id', '=', $company_id)
            ->where('status', '=', '2')
            ->get();
    }

    /**
     * Get company location by company ID
     * @param $company_id
     * @return mixed
     */
    public function getCompanyLocations($company_id)
    {
        return $this->model->where('id', $company_id)->get();
    }

    /**
     * get Company by company type or company ID
     * @param null $company_id
     * @param null $companyType
     * @return mixed
     */
    public function getCompany($company_id = null, $companyType = null){
        if($company_id == null) {
            return $this->model->where('status', '=', 2)->where('entity_type', '=', $companyType )->get();
        } else if($company_id != null && $companyType == null) {
            return $this->model->where('id', $company_id)->get();
        }
    }

    /**
     * get All Inspection requests
     * @param $sql
     * @return mixed
     */
    public static function getInspectionRequests($sql)
    {
        $result = DB::select($sql);
        return $result;
    }

    /**
     * get total number of requests
     * @return mixed
     */
    public static function getTotaleNumber()
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `requests`");
        return $result;
    }

    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

    /**
     * Update company by passing company ID
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateById($id, $data)
    {
        return $this->model
                    ->where('id', $id)
                    ->update($data);
    }

    /**
     * check if company exist in the system
     * @param $reg_no
     * @return mixed
     */
    public function isExistCompany($reg_no)
    {
        return $this->model
                    ->where('reg_no', $reg_no)
                    ->get();
    }

    public function updateCompanyByStatusAndId($status, $company_id)
    {
        return $this->model
            ->where('id', $company_id)
            ->update(array('status' => $status));
    }

    public function getUserGroupe($company_id)
    {
        return $this->model
            ->select('master_user_groups.id as id', 'master_user_groups.name as name')
            ->join('master_user_groups', 'companies.entity_type', '=', 'master_user_groups.entity_type_id')
            ->where('companies.id', $company_id)
            ->get();
        return $result;
    }

    public function calculateSubscriptionFee($company_id)
    {
        return DB::table('companies')
            ->select('companies.name as company_name', 'master_entity_types.name as master_entity_name' ,'company_location_licenses.id as license_id', 'master_subscriptions.amount as amount')
            ->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id')
            ->join('company_location_licenses', function($join){
                $join->on('company_location_licenses.company_id', '=', 'companies.id')
                    ->where('company_location_licenses.status','=', 1);
            })
            ->join('master_subscriptions', 'companies.entity_type', '=', 'master_subscriptions.entity_type_id')
            ->where('master_subscriptions.status', 1)
            ->where('master_subscriptions.validity_period_id', 1)
            ->where('companies.id', $company_id)
            ->get();
    }

    public function getSubscriptionFee($company_id)
    {
        return DB::table('companies')
            ->select('master_subscriptions.amount as amount')
            ->join('master_subscriptions', 'companies.entity_type', '=', 'master_subscriptions.entity_type_id')
            ->where('master_subscriptions.status', 1)
            //->where('master_subscriptions.validity_period_id', 1)
            ->where('companies.id', $company_id)
            ->get();
    }

    public function getCompanyDetails($company_id)
    {
        return $this->model
                    ->where('id', $company_id)
                    ->get();
    }

    public function getCompanyDetailsbyId($company_id)
    {
        return DB::table('companies')
             ->select('companies.*','coupons.code as coupon_code','coupons.id as coupon_id','coupons.type as coupon_type','coupon_details.amount as coupon_amount','coupon_details.type as amount_type', 'master_entity_types.name as master_entity_name', 'master_entity_types.id as master_entity_id','coupon_details.id as coupon_details_id','coupons.start_date','coupons.end_date')
             ->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id')
             ->leftJoin('coupons', 'companies.coupon_referral_id', '=', 'coupons.id')
             ->leftJoin('coupon_details', 'coupon_details.coupon_id', '=', 'coupons.id')
             ->where('companies.id', $company_id)
             ->get();
    }

    public function getSubscriptionType($company_id)
    {
        return $this->model
                    ->with('masterEntityType.masterSubscription')
                    ->where('id', $company_id)
                    ->get();
    }

    public function getAllInspectors($company_id, $entityType, $locationId)
    {
        $quiry =  $this->model->where('id', $company_id);
        // if CC
        if($entityType == 3){
            $quiry->with(array('user'=> function($query){
                $query->where('users.master_user_group_id', '=', 6)->orwhere('users.master_user_group_id', '=', 5);
                $query->where('users.status', '=', 1);
            }));
        }

        // if Government entity
        if($entityType == 4){
            $quiry->with(array('user'=> function($query){
                $query->where('users.master_user_group_id', '=', 8)->orwhere('users.master_user_group_id', '=', 7);
                $query->where('users.status', '=', 1);
            }));
        }

        // if MJ Business
        if($entityType == 2){

            $quiry->with(array('user'=> function($query)use($locationId){
                $query->with(array("companyUser"=> function($queryinner)use($locationId){
                    if($locationId != 0){
                        $queryinner->where('location_id', '=', $locationId);
                    }
                }));
                $query->where('users.master_user_group_id', '=', 3)->orwhere('users.master_user_group_id', '=', 2);
                $query->where('users.status', '=', 1);
            }));
        }

        $quiry->where('entity_type', $entityType);
        $result = $quiry->get();
        return $result;
    }


    public function getAllInspectorsEdit($company_id, $entityType, $locationId)
    {
        $quiry =  $this->model->where('id', $company_id);
        // if CC
        if($entityType == 3){
            $quiry->with(array('user'=> function($query){
                $query->where('users.master_user_group_id', '=', 6)->orwhere('users.master_user_group_id', '=', 5);
                $query->where('users.status', '=', 1);
            }));
        }

        // if Government entity
        if($entityType == 4){
            $quiry->with(array('user'=> function($query){
                $query->where('users.master_user_group_id', '=', 8)->orwhere('users.master_user_group_id', '=', 7);
                $query->where('users.status', '=', 1);
            }));
        }

        // if MJ Business
        if($entityType == 2){

            $quiry->with(array('user'=> function($query)use($locationId){
                $query->with(array("companyUser"=> function($queryinner)use($locationId){
                    if($locationId != 0){
                        $queryinner->where('location_id', '=', $locationId);
                    }
                }));
                $query->where('users.master_user_group_id', '=', 3)->orwhere('users.master_user_group_id', '=', 2);
                $query->where('users.status', '=', 1);
            }));
        }

        $quiry->where('entity_type', $entityType);
        $result = $quiry->get();

        return $result;
    }

    public function getAllCompanies()
    {
        return $this->model
                    ->select('companies.id as id','companies.created_at as created_at', 'companies.name as name','master_entity_types.name as masterEntityType', 'companies.status as status')
                    ->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id')
                    ->where('companies.entity_type', '!=', '1')
                    ->where('companies.status', '!=', '7')
                    ->orderBy('companies.id', 'desc')
                    ->get();
    }

    // get all pending comapny. entity type 3 or 4 and status 1
    public function getAllPendingCompanies()
    {          
        return $this->model
                    ->select('companies.id as id','companies.created_at as created_at', 'companies.name as name','master_entity_types.name as masterEntityType', 'companies.status as status')
                    ->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id')
                    ->where(function ($query){
                        $query->where('companies.entity_type', '=', '3')
                            ->orWhere('companies.entity_type', '=', '4');
                    })
                    ->where('companies.status', '=', '1')
                    ->get();
    }

    // get compnay account for entity type 3 and 4
    public function getRegisterCompanyCount($type)
    {
        return $this->model
                    ->select(DB::raw('COUNT(companies.id) as count'))
                    ->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id')
                    ->where('companies.entity_type', '=', $type)
                    ->where('companies.status', '=', '2')
                    ->first();
    }

    public function getUserPermissionByCompanyId($company_id)
    {
        return $this->model
                    ->select('master_user_groups.id as permission_id', 'master_user_groups.name as permission_name')
                    ->join('master_user_groups', 'companies.entity_type', '=', 'master_user_groups.entity_type_id')
                    ->where('companies.id', $company_id)
                    ->get();
    }

    public function searchCompany($business_name, $entity_type, $status)
    {
        if($entity_type != '') {
            $quiry =  $this->model->where('companies.entity_type', '=', $entity_type);
        } else {
            $quiry =  $this->model->where('companies.entity_type', '!=', '1');
        }
        if($status != '') {
            $quiry->where('companies.status', $status);
        }else{
            $quiry->where('companies.status', '!=',7);

        }
        if($business_name != '') {
            $quiry->where('companies.name', 'LIKE', '%'.$business_name.'%');
        }
            $quiry->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id');
            $quiry->select('companies.id as id','companies.created_at as created_at', 'companies.name as name','master_entity_types.name as masterEntityType', 'companies.status as status');
            $result = $quiry->get();
        return $result;
    }


    /**
     *  find companies by entity.
     * @param  $entityTypes
     * @return json
     */
    public function findCompanyByEntity($entityTypes){
        return $this->model->whereIn('entity_type', $entityTypes)->where("status", 2)->get();
    }
    
    /**
     * Get company details by company id
     * @param type $company_id
     * @return type
     */
    public function getCompanyById($company_id){
       $results = DB::table('companies')->where('id', '=', $company_id)->get();
       return $results;
    }





    /**
     *  find all active companies
     * @param  $simplifiyaAdminId
     * @return json
     */
    public function findAllActiveCompanies($simplifiyaAdminId){
        return $this->model->where('id', '!=',  $simplifiyaAdminId)->where("status", 2)->get();
    }


    public function getAllActiveCompanyUsers()
    {
        $result = $this->model
            ->select('companies.name as company_name', 'users.email as email', 'users.name as user_name', 'users.id as user_id', 'users.created_by as created_user', 'users.company_id')
            ->join('users', function($join){
                $join->on('users.company_id', '=', 'companies.id')
                    ->where('users.is_invite', '=', 1)
                    ->Where('users.status', '=', 1)
                    ->Where('users.is_send_mail', '=', 0);
            })
            ->where('companies.status','=', 2)
            ->where('companies.entity_type','=', 2)
            ->get();

        return $result;
    }

    public function getCardDetails($company_id)
    {
        return $this->model
                ->select('company_cards.card_id as card_id', 'companies.stripe_id as stripe_id')
                ->join('company_cards', 'companies.id', '=', 'company_cards.company_id')
                ->where('companies.id', $company_id)
                ->where('company_cards.status', 1)
                ->get();
    }

    public function getAllActivateCompanies()
    {
        return $this->model
                    ->where('status', '2')
                    ->where('entity_type', '!=','1')
                    ->get();
    }

    public function getAllActiveAndAbleToChargeCompanies()
    {
        return $this->model
            ->where('status', '2')
            ->where('entity_type', '=','2')
            ->get();
    }

    public function getAdminUser($company_id, $user_type)
    {
        return DB::table('users')
                ->where('company_id', $company_id)
                ->where('is_invite','!=', 0)
                ->where('master_user_group_id','=', $user_type)
                ->get();
    }

    public function updateIsAttempt($company_id)
    {
        return $this->model
            ->where('id', $company_id)
            ->update(array('is_first_attempt' => 1));
    }

    /**
     * Find all companies with zero audits
     * @param $status
     * @param $entityType
     * @return mixed
     */
    public function companiesWithNoAudit($status, $entityType) {
        return $this->model->
            leftJoin('appointments', 'companies.id', '=', 'appointments.from_company_id')
            ->selectRaw("COUNT(appointments.id) c, companies.*")
            ->where('status', $status)
            ->where('entity_type', $entityType)
            ->groupBy('companies.id')
            ->havingRaw('c = 0')
            ->get();

    }

    public function getSubscriptionPackage($entityType) {
        return DB::table('master_subscriptions')
            ->where('status','=', 1)
            ->where('entity_type_id','=', $entityType)
            ->get();

    }


    public function calculateSubscriptionForPlanFee($company_id,$package_id)
    {
        return DB::table('companies')
            ->select('companies.name as company_name', 'master_entity_types.name as master_entity_name' ,'company_location_licenses.id as license_id', 'master_subscriptions.amount as amount')
            ->join('master_entity_types', 'companies.entity_type', '=', 'master_entity_types.id')
            ->join('company_location_licenses', function($join){
                $join->on('company_location_licenses.company_id', '=', 'companies.id')
                    ->where('company_location_licenses.status','=', 1);
            })
            ->join('master_subscriptions', 'companies.entity_type', '=', 'master_subscriptions.entity_type_id')
            ->where('master_subscriptions.status', 1)
            ->where('master_subscriptions.id', $package_id)
            ->where('companies.id', $company_id)
            ->get();
    }

    public function createNonMjb($companyData,$user){
        try {

            \DB::beginTransaction();
            $c =$this->model->firstOrNew(array('id'=>$companyData['company_id']));
            $c->name = $companyData['name_of_business'];
            $c->entity_type = 2;
            $c->status = 7;
            // Save coupon data

            if($c->save()){
                $company_location=$this->createNonMjbLocation($c,$companyData,$user);
                if ($company_location){

                    $company_location_licenses=$this->createNonMjbLicenses($c,$companyData['choices'],$company_location,$user);

                    if($company_location_licenses){
                        \DB::commit();
                        return $c->id;
                    }
                }
            }

        }catch (Exception $e) {
            \DB::rollback();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function createNonMjbLocation($company_id,$companyData,$user){
        $company_location=$company_id->companyLocation()->firstOrNew(array('id'=>$companyData['company_location_id']));

        $company_location->name=$company_id->name;
        $company_location->city_id=$companyData['city'];
        $company_location->states_id=$companyData['state'];
        $company_location->address_line_1=$companyData['add_line_1'];
        $company_location->address_line_2=isset($companyData['add_line_2'])?$companyData['add_line_2']:'';
        $company_location->zip_code=$companyData['zip_code'];
        $company_location->phone_number=$companyData['phone_no'];
        $company_location->status=1;
        $company_location->created_by=$user;
        $company_location->contact_email=(isset($companyData['contact_email']))?$companyData['contact_email']:null;
        $company_location->contact_person=(isset($companyData['contact_person']))?$companyData['contact_person']:null;

        if ($company_location->save()){
            return $company_location;
        }
    }

    public function createNonMjbLicenses($company_id,$choises,$company_location_id,$user){
        $delete_exisiting_licenses=$this->deleteNonMjbLicense($company_id);
        if($delete_exisiting_licenses>=0){
            $actual_count=count($choises);
            $inserted_count=0;
            foreach ($choises as $choise){
                $companyLocationLicense=[
                    'license_id'=>$choise['license'],
                    'location_id'=>$company_location_id->id,
                    'license_number'=>$choise['licen_no'],
                    'status'=>1,
                    'created_by'=>$user
                ];
                if($company_id->companyLocationLicense()->create($companyLocationLicense)){

                    $inserted_count ++;
                };

            }

            if($actual_count  == $inserted_count )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

    }

    public function getNonMJBCompany($company_id){
        return $this->model->with('companyLocation')->where('id', $company_id)->first();
    }

    public function deleteNonMjbLicense($company_id){

        return $company_id->companyLocationLicense()->delete();
    }
    public function  getNonMJBCompanyWithCountry($company_id){

        return DB::table('company_locations')
            ->select('companies.id as company_id','companies.name', 'company_locations.*','master_states.country_id')
            ->join('companies', 'company_locations.company_id', '=', 'companies.id')
            ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
            ->where('companies.id', $company_id)
            ->get();

    }
    public function companyLocationLicense($company_id){
        return DB::table('master_licenses')
            ->select('master_licenses.*', 'company_location_licenses.id as license_id', 'company_location_licenses.license_number')
            ->join('company_location_licenses', 'company_location_licenses.license_id', '=', 'master_licenses.id')
            ->where('company_location_licenses.company_id', $company_id)
            ->get();
    }

    public function getNonMJBCompanyWithLicense($company_id){
        $company=$this->getNonMJBCompanyWithCountry($company_id);
            if (isset($company)) {
            $companyLicenses = $this->companyLocationLicense($company_id);
            return array('company' => $company, 'company_license' => $companyLicenses);
        }else {
            throw new \Exception('No company found');
        }

    }
    public function setCommissionEndDate($commission_period)
    {
        \Log::debug("===== commission_period " . print_r($commission_period, true));
        $data = array("commission_end_date" => Carbon::now()->addMonths($commission_period)->toDateString());

        return $this->model->where('id', Auth::User()->company_id)->update($data);
    }

    public function getUserLocations($user_id)
    {
        return DB::table('company_users')
                ->select('company_locations.*')
                ->join('company_locations', 'company_locations.id', '=', 'company_users.location_id')
                ->where('company_users.user_id', $user_id)
                ->get();
    }
}