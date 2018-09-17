@if($order_total > 0)
    @if(session()->has('coupon'))
        <h5>
            @lang("Public_ViewEvent.discount")({{session()->get('coupon')['name']}}):
            <span style="float: right;"><b>- {{ money(session()->get('coupon')['discount'],$event->currency) }}</b></span>
        </h5>
    @endif

    <h5>
        @lang("Public_ViewEvent.total"): <span style="float: right;"><b>{{ $orderService->getOrderTotalWithBookingFee(true) }}</b></span>
    </h5>

    @if($event->organiser->charge_tax)
        <h5>
            {{ $event->organiser->tax_name }} ({{ $event->organiser->tax_value }}%):
            <span style="float: right;"><b>{{ $orderService->getTaxAmount(true) }}</b></span>
        </h5>
        <h5>
            <strong>Grand Total:</strong>
            <span style="float: right;"><b>{{  $orderService->getGrandTotal(true) }}</b></span>
        </h5>
    @endif

    @if(!session()->has('coupon'))
        <h5>@lang("Public_ViewEvent.applyCoupon")</h5>
        <form action="{{route('coupon.store', ['event_id' => $event->id])}}" method="post" class="ajax">
            {{csrf_field()}}
            <input type="text" placeholder="@lang("Public_ViewEvent.coupon")" name="coupon_code">
            <button type="submit" class="button button-plain inline">Apply</button>
        </form>
    @else
        <h5>@lang("Public_ViewEvent.removeCoupon")</h5>
        <form action="{{route('coupon.destroy',['event_id' => $event->id])}}" method="post" class="ajax">
            {{csrf_field()}}
            {{method_field('delete')}}
            <button type="submit" class="btn btn-event-link">@lang("Public_ViewEvent.remove")</button>
        </form>
    @endif
@endif
