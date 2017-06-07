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

class CompanyLocationLicensesApplicabilityRepository extends Repository
{
    public function model()
    {
        return 'App\Models\CompanyLocationLicensesApplicability';
    }

}