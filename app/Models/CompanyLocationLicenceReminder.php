<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLocationLicenceReminder extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_location_licence_reminders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_location_license_id', 'reminder_id', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
