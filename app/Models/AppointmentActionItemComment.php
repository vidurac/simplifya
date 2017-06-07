<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentActionItemComment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointment_action_item_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['appointment_id','type', 'question_action_item_id', 'user_id', 'latitude', 'longitude', 'location', 'created_by', 'updated_by', 'content', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function image()
    {
        return $this->hasMany('App\Models\Image', 'entity_id');
    }

}
