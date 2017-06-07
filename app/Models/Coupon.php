<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'description', 'start_date', 'end_date', 'token','master_subscription_id', 'commission_period'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function couponDetails()
    {
        return $this->hasMany('App\Models\CouponDetails', 'coupon_id');
    }

    public function masterSubscription()
    {
        return $this->belongsTo('App\Models\MasterSubscription', 'master_subscription_id');
    }

    public function masterReferral(){
        return $this->belongsTo('App\Models\MasterReferral', 'master_referral_id');
    }

}
