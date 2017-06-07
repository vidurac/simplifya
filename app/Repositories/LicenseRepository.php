<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/18/2016
 * Time: 12:39 PM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;

class LicenseRepository  extends Repository
{
    public function model()
    {
        return 'App\Models\MasterLicense';
    }

    public function getLicenseTypes($location_id)
    {
        $this->model
            ->with('CompanyLocation.masterStates')
            ->where()
            ->get();
    }

    public function getAllLicenseTypes()
    {
        return $this->model
                    ->get();
    }

    public function changeLicenseStatus($license_id, $status)
    {
        return $this->model
                    ->where('id', $license_id)
                    ->update(array('status' => $status));
    }

    public function licenseDetailsById($license_id)
    {
        return $this->model
                    ->select('master_licenses.name as license_name',
                        'master_licenses.id as license_id',
                        'master_licenses.type as type',
                        'master_licenses.checklist_fee as checklist_fee',
                        'master_countries.id as country_id',
                        'master_countries.name as country_name',
                        'master_states.id as state_id',
                        'master_states.name as state_name'
                    )
                    ->join('master_states', 'master_licenses.master_states_id', '=', 'master_states.id')
                    ->join('master_countries', 'master_states.country_id', '=', 'master_countries.id')
                    ->where('master_licenses.id', $license_id)
                    ->get();
    }

    public function changeLicenseType($license_id, $data)
    {
        return $this->model
                    ->where('id', $license_id)
                    ->update($data);
    }

    public function getLicenseLocations($license_id, $company_id)
    {
        return $this->model
                ->select('company_locations.id as location_id', 'company_locations.name as location_name')
                ->join('company_locations', 'master_licenses.master_states_id', '=', 'company_locations.states_id')
                ->where('company_locations.company_id', $company_id)
                ->where('company_locations.status','=', 1)
                ->where('master_licenses.id', $license_id)
                ->get();
    }
}