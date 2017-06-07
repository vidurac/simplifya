<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->integer('master_user_group_id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->string('title');
            $table->boolean('is_invite');
            $table->integer('is_send_mail');
            $table->integer('status');
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('password_confirmation_code');
            $table->boolean('password_is_confirm');
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
