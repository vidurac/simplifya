<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAnswerValue extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_answer_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'status', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
