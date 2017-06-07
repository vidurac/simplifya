<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPermissionUserGroupAllocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_permission_user_group_allocations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['permission_id', 'user_group_id', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
