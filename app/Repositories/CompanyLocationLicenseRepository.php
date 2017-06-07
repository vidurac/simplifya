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

class CompanyLocationLicenseRepository extends Repository
{
    public function model()
    {
        return 'App\Models\CompanyLocationLicense';
    }

    public function getActiveLicenseCountByCompanyId($id)
    {
        return $this->model
            ->where('company_id', '=', $id)
            ->where('status', '=', 1)
            ->get()
            ->count();
    }

    /**
     * Returns all license including inactive once
     * @param $company_id
     * @return mixed
     * @internal param $id
     */
    public function getActiveInactiveLicenseCountByCompanyId($company_id)
    {
        return $this->model
            ->where('company_id', '=', $company_id)
//            ->whereIn('status', array(1, 0)) // both deleted and active license
            ->get()
            ->count();
    }
}