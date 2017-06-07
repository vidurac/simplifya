<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/18/2016
 * Time: 3:00 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;
use DB;

class LicenseLocationRepository extends Repository
{
    public function model()
    {
        return 'App\Models\CompanyLocationLicense';
    }

    public function getLicenseLocation($company_id)
    {
        return $this->model
                    //->with('companyLocation', 'masterLicense')
                    ->select('company_locations.name as company_loc_name',
                        'master_licenses.name as master_license_name',
                        'company_location_licenses.license_number',
                        'company_location_licenses.status as license_status',
                        'company_location_licenses.id as license_id'
                    )
                    ->join('company_locations', 'company_location_licenses.location_id', '=', 'company_locations.id')
                    ->join('master_licenses', 'company_location_licenses.license_id', '=', 'master_licenses.id')
                    ->where('company_location_licenses.status','!=', 0)
                    ->where('company_location_licenses.company_id', '=', $company_id)
                    ->get();
    }

    public function getLicenseDetailsById($id)
    {
        return $this->model
                    ->select('company_locations.name as company_loc_name',
                        'company_locations.id as company_loc_id',
                        'company_locations.states_id as states_id',
                        'master_licenses.name as master_license_name',
                        'master_licenses.id as master_license_id',
                        'company_location_licenses.name as dba_name',
                        'company_location_licenses.license_date as license_date',
                        'company_location_licenses.renewal_date as renewal_date',
                        'company_location_licenses.license_number',
                        'company_location_licenses.status as license_status',
                        'company_location_licenses.id as license_id'
                    )
                    ->join('master_licenses', 'company_location_licenses.license_id', '=', 'master_licenses.id')
                    ->join('company_locations', 'company_location_licenses.location_id', '=', 'company_locations.id')
                    ->where('company_location_licenses.status','!=', 0)
                    ->where('company_location_licenses.id', $id)
                    ->get();
    }

    public function getMasterLicense($state_id)
    {
        return DB::select('SELECT id, name FROM `master_licenses` WHERE master_states_id ='.$state_id);
    }

    public function updateLocationLicense($location_license_id, $company_id, $location_id, $data)
    {
        return $this->model
                    ->where('license_id', $location_license_id)
                    ->where('company_id', $company_id)
                    ->where('location_id', $location_id)
                    ->update($data);
    }

    public function changeLicenseLocationStatus($license_id, $status)
    {
        return $this->model
                    ->where('id', '=', $license_id)
                    ->update(['status' => $status]);
    }

    public function isExistLocationLicense($company_id, $license_id, $location_id)
    {
        return $this->model
                    ->where('company_id', $company_id)
                    ->where('license_id', $license_id)
                    ->where('location_id', $location_id)
                    ->first();
    }

    public function getAllLicenses($locationId, $companyId)
    {
        return $this->model
                    ->with('masterLicense')
                    ->where('company_location_licenses.location_id', $locationId)
                    ->where('company_location_licenses.company_id', $companyId)
                    ->where('status', 1)
                    ->orderBy('company_location_licenses.license_id', 'asc')
                    ->get();
    }

    public function checkLicenseInLocation($company_id)
    {
        return $this->model
                ->where('location_id', $company_id)
                ->where('status','!=', 0)
                ->get();
    }

    public function getLicenseAmount($license_id, $type)
    {
        if($type == 1){
            $result = DB::table('master_licenses')
                ->select('master_licenses.name', 'master_licenses.id', 'master_licenses.checklist_fee_inhouse')
                ->whereIn('master_licenses.id', $license_id)
                ->get();

            return $result;
        }
        else{
            $result = DB::table('master_licenses')
                ->select('master_licenses.name', 'master_licenses.id', 'master_licenses.checklist_fee')
                ->whereIn('master_licenses.id', $license_id)
                ->get();

            return $result;
        }

    }

