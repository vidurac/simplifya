<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponDetails extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupon_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['coupon_id', 'amount', 'type', 'order'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function coupon(){
        return $this->belongsTo('App\Models\Coupon', 'coupon_id');
    }
}
