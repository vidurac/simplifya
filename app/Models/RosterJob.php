<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterJob extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rosters_jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['roster_assign_id', 'roster_id','status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function rosterAssignee()
    {
        return $this->belongsTo('App\Models\RosterAssign', 'roster_assign_id');
    }
    public function rosterTaskResult(){
        return $this->hasMany('App\Models\RosterTaskResult', 'job_id');
    }

}
