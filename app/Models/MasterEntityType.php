<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEntityType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_entity_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'created_by', 'updated_by', 'status'];

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

    public function masterSubscription()
    {
        return $this->hasMany('App\Models\MasterSubscription', 'entity_type_id');
    }
}
