<?php

use Illuminate\Database\Seeder;

class AnswersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $answer =[
            [
                'name'=> 'Yes',
                'status'=> '1'
            ],
            [
                'name'=> 'No',
                'status'=> '1'
            ],
            [
                'name'=> 'I Dont Know',
                'status'=> '1'
            ]

        ];

        DB::table('master_answers')->delete();
        foreach ($answer as $key => $value) {
            App\Models\MasterAnswer::create($value);
        }
    }
}
