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


class MasterReferralPaymentsRepository extends Repository
{
    public function model() {
        return 'App\Models\MasterReferralPayment';
    }

    /**
     * Create Referral Payments and Update Company Subscription
     * @param array $data
     * @throws \Exception
     */
    public function createReferralPaymentAndUpdateCompanySubscriptions(array $data) {
        $company_subscription=array();
        foreach ($data['commissions'] as $commission){
            array_push($company_subscription,$commission['company_subscription_id']);
        }
        unset($data['commissions']);
        try {

            \DB::beginTransaction();
            $r =$this->model->create($data);
            if($r){
                $company_subscription=\DB::table('company_subscriptions')
                    ->whereIn('id',$company_subscription)
                    ->update(array('referral_payment_id'=>$r->id));

                if($company_subscription){
                    \DB::commit();
                }
            }


        }catch (Exception $e) {
            \DB::rollback();
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }




    /**
     * Returns referrer specific payment details
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function allCommissionPaymentsByReferrer($id){
        $referrer_payments = $this->model->select(
                array(
                    'master_referral_payments.*',
                )
            )->where('master_referral_payments.master_referral_id', $id)->get();

        if (isset($referrer_payments)) {
            return $referrer_payments;
        }else {
            throw new \Exception('No payment found');
        }
    }
}