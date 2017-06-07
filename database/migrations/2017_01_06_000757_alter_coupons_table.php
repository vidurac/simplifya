<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SQL_MODE='ALLOW_INVALID_DATES';");
        DB::statement("ALTER TABLE `coupons` CHANGE COLUMN `plan_id` `master_subscription_id`  int(11) NOT NULL AFTER `end_date`;");
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
