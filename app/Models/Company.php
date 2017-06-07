<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'reg_no', 'status_id', 'country_id', 'entity_type', 'completion_status', 'is_approved', 'is_first_attempt', 'created_by', 'updated_by', 'status','fein_last_digits','foc', 'coupon_referral_id','commission_end_date'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function companyLocation()
    {
        return $this->hasMany('App\Models\CompanyLocation', 'company_id');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User', 'company_id');
    }

    public function masterEntityType()
    {
        return $this->hasMany('App\Models\MasterEntityType', 'id', 'entity_type');
    }

    public function request()
    {
        return $this->hasMany('App\Models\request');
    }

    public function companyLocationLicense()
    {
        return $this->hasMany('App\Models\CompanyLocationLicense', 'company_id');
    }

    public function payment()
    {
        return $this->hasMany('App\Models\Payment', 'company_id');
    }

    public function companyCards()
    {
        return $this->hasMany('App\Models\CompanyCards', 'company_id');
    }

    public function companySubscriptionPlans()
    {
        return $this->hasMany('App\Models\MasterSubscription', 'company_id');
    }

    public function roster()
    {
        return $this->hasMany('App\Models\Roster', 'company_id');
    }
}
