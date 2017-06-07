<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/16/2016
 * Time: 12:12 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;
use DB;

class CompanyLocationRepository extends Repository
{
    public function model()
    {
        return 'App\Models\CompanyLocation';
    }

    /**
     * get company locations by company ids
     * @param $id
     * @return mixed
     */
    public function getLocationByCompanyId($id)
    {
        return $this->model
                ->select('company_locations.*', 'master_states.name as state_name', 'master_cities.name as city_name', 'master_countries.name as country_name')
                ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
                ->join('master_countries', 'master_states.country_id', '=', 'master_countries.id')
                ->join('master_cities', 'company_locations.city_id', '=', 'master_cities.id')
                ->where('company_locations.company_id', $id)
                ->where('company_locations.status', '!=' ,0)
                ->get();
    }

    /**
     * Get all lixenses
     * @return mixed
     */
    public function getLicense()
    {
        return $this->model;
    }

    /**
     * Get company locations by company ids
     * @param $id
     * @return mixed
     */
    public function getCompanyLocationById($id)
    {
        return $this->model
            ->where('company_id', $id)
            ->where('status', 1)
            ->get();
    }

    /**
     * Get license by location ids
     * @param $location_id
     * @return mixed
     */
    public function getLicenseTypes($location_id)
    {
       return $this->model
            ->select('master_licenses.name as license_name', 'master_licenses.id as id')
            ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
            ->join('master_licenses', 'master_licenses.master_states_id', '=', 'master_states.id')
            ->where('master_licenses.status','=', 1)
            ->where('company_locations.id', '=', $location_id)
            ->get();
    }

    /**
     * get business locations
     * @param $location_id
     * @return array
     */
    public function getBusinessLocation($location_id)
    {
        $location = $this->model
                    ->select('master_countries.id as country_id',
                        'master_states.id as states_id',
                        'company_locations.id as location_id',
                        'company_locations.name as location_name',
                        'company_locations.address_line_1',
                        'company_locations.address_line_2',
                        'company_locations.phone_number',
                        'company_locations.city_id',
                        'company_locations.zip_code',
                        'company_locations.states_id'
                    )
                    ->join('master_states', 'company_locations.states_id', '=', 'master_states.id')
                    ->join('master_countries', 'master_states.country_id', '=', 'master_countries.id')
                    ->where('company_locations.id', '=', $location_id)
                    ->get();
        $states = DB::select('SELECT master_states.id, master_states.name FROM `master_states` WHERE master_states.country_id='.$location[0]['country_id']);
        $cities = DB::select('SELECT master_cities.id, master_cities.name FROM `master_cities` WHERE master_cities.status_id='.$location[0]['states_id'].' ORDER BY master_cities.name ASC');
        $data = array('location' => $location, 'states' => $states, 'cities' => $cities);
        return $data;
    }

    /**
     * Change business location statuses
     * @param $location_id
     * @param $status
     * @return mixed
     */
    public function changeBusinessLocationStatus($location_id, $status)
    {
        return $this->model
                    ->where('id', '=', $location_id)
                    ->update(['status' => $status]);
    }

    /**
     * Update company location
     * @param $data
     * @param $location_id
     * @return mixed
     */
    public function updateCompanyLocation($data, $location_id)
    {
        return $this->model
                    ->where('id', '=', $location_id)
                    ->update($data);
    }

    /**
     * Check users in locations
     * @param $location_id
     * @return mixed
     */
    public function checkUserInLocation($location_id)
    {
        return DB::table('company_users')
                    ->join('users', function($join){
                        $join->on('users.id', '=', 'company_users.user_id')
                            ->where('users.status','!=', 0);
                    })
                    ->where('company_users.location_id', $location_id)
                    ->get();
    }

    public function getLocationLicensesByLocationID()
    {
        
    }

    /**
     * get location by location id
     * @param $location_id
     * @return mixed
     */
    public function getLocationByID($location_id)
    {
        return $this->model
                ->with('masterCity','masterStates', 'masterStates.masterCountry')
                ->where('id', $location_id)
                ->first();
    }

    /**
     * Get company locations
     * @param $business_name
     * @param $entity_type
     * @param $country
     * @param $state
     * @param $city
     * @return mixed
     */
    public function getCompanyLocations($business_name, $entity_type, $country, $state, $city)
    {
        $query = DB::table('company_locations');
        $query->leftJoin('companies as loc_company', 'company_locations.company_id', '=', 'loc_company.id');
        $query->leftJoin('master_states', 'company_locations.states_id', '=', 'master_states.id');
        $query->leftJoin('master_cities', 'company_locations.city_id', '=', 'master_cities.id');

        if($business_name != ""){
            $query->where('loc_company.name', 'like', '%' . $business_name . '%' );
        }
        if($state != ""){
            $query->where('master_states.id', '=', $state);
        }
        if($country != ""){
            $query->where('master_states.country_id', '=', $country);
        }
        if($city != ""){
            $query->where('master_cities.id', '=', $city);
        }
        $query->where('company_locations.status', '=', 1);
        $query->select(
            'master_states.name as state_name',
            'master_states.country_id as country_id',
            'master_cities.name as city_name',
            'company_locations.name as location_name',
            'company_locations.phone_number',
            'loc_company.name as company_name',
            'loc_company.entity_type as entity_type'
        );
        $results = $query->get();
        dd($results);
        return $results;
    }
}