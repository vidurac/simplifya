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


class CouponsRepository extends Repository
{
    public function model() {
        return 'App\Models\Coupon';
    }

    /**
     * validate coupon code
     * @param type $coupon_code
     * @return type boolean
     */
    public function validateCoupon($coupon_code,$subscription_plan){

        $current_date = date('Y-m-d');
        //check coupon is available in coupon table for selected subscription plan
        $is_coupon_valid = DB::table('coupons')
                            ->select('coupons.id')
                            //->where('coupons.code', '=', $coupon_code)
                            ->where('coupons.code', $coupon_code)
                            ->where('coupons.master_subscription_id', '=', $subscription_plan)
                            ->where('start_date', '<=', $current_date)
                            ->where('end_date', '>=', $current_date)
                            ->count();

        if($is_coupon_valid > 0)
        {
            //check coupon is already used or not
            $results = DB::table('company_subscription_plans')
                ->select('company_subscription_plans.id')
                ->join('coupons', 'company_subscription_plans.coupon_id', '=', 'coupons.id')
                //->where('coupons.code', '=', $coupon_code)
                ->where('coupons.code', $coupon_code)
                ->count();

            if($results > 0)
            {
                return array('success' => false, 'msg' => "already in use");
            }
            else
            {
                return array('success' => true, 'msg' => "valid.");
            }

        }
        else
        {
            return array('success' => false, 'msg' => "The code entered is either invalid or has expired");
        }

    }

    public function getAllCoupons(){

        $results = DB::table('coupons')
            ->select('coupons.*')
            ->get();
        //\Log::debug("==== .......rr111".print_r($results,true));
        return $results;
    }

    public function getCouponId($coupon_code){

        $results = DB::table('coupons')
            ->select('coupons.id as coupon_id')
            ->where('coupons.code', '=', $coupon_code)
            ->first();
        //\Log::debug("==== .......rr111".print_r($results,true));
        return $results;
    }

    public function getCouponsForReferral($referral_id){

        $results = $this->model->select('coupons.id as coupon_id')
                    ->where('coupons.master_referral_id', '=', $referral_id)
                    ->count();
        //\Log::debug("==== .......rr111".print_r($results,true));
        return $results;
    }

    /**
     * Get discounts
     * @param type $coupon_code
     * @return type
     */
    public function getDiscount($coupon_code,$subscription_plan){

        $results = DB::table('coupons')
            ->select('coupons.id as coupon_id','coupon_details.amount', 'coupon_details.type', 'coupon_details.order')
            ->join('coupon_details', 'coupon_details.coupon_id', '=', 'coupons.id')
            ->where('coupons.code', '=', $coupon_code)
            ->where('coupons.master_subscription_id', '=', $subscription_plan)
            ->get();
        //\Log::debug("==== .......rr111".print_r($results,true));
        return $results;
    }

    /**
     * Get discounts by Id
     * @param type $coupon_id
     * @return type
     */
    public function getDiscountById($coupon_id,$subscription_plan,$is_referral=0){

        $results = "";

        if($is_referral == 0)
        {
            $results = DB::table('coupons')
                ->select('coupons.id as coupon_id','coupon_details.amount', 'coupon_details.type', 'coupon_details.order')
                ->join('coupon_details', 'coupon_details.coupon_id', '=', 'coupons.id')
                ->where('coupons.id', '=', $coupon_id)
                ->where('coupons.master_subscription_id', '=', $subscription_plan)
                ->get();
        }

        if($is_referral == 1)
        {
            $results = DB::table('coupons')
                ->select('coupons.id as coupon_id','coupon_details.amount', 'coupon_details.type', 'coupon_details.order')
                ->join('coupon_details', 'coupon_details.coupon_id', '=', 'coupons.id')
                ->where('coupons.id', '=', $coupon_id)
                ->get();
        }

        //\Log::debug("==== .......rr111".print_r($results,true));
        return $results;
    }

    public function getDiscountByOrder($coupon_details, $sub_fee, $order=1,$no_of_license=1)
    {
        $discount = 0;
        $coupon_id = "";
        foreach($coupon_details as $coupon_detail)
        {
            //\Log::debug("==== .......discount".print_r($coupon_detail->order." ".$order,true));
            if($coupon_detail->order == $order)
            {
                if($coupon_detail->type == "percentage")
                {
                    $discount = $sub_fee * ($coupon_detail->amount) / 100;
                }
                if($coupon_detail->type == "fixed")
                {
                    $discount = $coupon_detail->amount;
                    $discount = $discount * $no_of_license;
                }
                $coupon_id = $coupon_detail->coupon_id;
                break;
            }
        }
        return array('discount' => $discount, 'coupon_id' => $coupon_id);
    }


