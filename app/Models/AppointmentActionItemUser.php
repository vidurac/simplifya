<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentActionItemUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointment_action_item_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['appointment_id', 'question_action_item_id', 'user_id', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
