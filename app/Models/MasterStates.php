<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterStates extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'country_id', 'status', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterCountry(){
        return $this->belongsTo('App\Models\MasterCountry', 'country_id');
    }

    public function masterCity(){
        return $this->hasMany('App\Models\MasterCity', 'status_id');
    }

    public function masterLicense(){
        return $this->hasMany('App\Models\MasterLicense', 'master_states_id');
    }

    public function companyLocation()
    {
        return $this->belongsTo('App\Models\CompanyLocation', 'states_id');
    }
}
