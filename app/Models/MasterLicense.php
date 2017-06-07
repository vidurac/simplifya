<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterLicense extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_licenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type', 'master_states_id', 'status', 'checklist_fee', 'checklist_fee_inhouse', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterStates(){
        return $this->belongsTo('App\Models\MasterStates', 'master_states_id');
    }

    public function companyLocationLicense()
    {
        return $this->belongsTo('App\Models\CompanyLocationLicense', 'license_id');
    }

    /**
     * The applicability that belong to the master license.
     */
    public function applicabilities()
    {
        return $this->belongsToMany('App\Models\MasterApplicabilities', 'master_license_applicabilities', 'master_license_id', 'master_applicability_id')->withTimestamps();
    }
}
