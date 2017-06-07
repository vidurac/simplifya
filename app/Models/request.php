<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from_company_id', 'to_company_id', 'company_location_id', 'comment', 'created_by', 'updated_by', 'status'];

    public function complianceCompany()
    {
        return $this->belongsTo('App\Models\Company', 'to_company_id');
    }

    public function marijuanaCompany()
    {
        return $this->belongsTo('App\Models\Company', 'from_company_id');
    }

    public function companyLocation()
    {
        return $this->belongsTo('App\Models\CompanyLocation', 'company_location_id');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
