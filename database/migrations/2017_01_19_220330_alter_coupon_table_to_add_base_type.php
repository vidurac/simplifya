<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCouponTableToAddBaseType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function ($table) {
            $table->enum('type', array('coupon', 'referral'))->default('coupon')->after('status');
            $table->string('token')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function($table) {
            $table->dropColumn('type');
            $table->dropColumn('token');
        });
    }
}
