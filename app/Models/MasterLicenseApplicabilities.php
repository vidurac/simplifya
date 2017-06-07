<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterLicenseApplicabilities extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_license_applicabilities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['master_license_id', 'master_applicability_id','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
