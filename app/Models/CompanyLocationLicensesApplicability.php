<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLocationLicensesApplicability extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_location_licenses_applicability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_location_id', 'location_license_id','master_applicability_id','type','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
