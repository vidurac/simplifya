<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanySubscriptionPlansTableToAddCouponReferralId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_subscription_plans', function ($table) {
            $table->integer('coupon_referral_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_subscription_plans', function($table) {
            $table->dropColumn('coupon_referral_id');
        });
    }
}
