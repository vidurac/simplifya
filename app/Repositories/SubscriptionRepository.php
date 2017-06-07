<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository extends Repository{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\MasterSubscription';
    }

    /**
     * insert new subscription
     * @param $dataset
     * @return mixed
     */
    public function insert($dataset)
    {
        return $this->model->insert($dataset);
    }

    /**
     * get subscription entry details by Id
     * @param $subscription_id
     */
    public function getSubscriptionRequestById($subscription_id)
    {
        return $this->model->where('id', $subscription_id)->get();
    }

    /**
     * get all subscriptions from database
     * @param $sql
     * @return mixed
     */
    public function getSubscriptionRequests($sql)
    {
        $result = DB::select($sql);
        return $result;
    }

    /**
     * get total number of entry count
     * @param $type
     * @return mixed
     */
    public static function getTotaleNumber($type)
    {
        $state = "";
        if($type=="cc_ge"){
            $state = ' (`entity_type_id` = 3 OR  `entity_type_id` = 4) ';
        }elseif($type=="mjb"){
            $state = ' (`entity_type_id` = 2) ';
        }
        $result = DB::select("SELECT COUNT(`id`) as count FROM `master_subscriptions` WHERE ". $state ." AND `status` = 1");
        return $result;
    }

    /**
     * get current row count
     * @return mixed
     */
    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

    /**
     * Remove subscription from subscription Id
     * @param $id
     * @return mixed
     */
    public function remove_subscription($id)
    {
        return $this->model
            ->where('id', $id)
            ->update(array('status' => 2));

    }

    /**
     *
     * get all subscription plans relevant
     *
     * @param $company_type
     * @param $validity_period
     * @return mixed
     */
    public function getSubscriptionPlans($company_type,$validity_period)
    {
        $subs_plans = DB::select("SELECT * FROM `master_subscriptions` WHERE  entity_type_id =". $company_type ." AND `status` = 1 AND validity_period_id=".$validity_period);
        return $subs_plans;
    }

    /**
     * Returns minimum subscription plan amount
     * @param $id
     * @return mixed
     */
    public function getMinimumSubscriptionAmountByEntityType($id) {
        $plan = $this->model->where('entity_type_id', $id)->orderBy('amount')->first();
        return $plan;
    }


}