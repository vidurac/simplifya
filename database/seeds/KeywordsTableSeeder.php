<?php

use Illuminate\Database\Seeder;

class KeywordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $audit =[
            [
                'id'=> '1',
                'name'=> 'keyword one',

            ],
            [
                'id'=> '2',
                'name'=> 'keyword two'

            ],
            [
                'id'=> '3',
                'name'=> 'keyword three'

            ],
            [
                'id'=> '4',
                'name'=> 'keyword four'

            ],


        ];

        DB::table('master_keywords')->delete();
        foreach ($audit as $key => $value) {
            App\Models\MasterKeyword::create($value);
        }
    }
}
