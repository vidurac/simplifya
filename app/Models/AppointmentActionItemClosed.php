<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentActionItemClosed extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointment_action_item_closed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['appointment_id', 'action_item_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
