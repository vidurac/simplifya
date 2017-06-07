<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashBoardModule extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dashboard_module';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','module_id'];
    
}
