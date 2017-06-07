<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterApplicabilities extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_applicabilities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type','country_id','status','group_id', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The licence that belong to the applicability.
     */
    public function licenses()
    {
        return $this->belongsToMany('App\Models\MasterLicense');
    }

}
