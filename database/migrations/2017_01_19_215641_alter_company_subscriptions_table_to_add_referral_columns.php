<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanySubscriptionsTableToAddReferralColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_subscriptions', function ($table) {
            $table->integer('referral_payment_id');
            $table->decimal('referral_commission', 10,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_subscriptions', function($table) {
            $table->dropColumn('referral_payment_id');
            $table->dropColumn('referral_commission');
        });
    }
}
