<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterLicences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SQL_MODE='ALLOW_INVALID_DATES';");
        DB::statement("ALTER TABLE `master_licenses` ADD COLUMN `type`  enum('FEDERAL','NORMAL') NULL DEFAULT 'NORMAL' AFTER `status`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
