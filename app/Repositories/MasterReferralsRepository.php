<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/5/2016
 * Time: 4:06 PM
 */

namespace App\Repositories;
use App\Models\CouponDetails;
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Mockery\CountValidator\Exception;


class MasterReferralsRepository extends Repository
{
    public function model() {
        return 'App\Models\MasterReferral';
    }

    /**
     * Save or edit referrals
     * @param array $data
     * @throws \Exception
     */
    public function saveOrEdit(array $data) {
        try {

            \DB::beginTransaction();
            $r =$this->model->firstOrNew(array('id'=>$data['id']));
            $r->name = $data['name'];
            $r->email = $data['email'];
            $r->commission_rates = $data['commission_rates'];
            $r->type = $data['type'];
            // Save coupon data
            $r->save();


            \DB::commit();
        }catch (Exception $e) {
            \DB::rollback();
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }

    public function deleteReferral($id)
    {
        return $this->model
            ->where('id', $id)
            ->update(array('status' => 0));
    }


    // get all pending commissions
    public function getAllCommissions()
    {
        return $this->model
            ->select('master_referrals.id','master_referrals.name',\DB::raw('sum(company_subscriptions.referral_commission) as referral_commission'),'master_subscriptions.name as subscription_name')
            ->join('coupons', 'master_referrals.id', '=', 'coupons.master_referral_id')
            ->join('company_subscription_plans', 'company_subscription_plans.coupon_referral_id', '=', 'coupons.id')
            ->join('company_subscriptions', 'company_subscriptions.company_subscription_plan_id', '=', 'company_subscription_plans.id')
            ->leftJoin('master_subscriptions', 'company_subscription_plans.master_subscription_id', '=', 'master_subscriptions.id')
            ->where('company_subscriptions.referral_payment_id', '=', '0')
            ->where('master_referrals.status','=',1)
            ->groupBy('master_referrals.id')
            ->get();
    }


    /**
     * Returns referrer specific details by id
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getReferrerById($id){
        $referrer = $this->model->select(
                array(
                    'master_referrals.*',
                )
            )->where('master_referrals.id', $id)->first();

        if (isset($referrer)) {
            $referrerCodeDetails = $referrer->coupons()->get();
            return array('referrer' => $referrer, 'referral_code_details' => $referrerCodeDetails);
        }else {
            throw new \Exception('No referrer found');
        }
    }
    /**
     * get All Referrals
     * @param array $data
     * @throws \Exception
     */
    public function getAllReferrals(){

        $results = DB::table('master_referrals')
            ->select('master_referrals.*')
            ->where('status','=',1)
            ->get();
        //\Log::debug("==== .......rr111".print_r($results,true));
        return $results;
    }

    /**
     * get Referral Commission
     * @param $coupon_id
     * @param $subscribe_plan_id
     * @param $subscrib_fee
     * @return float|int
     * @internal param array $data
     */
    public function getReferralCommission($coupon_id,$subscribe_plan_id,$subscrib_fee)
    {
        $results = $this->model->join('coupons', 'master_referrals.id', '=', 'coupons.master_referral_id')
                    ->where('coupons.id', $coupon_id)
                    ->where('master_referrals.status','=',1)
                    ->select('master_referrals.commission_rates')
                    ->get();

        if ($results->count()) {
            $commission_rates = isset($results[0]->commission_rates) ? json_decode($results[0]->commission_rates) : array();
        }else {
            $commission_rates = [];
        }
        $commision = 0;

        \Log::debug("==== subscribe_plan_id "  . $subscribe_plan_id);
        \Log::debug("==== commission rates");
        \Log::debug(print_r($commission_rates, true));
        foreach($commission_rates as $commission_rate)
        {
            if($commission_rate->id == $subscribe_plan_id)
            {
                if($commission_rate->type == "percentage")
                {
                    $commision = $subscrib_fee * $commission_rate->amount / 100;
                }
                if($commission_rate->type == "fixed")
                {
                    $commision = $commission_rate->amount;
                }
                break;
            }
            \Log::debug("===== ref commision.... " . print_r($commission_rate->id,true)." plan=".$subscribe_plan_id );
        }
        //\Log::debug("==== .......rr111".print_r($results,true));
        return $commision;
    }
}