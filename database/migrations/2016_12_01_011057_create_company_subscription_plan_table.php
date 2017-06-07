<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanySubscriptionPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('company_subscription_plans');
        Schema::create('company_subscription_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('master_subscription_id');
            $table->decimal('subscription_fee', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date');
            $table->integer('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('company_subscription_plans');
    }
}
