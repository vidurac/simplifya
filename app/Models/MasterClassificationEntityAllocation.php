<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterClassificationEntityAllocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_classification_entity_allocations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['classification_id', 'entity_type_id', 'created_by', 'updated_by'];

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
