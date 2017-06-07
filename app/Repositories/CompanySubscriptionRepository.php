<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/26/2016
 * Time: 10:31 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class CompanySubscriptionRepository extends Repository
{
    public function model()
    {
        return 'App\Models\CompanySubscription';
    }

    public function getPaymentDetail($company_id)
    {
        return $this->model
                    ->where('company_id', $company_id)
                    ->get();
    }
}