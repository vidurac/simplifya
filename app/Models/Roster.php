<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rosters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'company_id','status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    public function rosterTask()
    {
        return $this->hasMany('App\Models\RosterTask', 'roster_id');
    }
    public function rosterAssignees()
    {
        return $this->hasMany('App\Models\RosterAssign', 'roster_id');
    }
}
