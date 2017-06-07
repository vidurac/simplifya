<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCouponTableToAllowMasterSubscriptionIdAsZero extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `coupons` MODIFY `master_subscription_id` INTEGER UNSIGNED NULL DEFAULT 0;');
        DB::statement('ALTER TABLE `coupons` MODIFY `token` VARCHAR(255) NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `coupons` MODIFY `master_subscription_id` INTEGER UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE `coupons` MODIFY `token` VARCHAR(255) NOT NULL;');
    }
}
