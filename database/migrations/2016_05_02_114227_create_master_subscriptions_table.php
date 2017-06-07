<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('from_licence');
            $table->integer('to_licence');
            $table->decimal('amount', 10, 2);
            $table->integer('status');
            $table->integer('validity_period_id')->unsigned();
            $table->integer('entity_type_id')->unsigned();
            $table->string('created_by');
            $table->string('updated_by');
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
        Schema::drop('master_subscriptions');
    }
}
