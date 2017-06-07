<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLocationLicense extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_location_licenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'license_id', 'location_id', 'payment_id', 'license_number', 'name', 'license_date', 'renewal_date','amount','is_expired', 'created_by', 'updated_by', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function companyLocation()
    {
        return $this->belongsTo('App\Models\CompanyLocation', 'location_id');
    }

    public function masterLicense()
    {
        return $this->hasOne('App\Models\MasterLicense', 'id', 'license_id');

    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'id');
    }
}
