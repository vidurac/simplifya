<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['version_no', 'question', 'explanation', 'is_mandatory', 'is_draft', 'is_archive', 'comment', 'question_answer_id', 'parent_question_id', 'master_question_id', 'previous_question_id', 'supper_parent_question_id', 'created_by', 'updated_by', 'status', 'is_deleted', 'law', 'published_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function questionAnswer(){
        return $this->hasMany('App\Models\QuestionAnswer', 'question_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}
