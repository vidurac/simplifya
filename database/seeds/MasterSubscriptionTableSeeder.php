<?php

use Illuminate\Database\Seeder;

class MasterSubscriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscription =[
            [
                'name'       => 'Monthly',
                'on_of_days' => '30',
                'status'     => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'name'       => 'Quarterly',
                'on_of_days' => '109',
                'status'     => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'name'       => 'Half Yearly',
                'on_of_days' => '182',
                'status'     => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'name'       => 'Yearly',
                'on_of_days' => '365',
                'status'     => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ]
        ];

        DB::table('master_subscription')->delete();
		foreach ($subscription as $key => $value) {
            App\Models\MasterSubscription::create($value);
        }
    }
}
