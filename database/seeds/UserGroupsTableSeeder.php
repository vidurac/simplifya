<?php

use Illuminate\Database\Seeder;

class UserGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups =[
            [
                'name'=> 'Master Admin',
                'status'=> '1',
                'entity_type_id'=> '1'
            ],
            [
                'name'=> 'Master Admin',
                'status'=> '1',
                'entity_type_id'=> '2'
            ],
            [
                'name'=> 'Manager',
                'status'=> '1',
                'entity_type_id'=> '2'
            ],
            [
                'name'=> 'Employee',
                'status'=> '1',
                'entity_type_id'=> '2'
            ],
            [
                'name'=> 'Master Admin',
                'status'=> '1',
                'entity_type_id'=> '3'
            ],
            [
                'id'=> '6',
                'name'=> 'Inspector',
                'status'=> '1',
                'entity_type_id'=> '3'
            ],
            [
                'name'=> 'Master Admin',
                'status'=> '1',
                'entity_type_id'=> '4'
            ],
            [
                'name'=> 'Inspector',
                'status'=> '1',
                'entity_type_id'=> '4'
            ]

        ];

        DB::table('master_user_groups')->delete();
        foreach ($groups as $key => $value) {
            App\Models\MasterUserGroup::create($value);
        }

    }
}
