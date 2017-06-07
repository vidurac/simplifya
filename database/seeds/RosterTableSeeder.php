<?php

use Illuminate\Database\Seeder;

class RosterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rosters =[
            [
                'name'=> 'Opening',
                'company_id'=> '13',
                'status'=> '1',
            ],
            [
                'name'=> 'Closing',
                'company_id'=> '13',
                'status'=> '1',
            ]
        ];

        DB::table('rosters')->delete();
        foreach ($rosters as $key => $value) {
            App\Models\Roster::create($value);
        }
    }
}
