<?php

use Illuminate\Database\Seeder;

class LicensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $license =[
            [
                'id'=> '1',
                'name'=> 'Retail Cultivation Facility license',
                'master_states_id'=>'1',
                'status'=> '1',
                'checklist_fee'=> '12.50'
            ],
            [
                'id'=> '2',
                'name'=> 'Retail Marijuana Store license',
                'master_states_id'=>'1',
                'status'=> '1',
                'checklist_fee'=> '12.50'
            ],
            [
                'id'=> '3',
                'name'=> 'Medical Marijuana Center license',
                'master_states_id'=>'1',
                'status'=> '1',
                'checklist_fee'=> '12.50'
            ],
            [
                'id'=> '4',
                'name'=> 'Retail Marijuana Store license',
                'master_states_id'=>'1',
                'status'=> '0',
                'checklist_fee'=> '12.50'
            ],
            [
                'id'=> '5',
                'name'=> 'License A',
                'master_states_id'=>'7',
                'status'=> '0',
                'checklist_fee'=> '50'
            ],
            [
                'id'=> '6',
                'name'=> 'License B',
                'master_states_id'=>'7',
                'status'=> '1',
                'checklist_fee'=> '150'
            ],
            [
                'id'=> '7',
                'name'=> 'License C',
                'master_states_id'=>'7',
                'status'=> '0',
                'checklist_fee'=> '12.99'
            ]
        ];

        DB::table('master_licenses')->delete();
        foreach ($license as $key => $value) {
            App\Models\MasterLicense::create($value);
        }
    }
}
