<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_locations', function ($table) {
            $table->string('contact_email')->default(null);
            $table->string('contact_person')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_locations', function($table) {
            $table->dropColumn('contact_email');
            $table->dropColumn('contact_person');
        });
    }
}
