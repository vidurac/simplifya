<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyLocationsTableToMakeFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `company_locations`
                        MODIFY COLUMN `contact_email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `zip_code`,
                        MODIFY COLUMN `contact_person`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `contact_email`;
                      ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
