<?php

use Illuminate\Database\Seeder;

class AnswerValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $value =[
            [
                'name'=> 'Compliant',
                'status'=> '1'
            ],
            [
                'name'=> 'Non-Compliant',
                'status'=> '1'
            ],
            [
                'name'=> 'Unknown Compliance ',
                'status'=> '1'
            ]

        ];

        DB::table('master_answer_values')->delete();
        foreach ($value as $key => $value) {
            App\Models\MasterAnswerValue::create($value);
        }
    }
}
