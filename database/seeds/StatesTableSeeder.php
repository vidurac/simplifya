<?php

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states =[
            [
                'name'=> 'Colarado',
                'country_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'Alaska',
                'country_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'Arizona',
                'country_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'California',
                'country_id'=>'1',
                'status'=> '0'
            ],
            [
                'name'=> 'England',
                'country_id'=>'2',
                'status'=> '1'
            ],
            [
                'name'=> 'Scotland',
                'country_id'=>'2',
                'status'=> '0'
            ],
            [
                'name'=> 'Colombo',
                'country_id'=>'3',
                'status'=> '0'
            ],
            [
                'name'=> 'Gampaha',
                'country_id'=>'3',
                'status'=> '1'
            ]

    ];

        DB::table('master_states')->delete();
		foreach ($states as $key => $value) {
            App\Models\MasterStates::create($value);
        }
    }
}
