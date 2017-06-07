<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre_answer_id')->unsigned();
            $table->integer('answer_value_id')->unsigned();
            $table->integer('answer_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->integer('supper_parent_question_id')->unsigned();
            $table->boolean('is_deleted');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('question_answers');
    }
}
