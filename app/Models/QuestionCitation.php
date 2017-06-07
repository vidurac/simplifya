<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionCitation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'question_citation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question_id', 'citation', 'description', 'link', 'order_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
