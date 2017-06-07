<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmationCode extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'confirmation_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'confirmation_code','is_confirm'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * relationship between User and Role
     */
    public function user()
    {
        return $this->hasOne('App\Models\user');
    }
}
