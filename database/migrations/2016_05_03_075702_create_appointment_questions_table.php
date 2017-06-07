<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('comment');
            $table->integer('question_id')->unsigned();
            $table->integer('appointment_id')->unsigned();
            $table->integer('master_answer_id')->unsigned();
            $table->integer('parent_question_id')->unsigned();
            $table->integer('supper_parent_question_id')->unsigned();
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
        Schema::drop('appointment_questions');
    }
}
