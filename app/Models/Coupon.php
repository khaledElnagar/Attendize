<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends MyBaseModel
{
    public $rules = [
        'code'  => ['required','unique:coupons'],
        'type'  => ['required'],
        'end_date' => ['required'],
        'value'   => ['required'],
    ];

    /**
     * The validation error messages.
     *
     * @var array $messages
     */
    protected $messages = [
        'code.required'                       => 'You must at least give a code for your coupon.',
        'type.required'                       => 'You must at least give a type for your coupon.',
        'end_date.required'                   => 'You must at least give a end date for your coupon.',
        'value.required'                       => 'You must at least give a value for your coupon.',
    ];


    public static function findByCode($code,$event_id)
    {
        return self::where('code',$code)->where('end_date','>=',date('Y-m-d H:i:s'))->where('is_active',1)->where('event_id',$event_id)->first();
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
