<?php

namespace App\Http\Controllers;

use App\Coupon;
use Illuminate\Http\Request;

use App\Http\Requests;

class CouponsController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$event_id)
    {
        $coupon = Coupon::where('code',$request->get('coupon_code'))->first();
        if(!$coupon)
        {
            session()->flash('message', 'Whoops! Invalid coupon code. Please try again.');
            return redirect()->back();
        }

        $ticket_order = session()->get('ticket_order_' . $event_id);

        session()->put('coupon',[
            'name'=>$coupon->code,
            'discount'=>$coupon->discount($ticket_order['order_total']),
        ]);

        session()->flash('message', 'Success! Coupon has been applied!');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
