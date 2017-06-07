<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKeyword extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_keywords';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
