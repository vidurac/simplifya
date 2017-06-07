<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterTask extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rosters_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'roster_id','status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function roster(){
        return $this->belongsTo('App\Models\Roster', 'roster_id');
    }

    public function rosterTaskResult(){
        return $this->belongsTo('App\Models\RosterTaskResult', 'task_id');
    }
}
