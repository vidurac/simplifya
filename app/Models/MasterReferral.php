<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterReferral extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_referrals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'commition_rates', 'type'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterReferralPayments()
    {
        return $this->hasMany('App\Models\MasterReferralPayment', 'master_referral_id');
    }

    public function coupons()
    {
        return $this->hasMany('App\Models\Coupon', 'master_referral_id');
    }
}
