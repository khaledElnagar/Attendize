<?php

use Illuminate\Database\Seeder;
use App\Models\Coupon;
class CouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coupon::Create([
           'account_id'=>1,
           'organiser_id'=>1,
           'code'=>'ABC123',
            'type'=>'fixed',
            'value'=>30,
            'end_date'=>date('Y-m-d H:i:s'),
        ]);

        Coupon::Create([
            'account_id'=>1,
            'organiser_id'=>1,
            'code'=>'DEF456',
            'type'=>'percent',
            'percent_off'=>50,
            'end_date'=>date('Y-m-d H:i:s'),
        ]);
    }
}
