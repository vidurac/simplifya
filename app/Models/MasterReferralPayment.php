<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterReferralPayment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_referral_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['master_referral_id', 'amount', 'comment'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterReferral(){
        return $this->belongsTo('App\Models\MasterReferral', 'master_referral_id');
    }

    public function companySubscription(){
        return $this->belongsTo('App\Models\CompanySubscription', 'referral_payment_id');
    }

}