    public function getDiscountAmount($subscribe_plan,$subscrib_fee,$coupon_id,$is_referral=0, $no_of_license=1,$order=1)
    {

        $subscription_plan = $subscribe_plan;
        $coupon_details = $this->getDiscountById($coupon_id,$subscription_plan,$is_referral);

        $sub_fee = $subscrib_fee;
        $res = $this->getDiscountByOrder($coupon_details, $sub_fee,$order,$no_of_license);

        return $res;
    }

    /**
     * Save or edit coupons
     * @param array $data
     * @throws \Exception
     */
    public function saveOrEdit(array $data) {
        try {
            $couponDetailsData = array();
            if (isset($data['coupon_details'])) {
                $couponDetailsData = $data['coupon_details'];
                unset($data['coupon_details']);
            }

            \DB::beginTransaction();
            $c =$this->model->firstOrNew(array('id'=>$data['id']));
            $c->code = $data['code'];
            $c->description = $data['description'];
            $c->start_date = $data['start_date'];
            $c->end_date = $data['end_date'];
            if (isset($data['type'])) {
                $c->type = $data['type'];
            }
            if (isset($data['master_referral_id'])) {
                $c->master_referral_id = $data['master_referral_id'];
            }
            if (isset($data['commission_period'])) {
                $c->commission_period = $data['commission_period'];
            }

            \Log::debug("coupon data " . print_r($data, true));

            if ($data['id'] == 0) {
                $c->token = md5($data['code']);
            }
            $c->master_subscription_id = $data['master_subscription_id'];
            // Save coupon data
            $c->save();

            if ( count($couponDetailsData) ) {
                \Log::debug("coupon details");
                \Log::debug(print_r($couponDetailsData, true));
                if (is_string($couponDetailsData)) {
                    $couponDetailsData = json_decode($couponDetailsData, true);
                }
                \Log::debug("coupon details");
                \Log::debug(print_r($couponDetailsData, true));

                $amountGreaterThanZeroCount = 0;
                foreach($couponDetailsData  as $couponDetail)
                {
                    if ($couponDetail['amount'] > 0) {
                        $amountGreaterThanZeroCount++;
                    }
                }

                \Log::debug("amount count " . $amountGreaterThanZeroCount);
                if ($amountGreaterThanZeroCount ==  0) {
                    \DB::rollback();
                    throw new \Exception('Coupon details must have at least one valid amount.', 10);
                }

                // Delete coupon details
                $c->couponDetails()->delete();

                // Coupon details
                $order = 1;
                foreach($couponDetailsData  as &$couponDetail)
                {
                    $couponDetail['order'] = $order;
                    $cd = new CouponDetails($couponDetail);
                    $c->couponDetails()->save($cd);
                    $order++;
                }
            }
            \DB::commit();
            return $c;
        }catch (Exception $e) {
            \DB::rollback();
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Returns coupon specific details by id
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getCouponById($id) {

        $coupon = $this->model->join('master_subscriptions', 'master_subscriptions.id', '=', 'master_subscription_id')
            ->leftJoin('company_subscription_plans', 'company_subscription_plans.coupon_id', '=', 'coupons.id')
            ->select(
                array(
                    'coupons.*',
                    'master_subscriptions.name as master_subscription_name',
                    'master_subscriptions.validity_period_id as validity_period_id',
                    \DB::raw('COUNT(company_subscription_plans.id) as used')
                )
            )->where('coupons.id', $id)->groupBy('coupons.id')->first();

        if (isset($coupon)) {
            $couponData =
               [
                    'id' => (INT)$coupon['id'],
                    'code' => $coupon['code'],
                    'description' => $coupon['description'],
                    'start_date' => date("m/d/Y", strtotime($coupon['start_date'])),
                    'end_date' => date("m/d/Y", strtotime($coupon['end_date'])),
                    'master_subscription_id' => (INT)$coupon['master_subscription_id'],
                    'status' => $coupon['status'],
                    'created_at' => $coupon['created_at'],
                    'updated_at' => $coupon['updated_at'],
                    'type' => $coupon['type'],
                    'token' => $coupon['token'],
                    'master_referral_id' => $coupon['master_referral_id'],
                    'commission_period' => $coupon['commission_period'],
                    'master_subscription_name' => $coupon['master_subscription_name'],
                    'validity_period_id' => $coupon['validity_period_id'],
                    'used' => (INT)$coupon['used'],
                ];




            $couponDetails = $coupon->couponDetails()->get();
            return array('coupon' => $couponData, 'coupon_details' => $couponDetails);
        }else {
            throw new \Exception('No coupon found');
        }

        //return App\Models\Coupon::join('master_subscriptions', 'master_subscriptions.id', '=', 'master_subscription_id')->leftJoin('company_subscription_plans', 'company_subscription_plans.coupon_id', '=', 'coupons.id')->select(array('coupons.*', 'master_subscriptions.name as master_subscription_name', \DB::raw('COUNT(company_subscription_plans.id) as used')))->where('coupons.id', 27)->groupBy('coupons.id')->first();
    }

    /**
     * Returns coupon specific details by id
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getReferralCouponById($id) {

        $coupon = $this->model->select(
                array(
                    'coupons.*',
                )
            )->where('coupons.id', $id)->groupBy('coupons.id')->first();

        if (isset($coupon)) {
            $couponData =
                [
                    'id' => (INT)$coupon['id'],
                    'code' => $coupon['code'],
                    'description' => $coupon['description'],
                    'start_date' => date("m/d/Y", strtotime($coupon['start_date'])),
                    'end_date' => date("m/d/Y", strtotime($coupon['end_date'])),
                    'master_subscription_id' => (INT)$coupon['master_subscription_id'],
                    'status' => $coupon['status'],
                    'created_at' => $coupon['created_at'],
                    'updated_at' => $coupon['updated_at'],
                    'type' => $coupon['type'],
                    'token' => $coupon['token'],
                    'master_referral_id' => $coupon['master_referral_id'],
                    'commission_period' => $coupon['commission_period'],
                    'master_subscription_name' => $coupon['master_subscription_name'],
                    'validity_period_id' => $coupon['validity_period_id'],
                    'used' => (INT)$coupon['used'],
                ];
            $couponDetails = $coupon->couponDetails()->get();
            $couponDetails = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'coupon_id' => $item['coupon_id'],
                    'amount' => (float) $item['amount'],
                    'type' => $item['type'],
                    'order' => $item['order'],
                ];
            }, $couponDetails->toArray());
            return array('coupon' => $couponData, 'coupon_details' => $couponDetails);
        }else {
            throw new \Exception('No coupon found');
        }

        //return App\Models\Coupon::join('master_subscriptions', 'master_subscriptions.id', '=', 'master_subscription_id')->leftJoin('company_subscription_plans', 'company_subscription_plans.coupon_id', '=', 'coupons.id')->select(array('coupons.*', 'master_subscriptions.name as master_subscription_name', \DB::raw('COUNT(company_subscription_plans.id) as used')))->where('coupons.id', 27)->groupBy('coupons.id')->first();
    }

    public function allCoupons($ref_only=0) {

        \Log::debug("coupon details".$ref_only);
        if($ref_only == 0)
        {
            return $this->model->join('master_subscriptions', 'master_subscriptions.id', '=', 'master_subscription_id')
                ->leftJoin('company_subscription_plans', 'company_subscription_plans.coupon_id', '=', 'coupons.id')
                ->select(
                    array(
                        'coupons.*',
                        'master_subscriptions.name as master_subscription_name',
                        \DB::raw('COUNT(company_subscription_plans.id) as used')
                    )
                )->groupBy('coupons.id')->get();
        }
        if($ref_only == 1)
        {
            return $this->model->leftJoin('master_subscriptions', 'master_subscriptions.id', '=', 'master_subscription_id')
                ->leftJoin('company_subscription_plans', 'company_subscription_plans.coupon_id', '=', 'coupons.id')
                ->select(
                    array(
                        'coupons.*',
                        'master_subscriptions.name as master_subscription_name',
                        \DB::raw('COUNT(company_subscription_plans.id) as used')
                    )
                )
                ->where('coupons.type', '=', 'referral')
                ->groupBy('coupons.id')->get();
        }

    }


    /**
     * Returns referral code details by using token
     * @param $token
     */
    public function getReferralByToken($token) {
        $current_date = date('Y-m-d');
        return $this->model->where('token', $token)->first();
    }

    /**
     * Returns referrers commission details
     * @param
     */

    public function allCommissions($id){
        return $this->model->join('company_subscription_plans', 'coupons.id', '=', 'company_subscription_plans.coupon_referral_id')
            ->join('company_subscriptions', 'company_subscription_plans.id', '=', 'company_subscriptions.company_subscription_plan_id')
            ->join('companies', 'company_subscription_plans.company_id', '=', 'companies.id')
            ->join('master_subscriptions', 'company_subscription_plans.master_subscription_id', '=', 'master_subscriptions.id')
            ->select(
                array(
                    'coupons.id',
                    'company_subscriptions.referral_payment_id',
                    'company_subscriptions.referral_commission AS commission',
                    'companies.name AS mjb_name',
                    'master_subscriptions.validity_period_id AS plan',
                    'company_subscriptions.id AS company_subscription_id',
                    'companies.created_at'
                )
            )
            ->where('coupons.master_referral_id', '=', $id)
            ->orderBy('company_subscriptions.referral_payment_id', 'asc')
            ->get();
    }

    /**
     * Returns referral code details by using token
     * @param $token
     */
    public function getCouponDetails($coupon_id) {
        return $this->model->where('id', $coupon_id)->first();
    }
}
