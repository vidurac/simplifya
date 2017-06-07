<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySubscription extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'master_subscription_id', 'amount', 'payment_id', 'created_by', 'updated_by', 'company_subscription_plan_id','discount','referral_commission'];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function companySubscriptionPlan()
    {
        return $this->belongsTo('App\Models\CompanySubscriptionPlan', 'company_subscription_plan_id');
    }

    public function masterReferralPayments()
    {
        return $this->hasMany('App\Models\MasterReferralPayment', 'referral_payment_id');
    }

}
