<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country =[
            [
                'name'=> 'United States',
                'status'=> '1'
            ],
            [
                'name'=> 'United Kingdom',
                'status'=> '0'
            ],
            [
                'name'=> 'Sri Lanka',
                'status'=> '1'
            ]
        ];

        DB::table('master_countries')->delete();
        foreach ($country as $key => $value) {
            App\Models\MasterCountry::create($value);
        }
    }
}
