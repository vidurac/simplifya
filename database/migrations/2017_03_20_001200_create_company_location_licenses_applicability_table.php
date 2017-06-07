<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyLocationLicensesApplicabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_location_licenses_applicability', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_location_id');
            $table->integer('location_license_id');
            $table->integer('master_applicability_id');
            $table->string('type');
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
        Schema::drop('company_location_licenses_applicability');
    }
}
