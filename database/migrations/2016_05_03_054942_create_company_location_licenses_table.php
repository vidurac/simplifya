<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyLocationLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_location_licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('license_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->integer('payment_id')->unsigned();
            $table->string('license_number');
            $table->string('name');
            $table->date('license_date');
            $table->date('renewal_date');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_expired');
            $table->integer('status');
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
        Schema::drop('company_location_licenses');
    }
}