    public function getAllLicensesByCompanyId($company_id)
    {
        return DB::table('company_location_licenses')
                    ->select('company_location_licenses.id as license_id',
                        'master_licenses.name as name',
                        'company_location_licenses.license_number as license_number',
                        'company_location_licenses.license_date',
                        'company_location_licenses.renewal_date',
                        'company_location_licenses.status', 'company_locations.name as location_name'
                    )
                    ->join('master_licenses', 'company_location_licenses.license_id', '=', 'master_licenses.id')
                    ->join('company_locations', 'company_location_licenses.location_id', '=', 'company_locations.id')
                    ->where('company_location_licenses.company_id', $company_id)
                    ->where('company_location_licenses.status', '!=', 0)
                    ->get();
    }


    public function searchLicense($company_id, $license_number, $license_type, $license_location)
    {
        $query = $this->model->where('company_location_licenses.company_id', $company_id);
        if ($license_type != '') {
            $query->where('company_location_licenses.license_id', $license_type);
        }
        if ($license_location != '') {
            $query->where('company_location_licenses.location_id', $license_location);
        }
        if ($license_number != '') {
            $query->where('license_number', 'LIKE', '%' . $license_number . '%');
        }
        $query->join('master_licenses', 'company_location_licenses.license_id', '=', 'master_licenses.id');
        $query->select('company_location_licenses.id as license_id',
            'master_licenses.name as name',
            'company_location_licenses.license_number as license_number',
            'company_location_licenses.license_date',
            'company_location_licenses.renewal_date',
            'company_location_licenses.status'
        );
        $result = $query->get();
        return $result;
    }
    public function getLicenseList ($cId)
    {
        $licenses = $this->model->join('company_locations', 'company_locations.id', '=', 'company_location_licenses.location_id')
                                ->join('companies', 'companies.id', '=', 'company_location_licenses.company_id')
                                ->join('master_licenses', 'master_licenses.id', '=', 'company_location_licenses.license_id')
                                ->where('company_location_licenses.company_id', '=', $cId)
                                ->select('companies.name as company', 'company_location_licenses.license_number','company_location_licenses.license_date', 'company_location_licenses.renewal_date', 'company_locations.name as location', 'master_licenses.name as license')
                                ->get();
        \Log::info('=====SS=====');
        \Log::info(print_r(json_encode($licenses),true));
        \Log::info('=====EE=====');

        $array = [];                        
        if (count ($licenses) > 0)
        {
            foreach ($licenses as $key => $license) 
            {
                $date1 = date_create(date('Y-m-d', time()));
                $date2 = date_create(date('Y-m-d', strtotime($license->renewal_date)));
                $diff  = date_diff($date1,$date2);

                $curdate = strtotime(date('Y-m-d', time()));
                $mydate  = strtotime(date('Y-m-d', strtotime($license->renewal_date)));
                \Log::info('====MY Date==='.$license->renewal_date.'/'.$mydate);

                $days =  $diff->days;
                \Log::info('==== Diff ==='.$days);

                if ($curdate > $mydate)
                {
                    $days = -1;    
                }

                if ($mydate == 0) {
                    $days = 0;
                }

                if ($license->renewal_date=='0000-00-00')
                {
                    $days = -2;
                }


                $array[$key]['license']         = $license->license;
                $array[$key]['license_number']  = $license->license_number;
                $array[$key]['location']        = $license->location;
                $array[$key]['remaining']       = $days;
            }
            
        }                        
        return $array;                        

    }

    public function updateLicenseById($payment_id, $license_id, $location_id, $user_id)
    {
        return $this->model
                    ->where('license_id', $license_id)
                    ->where('location_id', $location_id)
                    ->update(['payment_id' => $payment_id, 'status' => 1, 'updated_by' => $user_id]);

    }

    public function updateDeletedLicenseById($license_id, $location_id, $user_id, $data)
    {
        return $this->model
            ->where('license_id', $license_id)
            ->where('location_id', $location_id)
            ->update($data);
    }

    public function getActiveLicense($company_id)
    {
        return DB::table('company_location_licenses')
            ->where('company_location_licenses.company_id', $company_id)
            ->where('company_location_licenses.status', '=', 1)
            ->count();
    }
}