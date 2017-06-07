<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQuestionsAndQuestionActionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SQL_MODE='ALLOW_INVALID_DATES';");
        DB::statement("ALTER TABLE `questions` CHANGE `question` `question` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
        DB::statement("ALTER TABLE `question_action_items` CHANGE `name` `name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
