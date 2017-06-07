<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('reg_no');
            $table->integer('status_id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->integer('entity_type')->unsigned();
            $table->integer('status');
            $table->integer('is_first_attempt');
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('stripe_id')->nullable();
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
        Schema::drop('companies');
    }
}
