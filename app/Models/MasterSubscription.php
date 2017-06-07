<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSubscription extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'from_licence', 'to_licence', 'amount', 'status', 'validity_period_id', 'entity_type_id', 'created_by', 'updated_by', 'description'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterEntityType()
    {
        return $this->belongsTo('App\Models\MasterEntityType', 'id');
    }

    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo('App\Models\Coupon', 'master_subscription_id');
    }
}
