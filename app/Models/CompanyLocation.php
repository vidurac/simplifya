<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'company_id', 'city_id', 'states_id', 'address_line_1', 'address_line_2', 'zip_code','phone_number', 'created_by', 'updated_by', 'status','contact_email','contact_person'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function masterCity(){
        return $this->hasOne('App\Models\MasterCity', 'id', 'city_id');
    }

    public function masterStates(){
        return $this->hasOne('App\Models\MasterStates', 'id', 'states_id');
    }

    public function companyUser()
    {
        return $this->hasMany('App\Models\CompanyUser', 'location_id');
    }

    public function companyLocationLicense()
    {
        return $this->hasMany('App\Models\CompanyLocationLicense', 'id');
    }

    public function request()
    {
        return $this->hasMany('App\Models\request');
    }
}
