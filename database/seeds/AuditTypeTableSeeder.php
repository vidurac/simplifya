<?php

use Illuminate\Database\Seeder;

class AuditTypeTableSeeder extends Seeder
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
                'name'=> 'In-house',

            ],
            [
                'id'=> '2',
                'name'=> '3rd-Party'

            ]


        ];

        DB::table('master_audit_types')->delete();
        foreach ($audit as $key => $value) {
            App\Models\MasterAuditType::create($value);
        }
    }
}
