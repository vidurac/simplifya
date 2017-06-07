<?php

use Illuminate\Database\Seeder;

class MasterCouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coupons =[
            [
                'code'=> 'PERFECTFX',
                'description'=> '10% Off for the first month',
                'start_date'=> '2017-01-06',
                'end_date'=> '2017-05-06',
                'master_subscription_id'=> 2,
            ],
            [
                'code'=> 'EDSFRTYGF',
                'description'=> '$50 Off for Six months plan',
                'start_date'=> '2017-01-06',
                'end_date'=> '2017-05-06',
                'master_subscription_id'=> 2,
            ],

        ];

        DB::table('coupons')->delete();
        foreach ($coupons as $key => $value) {
            App\Models\Coupon::create($value);
        }
    }
}
