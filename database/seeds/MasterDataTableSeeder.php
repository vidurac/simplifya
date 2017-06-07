<?php

use Illuminate\Database\Seeder;

class MasterDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $masterDatas =[
            [
                'name'=> 'MJB_FREE_SIGN_UP',
                'value'=> '1',
                'created_by'=> '1',
                'updated_by'=> '1',
            ],
            [
                'name'=> 'MJB_FREE_LICENSE',
                'value'=> '1',
                'created_by'=> '1',
                'updated_by'=> '1',
            ],
            [
                'name'=> 'CC_GE_FREE_CHECKLIST',
                'value'=> '1',
                'created_by'=> '1',
                'updated_by'=> '1',
            ]
        ];

        foreach ($masterDatas as $key => $value) {
            App\Models\MasterData::create($value);
        }
    }
}
