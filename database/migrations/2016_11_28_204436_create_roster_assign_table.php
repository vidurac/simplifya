<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRosterAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rosters_assign', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('roster_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date');
            $table->integer('frequency');
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
        Schema::drop('rosters_assign');
    }
}
