<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Organiser;
use Log;
use Auth;
use Validator;

use App\Http\Requests;

class CouponsController extends MyBaseController
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
            'id'=>$coupon->id,
            'name'=>$coupon->code,
            'discount'=>$coupon->discount($ticket_order['total_booking_fee']),
        ]);

        session()->flash('message', 'Success! Coupon has been applied!');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        session()->forget('coupon');

        session()->flash('message', 'Coupon has been removed!');
        return redirect()->back();
    }


    public function showCouponsList(Request $request,$organiser_id)
    {
        $organiser = Organiser::scope()->findOrfail($organiser_id);

        $allowed_sorts = ['created_at', 'end_date', 'isActive', 'type', 'value', 'percent', 'code'];

        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at');

        $coupons = $searchQuery
            ? Coupon::scope()->where('code', 'like', '%' . $searchQuery . '%')->orderBy($sort_by,
                'desc')->where('organiser_id', '=', $organiser_id)->paginate(12)
            : Coupon::scope()->where('organiser_id', '=', $organiser_id)->orderBy($sort_by, 'desc')->paginate(12);

        $data = [
            'coupons'    => $coupons,
            'organiser' => $organiser,
            'search'    => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => $request->get('sort_by') ? $request->get('sort_by') : '',
                'showPast' => $request->get('past'),
            ],
        ];


        return view('ManageOrganiser.Coupons', $data);

    }

    public function showCreateCoupon(Request $request)
    {
        $data = [
            'modal_id'     => $request->get('modal_id'),
            'organisers'   => Organiser::scope()->lists('name', 'id'),
            'organiser_id' => $request->get('organiser_id') ? $request->get('organiser_id') : false,
        ];

        return view('ManageOrganiser.Modals.CreateCoupon', $data);
    }


    public function postCreateCoupon(Request $request)
    {
        $coupon = Coupon::createNew();

        if (!$coupon->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $coupon->errors(),
            ]);
        }

        $coupon->code = $request->get('code');

        if($request->get('type')=='fixed')
            $coupon->value = strip_tags($request->get('value'));
        elseif($request->get('type')=='percent')
            $coupon->percent_off = strip_tags($request->get('value'));

        $coupon->type = strip_tags($request->get('type'));
        $coupon->organiser_id = $request->get('organiser_id');
        $coupon->is_active = 1;
        $coupon->end_date = $request->get('end_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('end_date')) : null;


        try {
            $coupon->save();
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'status'   => 'error',
                'messages' => trans("Controllers.coupon_create_exception"),
            ]);
        }

        return response()->json([
            'status'      => 'success',
            'id'          => $coupon->id,
            'redirectUrl' => route('showOrganiserCoupons', [
                'organiser_id'  => $coupon->organiser_id,
                'first_run' => 'yup',
            ]),
        ]);
    }

    public function showEditCoupon(Request $request,$coupon_id)
    {
        $data = [
            'modal_id'     => $request->get('modal_id'),
            'organisers'   => Organiser::scope()->lists('name', 'id'),
            'organiser_id' => $request->get('organiser_id') ? $request->get('organiser_id') : false,
            'coupon'=> Coupon::find($coupon_id)
        ];

        return view('ManageOrganiser.Modals.EditCoupon', $data);
    }


    public function postEditCoupon(Request $request,$coupon_id)
    {
        $coupon = Coupon::find($coupon_id);
        $coupon->rules['code']= ['required','unique:coupons,code,'.$coupon->id];

        if (!$coupon->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $coupon->errors(),
            ]);
        }

        $coupon->code = $request->get('code');

        if($request->get('type')=='fixed')
            $coupon->value = strip_tags($request->get('value'));
        elseif($request->get('type')=='percent')
            $coupon->percent_off = strip_tags($request->get('value'));

        $coupon->type = strip_tags($request->get('type'));
        $coupon->is_active = $request->get('is_active');
        $coupon->end_date = $request->get('end_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('end_date')) : null;


        try {
            $coupon->save();
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'status'   => 'error',
                'messages' => trans("Controllers.coupon_create_exception"),
            ]);
        }

        return response()->json([
            'status'      => 'success',
            'id'          => $coupon->id,
            'redirectUrl' => route('showOrganiserCoupons', [
                'organiser_id'  => $coupon->organiser_id,
                'first_run' => 'yup',
            ]),
        ]);
    }

}
