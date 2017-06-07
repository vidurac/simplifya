<?php

use Illuminate\Database\Seeder;

class ClassificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classification =[
            [
                'name'=> 'Custom Classification One',
                'status'=> '1',
                'is_system'=> '1',
                'is_required'=> '1',
                'is_main'=> '1',
                'is_multiselect' => '0'

            ]

        ];

        DB::table('master_classifications')->delete();
        foreach ($classification as $key => $value) {
            App\Models\MasterClassification::create($value);
        }
    }
}
