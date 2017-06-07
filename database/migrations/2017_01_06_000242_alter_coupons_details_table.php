<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCouponsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SQL_MODE='ALLOW_INVALID_DATES';");
        DB::statement("ALTER TABLE `coupon_details` MODIFY COLUMN `type`  enum('fixed','percentage') NOT NULL DEFAULT 'percentage' AFTER `amount`;");
        DB::statement("ALTER TABLE `coupon_details` CHANGE COLUMN `master_coupon_id` `coupon_id`  int(11) NOT NULL AFTER `id`;");
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
