<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities =[
            [
                'name'=> 'Aguilar',
                'status_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'Bow Mar',
                'status_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'Castle Rock',
                'status_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'Estes Park',
                'status_id'=>'1',
                'status'=> '1'
            ],
            [
                'name'=> 'Frederick',
                'status_id'=>'1',
                'status'=> '0'
            ],
            [
                'name'=> 'Genoa',
                'status_id'=>'2',
                'status'=> '1'
            ],
            [
                'name'=> 'Glendale',
                'status_id'=>'2',
                'status'=> '0'
            ],
            [
                'name'=> 'Nugegoda',
                'status_id'=>'7',
                'status'=> '0'
            ],
            [
                'name'=> 'Colombo 7',
                'status_id'=>'7',
                'status'=> '0'
            ]



        ];

        DB::table('master_cities')->delete();
        foreach ($cities as $key => $value) {
            App\Models\MasterCity::create($value);
        }
    }
}
