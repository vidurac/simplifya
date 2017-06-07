<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionActionItem extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'question_action_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'question_id', 'created_by', 'updated_by', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function Question(){
        return $this->belongsTo('App\Models\Question', 'question_answer_id');
    }
}
