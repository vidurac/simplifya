<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterClassificationEntityAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_classification_entity_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('classification_id')->unsigned();
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
        Schema::drop('master_classification_entity_allocations');
    }
}
