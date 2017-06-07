<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterTaskResult extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rosters_task_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['task_id', 'job_id','status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function roster(){
        return $this->belongsTo('App\Models\Roster', 'roster_id');
    }

    public function rosterJob()
    {
        return $this->belongsTo('App\Models\RosterJob', 'job_id');
    }

    public function rosterTask()
    {
        return $this->belongsTo('App\Models\RosterTaskResult', 'task_id');
    }
}
