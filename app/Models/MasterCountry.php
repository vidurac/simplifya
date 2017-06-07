<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCountry extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'created_by', 'updated_by', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterStates(){
        return $this->hasMany('App\Models\MasterStates', 'country_id');
    }

}
