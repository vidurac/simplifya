<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users =[
            [
                'title'=> 'Mr',
                'name'=> 'Achintha Fernando',
                'email' => 'a@a.com',
                'password' => Hash::make('123456'),
                'status'   => '1',
                'is_invite'   => false,
                'master_user_group_id'   => '1',
                'company_id'   => '1'
            ],
            [
                'title'=> 'Mr',
                'name'=> 'marion',
                'email' => 'marion.m@ceylonsolutions.com',
                'password' => Hash::make('simply@123'),
                'status'   => '1',
                'is_invite'   => false,
                'master_user_group_id'   => '1',
                'company_id'   => '1'
            ]
        ];

        DB::table('users')->delete();
        foreach ($users as $key => $value) {
            App\Models\User::create($value);
        }
    }
}
