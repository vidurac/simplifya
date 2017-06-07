<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Companies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SQL_MODE='ALLOW_INVALID_DATES';");
        //DB::statement("ALTER TABLE `questions` CHANGE `question` `question` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
        DB::statement("ALTER TABLE `companies` ADD COLUMN `fein_last_digits`  varchar(25) NULL AFTER `reg_no`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::drop('companies');
    }
}
