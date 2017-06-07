<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_company_id')->unsigned();
            $table->integer('to_company_id')->unsigned();
            $table->integer('company_location_id')->unsigned();
            $table->integer('assign_to_user_id')->unsigned();
            $table->integer('payment_id')->unsigned();
            $table->string('inspection_number');
            $table->text('comment');
            $table->dateTime('inspection_date_time');
            $table->integer('appointment_status');
            $table->boolean('share_mjb');
            $table->dateTime('start_inspection');
            $table->dateTime('finish_inspection');
            $table->decimal('start_latitude', 8, 6);
            $table->decimal('start_longitude', 9, 6);
            $table->decimal('finish_latitude', 8, 6);
            $table->decimal('finish_longitude', 9, 6);
            $table->integer('report_status');
            $table->decimal('amount', 10,2);
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
        Schema::drop('appointments');
    }
}
