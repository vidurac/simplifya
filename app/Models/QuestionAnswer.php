<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'question_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['answer_value_id', 'pre_answer_id', 'answer_id', 'question_id', 'supper_parent_question_id', 'created_by', 'updated_by', 'is_deleted'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function Question(){
        return $this->hasMany('App\Models\Question');
    }

    /**
     * get master answer list
     */
//    public function masterAnswer(){
//
//    }
}
