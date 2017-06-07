<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company =[
            [
                'name'=> 'Simplifya',
                'reg_no'=> '123456',
                'status_id'=> '0',
                'country_id'=> '0',
                'entity_type'=> '1',
                'status'=> '2',
                'created_by'=> '1',
                'updated_by'=> '1',
                'stripe_id'=> '',
            ]
        ];

        DB::table('companies')->delete();
        foreach ($company as $key => $value) {
            App\Models\company::create($value);
        }
    }
}
