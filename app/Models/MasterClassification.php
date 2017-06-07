<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterClassification extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_classifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'status', 'is_system', 'predecessor_1', 'predecessor_2', 'is_required', 'is_main', 'is_multiselect', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function masterClassificationOptions(){
        return $this->hasMany('App\Models\MasterClassificationOption', 'classification_id');
    }

    public function masterClassificationAllocations(){
        return $this->hasMany('App\Models\MasterClassificationEntityAllocation', 'classification_id');
    }

}
