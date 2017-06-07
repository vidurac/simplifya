<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/9/2016
 * Time: 9:48 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class AppointmentRepository extends Repository
{
    public function model()
    {
        return 'App\Models\appointment';
    }

    /**
     * @param $dataset
     * @return mixed
     * Save an appointment
     */
    public function createAppointment($dataset)
    {
        return $this->model->create($dataset);
    }

    public function searchAppointments($fromDate, $toDate, $mjBusiness, $companyName, $status, $entityType, $thPartyAudit, $user=false)
    {
        // if cc or government entity
        if($entityType == "cc"){
            if($mjBusiness != ""){
                $qry = $this->model->where('from_company_id', $companyName)->where('to_company_id', $mjBusiness);
            }
            else{
                $qry = $this->model->where('from_company_id', $companyName);
            }
        }
        //if mj business
        else if($entityType == "mj"){
            if($companyName != ""){
                $qry = $this->model->where('from_company_id', $companyName)->where('to_company_id', $mjBusiness);
            }
            else{
                if($thPartyAudit == 'true') {
                    if($user->master_user_group_id != Config::get('simplifya.MjbManager')) {
                        $qry = $this->model->where(function($query) use ($mjBusiness, $companyName){
                            $query->Where('from_company_id', '!=', $mjBusiness);
                            $query->Where('to_company_id', '=', $mjBusiness);
                            $query->Where('share_mjb', '=', 1);
                            $query->Where('report_status', '=', 3);
                        });
                    } else {
                        $locations  = $this->getUserLocations($user->id);
                        $qry = $this->model->where(function($query) use ($mjBusiness, $companyName, $locations){
                            $query->Where('from_company_id', '!=', $mjBusiness);
                            $query->Where('to_company_id', '=', $mjBusiness);
                            $query->Where('share_mjb', '=', 1);
                            $query->Where('report_status', '=', 3);
                            $query->whereIn('company_location_id', $locations);
                        });
                    }

                } else {
                    if($user->master_user_group_id != Config::get('simplifya.MjbManager')) {
                        $qry = $this->model->where(function($query) use ($mjBusiness){
                            $query->Where('from_company_id', $mjBusiness);
                            $query->Where('to_company_id', $mjBusiness);
                        });
                    } else {
                        $locations  = $this->getUserLocations($user->id);
                        $qry = $this->model->where(function ($query) use ($mjBusiness, $locations) {
                            $query->Where('from_company_id', $mjBusiness);
                            $query->Where('to_company_id', $mjBusiness);
                            $query->whereIn('company_location_id', $locations);
                        });

                    }

                }

            }
        }
        // if supper admin
        else if($entityType == "admin"){
            if($companyName != "" && $mjBusiness != ""){
                $qry = $this->model->where('from_company_id', $companyName)->where('to_company_id', $mjBusiness);
            }
            else if($companyName != "" ){
                $qry = $this->model->where('from_company_id', $companyName);
            }
            else if($mjBusiness != ""){
                $qry = $this->model->where('to_company_id', $mjBusiness);
            }
            else{
                $qry = $this->model;
            }
        }
        else{
            $qry = $this->model;
        }


        if($fromDate != "" && $toDate != ""){
            $qry->whereBetween('inspection_date_time', array($fromDate, $toDate));
        }
        else if($fromDate != ""){
            $qry->where('inspection_date_time', '>=', $fromDate);
        }
        else if($toDate != ""){
            $qry->where('inspection_date_time', '<=', $toDate);
        }

        if($status != ""){
            $qry->where('appointment_status', $status);
        }

        return $qry->orderBy('created_at', 'desc')->get();
        //$result = $qry->orderBy('created_at', 'desc')->toSql();
        //print_r($result);die;
    }

    public function getUserLocations($user_id)
    {
        $location = [];
        $results = DB::table('company_users')
                    ->select('company_locations.id')
                    ->join('company_locations', 'company_locations.id', '=', 'company_users.location_id')
                    ->where('company_users.user_id', $user_id)
                    ->get();
        foreach ($results as $result) {
            $location[] = $result->id;
        }
        return $location;
    }

    public function getAppointmentDetailsById($id)
    {
        return $this->model->get();
    }

    /**
     * get All Inspection reports
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
        $result = DB::select("SELECT COUNT(`id`) as count FROM `appointments`");
        return $result;
    }

    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

    /**
     * get total number of requests
     * @return mixed
     */
    public static function getFilteredTotalNumber($where)
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `appointments`".$where);
        return $result;
    }

    public static function getFilteredManagerTotalNumber($where)
    {
        $result = DB::select("SELECT COUNT(`appointments`.`id`) as count FROM `appointments`".$where);
        return $result;
    }

    /**
     * get total number of requests
     * @return mixed
     */
    public static function getFilteredEmployeeTotalNumber($where)
    {
        $result = DB::select("SELECT COUNT(`appointments`.`id`) as count FROM `appointments` JOIN `appointment_classifications` ON `appointments`.`id` = `appointment_classifications`.`appointment_id` JOIN appointment_action_item_users ON `appointment_action_item_users`.`appointment_id` = `appointments`.`id`".$where);
        return $result;
    }

    public function getAppointmentByAssignUserId($user_id)
    {

        return $this->model
                    ->select(
                            'companies.id as company_id',
                            'companies.name as company_name',
                            'company_locations.name as loc_name',
                            'company_locations.address_line_1 as address_line_1',
                            'company_locations.address_line_2 as address_line_2',
                            'company_locations.zip_code as zip_code',
                            'master_states.name as state',
                            'master_countries.name as country_name',
                            'master_cities.name as city_name',
                            'appointments.id as appointment_id',
                            'appointments.inspection_number as inspection_number',
                            'appointments.created_at as created_at',
                            'appointments.inspection_date_time',
                            'appointment_classifications.option_value'
                        )
                    ->join('companies','appointments.to_company_id', '=', 'companies.id')
                    ->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id')
                    ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
                    ->join('master_countries', 'master_states.country_id', '=', 'master_countries.id')
                    ->join('master_cities', 'company_locations.city_id', '=', 'master_cities.id')
                    ->join('appointment_classifications', 'appointments.id', '=', 'appointment_classifications.appointment_id')
                    ->where('appointments.assign_to_user_id', $user_id)
                    ->where('appointments.report_status', '=', 0)
                    ->where('appointments.appointment_status', 1)
                    ->where('appointment_classifications.entity_type','AUDIT_TYPE')
                    ->orderBy('appointments.id','DESC')
                    ->get()
                    ->toArray();

    }

    /**
     * get appointment details
     *
     * @param user role
     * @param compnay type
     * 
     * @throws
     * @author CK
     * @return raw data
     * @link 
     * @since 1.0.0
     */
    public function getAppointmentForDashboard ($role, $type, $cId, $uId)
    {
        $appointments = '';
        if ($role == Config::get('simplifya.MjbMasterAdmin') && $type == Config::get('simplifya.MARIJUANA_COMPANY_TYPE'))
        {
            $appointments = $this->model->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                    ->where('to_company_id', '=', $cId)
                                    ->where('appointments.report_status', '=', 0)
                                    ->select(DB::raw('(SELECT name FROM companies where id = appointments.from_company_id) as company'), 'inspection_date_time', 'company_locations.name')
                                    ->orderBy('inspection_date_time', 'DESC')
                                    ->groupBy('appointments.id')
                                    ->get();   
        } 
        if ($role == Config::get('simplifya.MjbManager') && $type == Config::get('simplifya.MARIJUANA_COMPANY_TYPE'))
        {
            $appointments = $this->model->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                    ->join('company_users', 'company_users.location_id', '=', 'company_locations.id')
                                    ->join('users', 'users.id', '=', 'company_users.user_id')
                                    ->where('to_company_id', '=', $cId)
                                    ->select(DB::raw('(SELECT name FROM companies where id = appointments.from_company_id) as company'), 'inspection_date_time', 'company_locations.name')
                                    ->where('appointments.report_status', '=', 0)
                                    ->orderBy('inspection_date_time', 'DESC')
                                    ->groupBy('appointments.id')
                                    ->get();    
        }
        if($role == Config::get('simplifya.CcMasterAdmin') && $type == Config::get('simplifya.COMPLIANCE_COMPANY_TYPE'))
        {
            $appointments = $this->model->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                    ->where('from_company_id', '=', $cId)
                                    ->where('appointments.report_status', '=', 0)
                                    ->select(DB::raw('(SELECT name FROM companies where id = appointments.to_company_id) as company'), 'inspection_date_time', 'company_locations.name')
                                    ->orderBy('inspection_date_time', 'DESC')
                                    ->groupBy('appointments.id')
                                    ->get(); 
        }
        if($role == Config::get('simplifya.CcInspector') && $type == Config::get('simplifya.COMPLIANCE_COMPANY_TYPE'))
        {
            $appointments = $this->model->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                    ->where('from_company_id', '=', $cId)
                                    ->where('assign_to_user_id', '=', $uId)
                                    ->where('appointments.report_status', '=', 0)
                                    ->select(DB::raw('(SELECT name FROM companies where id = appointments.to_company_id) as company'), 'inspection_date_time', 'company_locations.name')
                                    ->orderBy('inspection_date_time', 'DESC')
                                    ->groupBy('appointments.id')
                                    ->get(); 
        }
        if($role == Config::get('simplifya.GeMasterAdmin') && $type == Config::get('simplifya.GOVERNMENT_ENTITY_TYPE'))
        {
            $appointments = $this->model->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                    ->where('from_company_id', '=', $cId)
                                    ->where('appointments.report_status', '=', 0)
                                    ->select(DB::raw('(SELECT name FROM companies where id = appointments.to_company_id) as company'), 'inspection_date_time', 'company_locations.name')
                                    ->orderBy('inspection_date_time', 'DESC')
                                    ->groupBy('appointments.id')
                                    ->get(); 
        }
        if($role == Config::get('simplifya.GeInspector') && $type == Config::get('simplifya.GOVERNMENT_ENTITY_TYPE'))
        {
            $appointments = $this->model->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                    ->where('from_company_id', '=', $cId)
                                    ->where('assign_to_user_id', '=', $uId)
                                    ->where('appointments.report_status', '=', 0)
                                    ->select(DB::raw('(SELECT name FROM companies where id = appointments.to_company_id) as company'), 'inspection_date_time', 'company_locations.name')
                                    ->orderBy('inspection_date_time', 'DESC')
                                    ->groupBy('appointments.id')
                                    ->get(); 
        }
        return $appointments;
        
    }

    /**
     * get appointment details
     *
     * @param user role
     * @param compnay type
     * 
     * @throws
     * @author CK
     * @return raw data
     * @link 
     * @since 1.0.0
     */
    public function getAppointmentForMobile ($role, $cType, $cId, $uId, $type)
    {
        $appointments = [];
        $pagination_size = 100;
        
        if ($type == 1){
           
            if ($role == Config::get('simplifya.MjbMasterAdmin') && $cType == Config::get('simplifya.MARIJUANA_COMPANY_TYPE')){ 
                $query = $this->model->select(
                                'companies.id as company_id',
                                'companies.name as company_name',
                                'company_locations.name as loc_name',
                                'company_locations.address_line_1 as address_line_1',
                                'company_locations.address_line_2 as address_line_2',
                                'master_states.name as state',
                                'master_countries.name as country_name',
                                'appointments.id as appointment_id',
                                'appointments.inspection_number as inspection_number',
                                'appointments.inspection_date_time',
                                'appointment_classifications.option_value',
                                'cf.name as from_company_name',
                                'master_audit_types.name as audit_type_name'
                            );
                 $query->join('companies','appointments.to_company_id', '=', 'companies.id');
                 $query->join('companies as cf','appointments.from_company_id', '=', 'cf.id');
                 $query->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id');
                 $query->join('master_countries', 'company_locations.city_id', '=', 'master_countries.id');
                 $query->join('master_states', 'company_locations.states_id', '=', 'master_states.id');
                 
                 $query->join('appointment_classifications', function($join){
                     $join->on('appointments.id', '=', 'appointment_classifications.appointment_id');
                     $join->where('appointment_classifications.entity_type', '=', 'AUDIT_TYPE');
                  });
                 $query->join('master_audit_types', 'master_audit_types.id', '=', 'appointment_classifications.option_value');
                 
                 $query->where('appointments.to_company_id', $cId);
                 $query->where('appointments.report_status', '=', 3);
                 $query->where('appointments.share_mjb', 1);
                 $query->where('appointments.appointment_status', 1);
                 $query->orderBy('appointments.id','DESC');
               $appointments = $query->paginate($pagination_size); 
             
            } 
            
            if ($role == Config::get('simplifya.MjbManager') && $cType == Config::get('simplifya.MARIJUANA_COMPANY_TYPE')){
                $query = $this->model->select(
                                'companies.id as company_id',
                                'companies.name as company_name',
                                'company_locations.name as loc_name',
                                'company_locations.address_line_1 as address_line_1',
                                'company_locations.address_line_2 as address_line_2',
                                'master_states.name as state',
                                'master_countries.name as country_name',
                                'appointments.id as appointment_id',
                                'appointments.inspection_number as inspection_number',
                                'appointments.created_at as created_at',
                                'appointment_classifications.option_value',
                                'cf.name as from_company_name',
                                'master_audit_types.name as audit_type_name',
                                'appointments.inspection_date_time'
                            );
                        $query->join('companies','appointments.to_company_id', '=', 'companies.id');
                        $query->join('companies as cf','appointments.from_company_id', '=', 'cf.id');
                        $query->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id');
                        $query->join('company_users', 'company_users.location_id', '=', 'appointments.company_location_id');
                        $query->join('users', 'users.id', '=', 'company_users.user_id');
                        $query->join('master_countries', 'company_locations.city_id', '=', 'master_countries.id');
                        $query->join('master_states', 'company_locations.states_id', '=', 'master_states.id');
                        $query->join('appointment_classifications', function($join){
                              $join->on('appointments.id', '=', 'appointment_classifications.appointment_id');
                              $join->where('appointment_classifications.entity_type', '=', 'AUDIT_TYPE');
                           });
                        $query->join('master_audit_types', 'master_audit_types.id', '=', 'appointment_classifications.option_value');
                        $query->where('company_users.user_id', $uId);
                        $query->where('appointments.report_status', '=', 3);
                        $query->where('appointments.share_mjb', 1);
                        $query->where('appointments.appointment_status', 1);
                        $query->orderBy('appointments.id','DESC');
                         $appointments = $query->paginate($pagination_size);  
                
            }
            
            if ($role == Config::get('simplifya.MjbEmployee') && $cType == Config::get('simplifya.MARIJUANA_COMPANY_TYPE')){ 
                $appointments = $this->model->select(
                                'companies.id as company_id',
                                'companies.name as company_name',
                                'company_locations.name as loc_name',
                                'company_locations.address_line_1 as address_line_1',
                                'company_locations.address_line_2 as address_line_2',
                                'master_states.name as state',
                                'master_countries.name as country_name',
                                'appointments.id as appointment_id',
                                'appointments.inspection_number as inspection_number',
                                'appointments.created_at as created_at',
                                'appointments.inspection_date_time'
                            )
                        ->join('companies','appointments.to_company_id', '=', 'companies.id')
                        ->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id')
                        ->join('appointment_action_item_users', 'appointment_action_item_users.appointment_id', '=', 'appointments.id')
                        ->join('master_countries', 'company_locations.city_id', '=', 'master_countries.id')
                        ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
                        ->where('appointment_action_item_users.user_id', $uId)
                        ->where('appointments.report_status', '=', 3)
                        ->where('appointments.share_mjb', 1)
                        ->where('appointments.appointment_status', 1)
                        ->groupBy('appointments.id')
                        ->orderBy('appointments.id','DESC')
                        ->paginate($pagination_size);    
                
            } 
            
        } elseif ($type == 2){
           
            if ($role == Config::get('simplifya.MjbMasterAdmin') && $cType == Config::get('simplifya.MARIJUANA_COMPANY_TYPE')){
                $query = $this->model->select(
                                'companies.id as company_id',
                                'companies.name as company_name',
                                'company_locations.name as loc_name',
                                'company_locations.address_line_1 as address_line_1',
                                'company_locations.address_line_2 as address_line_2',
                                'master_states.name as state',
                                'master_countries.name as country_name',
                                'appointments.id as appointment_id',
                                'appointments.inspection_number as inspection_number',
                                'appointments.created_at as created_at',
                                'appointments.inspection_date_time',
                                'cf.name as from_company_name'
                            );
                        $query->join('companies','appointments.to_company_id', '=', 'companies.id');
                        $query->join('companies as cf','appointments.from_company_id', '=', 'cf.id');
                        $query->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id');
                        $query->join('master_countries', 'company_locations.city_id', '=', 'master_countries.id');
                        $query->join('master_states', 'company_locations.states_id', '=', 'master_states.id');
                        $query->where('appointments.to_company_id', $cId);
                        $query->where('appointments.report_status', '=', 0);
                        $query->where('appointments.appointment_status', 1);
                        $query->orderBy('appointments.id','DESC');
                        $appointments = $query->paginate($pagination_size);             
            } 
            
            if ($role == Config::get('simplifya.MjbManager') && $cType == Config::get('simplifya.MARIJUANA_COMPANY_TYPE')){
                $query = $this->model->select(
                                'companies.id as company_id',
                                'companies.name as company_name',
                                'company_locations.name as loc_name',
                                'company_locations.address_line_1 as address_line_1',
                                'company_locations.address_line_2 as address_line_2',
                                'master_states.name as state',
                                'master_countries.name as country_name',
                                'appointments.id as appointment_id',
                                'appointments.inspection_number as inspection_number',
                                'appointments.created_at as created_at',
                                'appointments.inspection_date_time',
                                'cf.name as from_company_name'
                            );
                        $query->join('companies','appointments.to_company_id', '=', 'companies.id');
                        $query->join('companies as cf','appointments.from_company_id', '=', 'cf.id');
                        $query->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id');
                        $query->join('company_users', 'company_users.location_id', '=', 'appointments.company_location_id');
                        $query->join('users', 'users.id', '=', 'company_users.user_id');
                        $query->join('master_countries', 'company_locations.city_id', '=', 'master_countries.id');
                        $query->join('master_states', 'company_locations.states_id', '=', 'master_states.id');
                        $query->where('company_users.user_id', $uId);
                        $query->where('appointments.report_status', '=', 0);
                        $query->where('appointments.appointment_status', 1);
                        $query->orderBy('appointments.id','DESC');
                        $appointments = $query->paginate($pagination_size);                            
            }
            
            if ($role == Config::get('simplifya.MjbEmployee') && $cType == Config::get('simplifya.MARIJUANA_COMPANY_TYPE')){ 
                $appointments = $this->model->select(
                                'companies.id as company_id',
                                'companies.name as company_name',
                                'company_locations.name as loc_name',
                                'company_locations.address_line_1 as address_line_1',
                                'company_locations.address_line_2 as address_line_2',
                                'master_states.name as state',
                                'master_countries.name as country_name',
                                'appointments.id as appointment_id',
                                'appointments.inspection_number as inspection_number',
                                'appointments.inspection_date_time',
                                'appointments.created_at as created_at'
                            )
                        ->join('companies','appointments.to_company_id', '=', 'companies.id')
                        ->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id')
                        ->join('appointment_action_item_users', 'appointment_action_item_users.appointment_id', '=', 'appointments.id')
                        ->join('master_countries', 'company_locations.city_id', '=', 'master_countries.id')
                        ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
                        ->where('appointment_action_item_users.user_id', $uId)
                        ->where('appointments.report_status', '=', 0)
                        ->where('appointments.appointment_status', 1)
                        ->orderBy('appointments.id','DESC')
                        ->paginate($pagination_size);                            
            }
        }
        
        return $appointments;
        
    }

    //get related user for the comment
    public function getNotifiedUsers ($aId)
    {
        $users = [];
        $result = $this->model->join('company_users', 'company_users.location_id', '=', 'appointments.company_location_id')
                              ->join('users', 'users.id', '=', 'company_users.user_id')
                              ->where('appointments.id', '=', $aId)
                              ->where('users.master_user_group_id', '=', Config::get('simplifya.MjbManager'))
                              ->select('users.id')
                              ->get();

        $masterUsers = DB::table('appointments')->join('users', 'users.company_id', '=', 'appointments.to_company_id')
                                                ->where('appointments.id', '=', $aId)
                                                ->where('users.master_user_group_id', '=', Config::get('simplifya.MjbMasterAdmin'))
                                                ->select('users.id')
                                                ->get();
        if (count ($result) > 0)
        {
            foreach ($result as $key => $value) 
            {
                $users[] = $value->id; 
            }
        }
        if (count ($masterUsers) > 0)
        {
            foreach ($masterUsers as $key => $masterUser) 
            {
                $users[] = $masterUser->id; 
            }   
        }                                                                         
        return array_unique($users);                          
    }
    
    /**
     * Save inspection data
     * @param type $dataset
     * @param type $appointment_id
     * @return type
     */
    public function saveInspection($dataset, $appointment_id){
       return $status = DB::table('appointments')->where('id', '=', $appointment_id)->update($dataset);
    }
    
    /**
     * Get report status
     * @param type $appointment_id
     * @return type
     */
    public function getReportStatus($appointment_id){
       return $status = DB::table('appointments')->where('id', '=', $appointment_id)->get();
    }

    public function getAppointmentByEntityType($entity_type, $pagination_size)
    {
        if($entity_type == Config::get('simplifya.MjbManager')) {
            $query = $this->model->select(
                'companies.id as company_id',
                'companies.name as to_company_name',
                'master_countries.name as country_name',
                'appointments.id as appointment_id',
                'appointments.inspection_number as inspection_number',
                'appointments.created_at as created_at',
                'appointment_classifications.option_value',
                'fromCompany.name as from_company_name',
                'master_audit_types.name as audit_type_name'
            );
            $query->join('companies','appointments.to_company_id', '=', 'companies.id');
            $query->join('companies as fromCompany','appointments.from_company_id', '=', 'fromCompany.id');
            $query->join('appointment_classifications', 'appointments.id', '=', 'appointment_classifications.appointment_id');
            $query->where('appointment_classifications.entity_type', 'AUDIT_TYPE');
            $query->where('appointments.appointment_status', '=', 1);
            $query->orderBy('appointments.id','DESC');
            $appointments = $query->paginate($pagination_size);
        }

    }

    /**
     * Get Appointment notified users
     * @param $appointment_id
     * @return array
     */
    public function getAppointmentNotifiedUsers($appointment_id){
        $users_list = [];
        // Get admin users
        $admin_users = DB::table('appointments')
                        ->join('users', function ($q){
                            $q->on('users.company_id', '=', 'appointments.to_company_id')
                                ->where('users.master_user_group_id', '=', 2);
                        })
                        ->where('appointments.id', '=', $appointment_id)
                        ->lists('users.id');

        $users_list = array_merge($users_list, $admin_users);

        // Get managers
        $managers = DB::table('appointments')
                    ->join('company_users', 'company_users.location_id', '=', 'appointments.company_location_id')

                ->join('users', function ($q){
                    $q->on('users.id', '=', 'company_users.user_id')
                        ->where('users.master_user_group_id', '=', 3);
                })
                ->where('appointments.id', '=', $appointment_id)
                ->lists('users.id');

        $users_list = array_merge($users_list, $managers);

        return array_values(array_unique($users_list));

    }

    /**
     * Get action item details
     * @param $action_item_id
     * @return mixed
     */
    public function getActionItemDetails($action_item_id){
        $result = DB::table('question_action_items')
                ->where('id', '=', $action_item_id)
                ->get();
        return $result;
    }

    function getCompanyInfo($appointment_id)
    {
        $temp = "company";
        return $this->model->join('companies','appointments.from_company_id', '=', 'companies.id')
            ->join('companies AS c2','appointments.to_company_id', '=', 'c2.id')
            ->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id')
            ->leftJoin('images', function($join) use($temp)
            {
                $join->on('appointments.from_company_id', '=', 'images.entity_id');
                $join->where('images.entity_tag', '=', $temp);
            })
            ->leftJoin('appointment_classifications', function($join)
            {
                $join->on('appointment_classifications.appointment_id', '=', 'appointments.id');
                $join->where('appointment_classifications.entity_type', '=', 'LICENCE');
            })
            ->leftJoin('master_licenses', 'appointment_classifications.option_value', '=', 'master_licenses.id')
            //->leftJoin('company_location_licenses', 'company_location_licenses.location_id', '=', 'appointments.company_location_id')
            ->leftJoin('company_location_licenses', function($join)
            {
                $join->on('company_location_licenses.location_id', '=', 'appointments.company_location_id');
                $join->on('company_location_licenses.license_id', '=', 'master_licenses.id');
            })
            ->leftJoin('master_cities', 'master_cities.id', '=', 'company_locations.city_id')
            ->leftJoin('master_states', 'master_states.id', '=', 'company_locations.states_id')
            //->join('images', 'appointments.from_company_id', '=', 'images.entity_id')
            ->where('appointments.id', '=', $appointment_id)
            //->where('images.entity_tag', '=', 'company')
            ->select('companies.name as from_company_name',
                'c2.name as to_company_name',
                'company_locations.name as location',
                'appointments.id as appointment_id',
                'appointments.inspection_date_time as inspection_date_time',
                'images.name as image_name',
                'appointments.inspection_number as inspection_number',
                'company_locations.address_line_1',
                'company_locations.address_line_2',
                'master_licenses.name as license_name',
                'company_location_licenses.license_number',
                'master_cities.name as city',
                'master_states.name as state',
                'company_locations.zip_code'
            )
            ->get();
    }

    function getAllAppointmentByStatus($current_time, $after2day_time)
    {
        return $this->model
                    ->leftJoin('email_notification_logs', function($join)
                    {
                        $join->on('appointments.id', '=', 'email_notification_logs.company_id');
                        $join->where('email_notification_logs.notification_type', '=', 3);
                    })
                    ->join('company_locations', 'appointments.company_location_id', '=', 'company_locations.id')
                    ->join('master_cities', 'company_locations.city_id', '=', 'master_cities.id')
                    ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
                    ->join('companies','appointments.to_company_id', '=', 'companies.id')
                    ->join('users','appointments.assign_to_user_id', '=', 'users.id')
                    ->where('appointment_status', 1)
                    ->where('report_status', 0)
                    ->where('inspection_date_time', $after2day_time)
                    //->whereBetween('inspection_date_time', array($current_time, $after2day_time))
                    ->select('appointments.*','email_notification_logs.company_id',
                        'company_locations.name as location_name',
                        'company_locations.address_line_1 as address_line_1',
                        'company_locations.address_line_2 as address_line_2',
                        'company_locations.zip_code as zip_code',
                        'users.name as user_name',
                        'users.email as email',
                        'companies.name as to_company_name',
                        'master_cities.name as city',
                        'master_states.name as state'
                    )
                    ->get();
    }
}