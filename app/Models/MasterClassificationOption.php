<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterClassificationOption extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_classification_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'classification_id', 'option_value', 'created_by', 'updated_by', 'status','parent_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterClassification(){
        return $this->belongsTo('App\Models\MasterClassification', 'classification_id');
    }
}
