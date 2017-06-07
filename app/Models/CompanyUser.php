<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'location_id', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function companyLocation()
    {
        return $this->belongsTo('App\Models\CompanyLocation', 'location_id');
    }
}
