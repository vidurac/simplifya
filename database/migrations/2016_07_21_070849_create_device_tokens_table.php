<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('device_tokens', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('user_id')->index();
         $table->string('device_token', 1024)->index();
         $table->string('device_type', 20);
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
        Schema::drop('device_tokens');
    }
}
