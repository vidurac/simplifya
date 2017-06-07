<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentCommentsNotifyUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointment_comments_notify_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'appointment_action_item_comments_id', 'status', 'type', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['updated_at'];
}
