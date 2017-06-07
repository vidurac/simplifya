<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterUserGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'entity_type_id', 'created_by', 'updated_by', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user(){
        return $this->hasOne('App\Models\User', 'id');
    }
}
