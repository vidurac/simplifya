<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('version_no');
            $table->string('question');
            $table->string('explanation');
            $table->boolean('is_mandatory');
            $table->boolean('is_draft');
            $table->boolean('is_archive');
            $table->string('comment');
            $table->integer('question_answer_id')->unsigned();
            $table->integer('parent_question_id')->unsigned();
            $table->integer('master_question_id')->unsigned();
            $table->integer('previous_question_id')->unsigned();
            $table->integer('supper_parent_question_id')->unsigned();
            $table->integer('status');
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
        Schema::drop('questions');
    }
}
