<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCity extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'status_id', 'created_by', 'updated_by', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterStates(){
        return $this->belongsTo('App\Models\MasterStates', 'status_id');
    }

    public function companyLocation()
    {
        return $this->belongsTo('App\Models\CompanyLocation', 'city_id');
    }
}
