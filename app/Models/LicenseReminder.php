<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseReminder extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'license_reminders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['license_location_id', 'user_id', 'reminder'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}
