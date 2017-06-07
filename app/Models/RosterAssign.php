<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterAssign extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rosters_assign';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'roster_id','start_date','end_date','due_date','frequency'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function roster(){
        return $this->belongsTo('App\Models\Roster', 'roster_id');
    }
    public function rosterJobs(){
        return $this->hasMany('App\Models\RosterJob', 'roster_assign_id');
    }
}
