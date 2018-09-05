<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends MyBaseModel
{
    public static function findByCode($code)
    {
        return self::where('code',$code)->where('end_date','<=',date('Y-m-d'))->where('isAcive',1)->first();
    }

    public function organiser()
    {
        return $this->belongsTo(\App\Models\Organiser::class);
    }


    public function discount($total)
    {
        if($this->type == 'fixed')
            return $this->value;
        elseif ($this->type == 'percent')
            return ($this->percent_off/100) * $total;

        return 0;
    }
}
