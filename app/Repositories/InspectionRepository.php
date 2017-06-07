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

class InspectionRepository extends Repository
{
    public function model()
    {
        return 'App\Models\request';
    }

    /**
     * @param $company_id
     * @return mixed
     * get complience company manager by company Id
     */
    public function sendInspectionRequest($company_id)
    {
        return DB::table('users')
            ->where('company_id', $company_id)
            ->where('master_user_group_id', 5)
            ->get();
    }

    public function createInspectionRequest($dataset)
    {
        return $this->model->create($dataset);
    }

    /**
     * Get all request details
     */
    public function getRequestDetails($req_id)
    {
        return $this->model->with('complianceCompany', 'marijuanaCompany', 'companyLocation')->where('id', $req_id)->get();
    }

    public function getRequestMJBCompanies($req_id)
    {
        return $this->model->with('marijuanaCompany', 'companyLocation')->where('id', $req_id)->get();
    }

    public function manageRequest($req_id, $status)
    {
        return $this->model->where('id', $req_id)->update(['status' => $status]);
    }
}