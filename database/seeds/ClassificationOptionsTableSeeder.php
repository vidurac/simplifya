<?php

use Illuminate\Database\Seeder;

class ClassificationOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $options =[
            [
                'name'=> 'Option A',
                'option_value'=> 'option_a',
                'status'=> '1',
                'classification_id'=> '1'

            ]
        ];

        DB::table('master_classification_options')->delete();
        foreach ($options as $key => $value) {
            App\Models\MasterClassificationOption::create($value);
        }
    }
}
