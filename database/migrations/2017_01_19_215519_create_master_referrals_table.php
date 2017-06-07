<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_referrals', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->text('commission_rates');
            $table->enum('type', array('partner', 'salesperson', 'contractor', 'business'));
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
        Schema::drop('master_referrals');
    }
}
