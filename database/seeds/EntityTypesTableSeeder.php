<?php

use Illuminate\Database\Seeder;

class EntityTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entity =[
            [
                'name'=> 'Simplifya',
                'status'=> '2'
            ],
            [
                'name'=> 'Marijuana Business',
                'status'=> '1'
            ],
            [
                'name'=> 'Compliance Company',
                'status'=> '1'
            ],
            [
                'name'=> 'Government Entity',
                'status'=> '1'
            ]
        ];

        DB::table('master_entity_types')->delete();
        foreach ($entity as $key => $value) {
            App\Models\MasterEntityType::create($value);
        }
    }
}
