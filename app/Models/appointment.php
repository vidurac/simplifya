<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from_company_id', 'to_company_id', 'company_location_id', 'assign_to_user_id', 'payment_id', 'inspection_number', 'comment', 'inspection_date_time', 'appointment_status', 'report_status', 'created_by', 'updated_by', 'amount', 'start_inspection', 'finish_inspection' , 'share_mjb'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
}
