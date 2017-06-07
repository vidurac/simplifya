<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionClassification extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'question_classifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question_id', 'entity_tag', 'option_value', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
