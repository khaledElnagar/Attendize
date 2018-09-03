<?php

use Illuminate\Database\Seeder;
use App\Coupon;
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
           'code'=>'ABC123',
            'type'=>'fixed',
            'value'=>30,
        ]);

        Coupon::Create([
           'code'=>'DEF456',
            'type'=>'percent',
            'percent_off'=>50,
        ]);
    }
}
