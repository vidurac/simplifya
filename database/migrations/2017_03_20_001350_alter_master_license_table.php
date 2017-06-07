<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterLicenseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_licenses', function ($table) {
            DB::statement('ALTER TABLE `master_licenses` DROP COLUMN `type`;');
            $table->integer('type')->default(2)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_licenses', function($table) {
            $table->dropColumn('type');
        });
    }
}
