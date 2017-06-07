<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentQuestion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointment_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['comment', 'question_id', 'parent_id', 'appointment_id', 'master_answer_id', 'parent_question_id', 'supper_parent_question_id', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
