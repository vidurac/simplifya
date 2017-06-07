<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCards extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['company_id', 'card_id', 'created_by', 'updated_by','status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'id');
    }
}
