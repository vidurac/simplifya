<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('req_date_time');
            $table->string('tx_id', 45);
            $table->string('object', 45);
            $table->string('req_currency', 45);
            $table->decimal('req_amount', 10,2);
            $table->dateTime('res_date_time');
            $table->string('res_id', 45);
            $table->string('res_currency', 45);
            $table->decimal('res_amount', 10,2);
            $table->integer('company_id');
            $table->string('tx_type', 45);
            $table->integer('tx_status');
            $table->string('charge_id', 45);
            $table->string('balance_transaction', 45);
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
        Schema::drop('payments');
    }
}
