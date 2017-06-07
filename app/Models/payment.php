<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['req_date_time', 'tx_id', 'object', 'req_currency', 'req_amount', 'res_date_time', 'res_id', 'res_currency', 'res_amount', 'company_id', 'tx_type', 'tx_status', 'charge_id','balance_transaction' ,'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }


}
