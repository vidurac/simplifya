<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('master_states_id')->unsigned();
            $table->integer('status');
            $table->decimal('checklist_fee', 10, 2);
            $table->decimal('checklist_fee_inhouse', 10, 2);
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
        Schema::drop('master_licenses');
    }
}
