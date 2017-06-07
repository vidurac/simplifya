<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySubscriptionPlan extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_subscription_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'master_subscription_id', 'subscription_fee', 'start_date', 'end_date', 'due_date', 'active','coupon_id','coupon_referral_id'];

    public function companySubscriptions()
    {
        return $this->hasMany('App\Models\CompanySubscription', 'company_subscription_plan_id');
    }



}
