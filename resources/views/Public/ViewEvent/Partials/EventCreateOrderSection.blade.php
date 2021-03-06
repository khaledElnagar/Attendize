<section id='order_form' class="container">
    <div class="row">
        <h1 class="section_head">
            @lang("Public_ViewEvent.order_details")
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12" style="text-align: center">
            @lang("Public_ViewEvent.below_order_details_header")
        </div>
        <div class="col-md-4 col-md-push-8">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-cart mr5"></i>
                        @lang("Public_ViewEvent.order_summary")
                    </h3>
                </div>

                <div class="panel-body pt0">
                    <table class="table mb0 table-condensed">
                        @foreach($tickets as $ticket)
                        <tr>
                            <td class="pl0">{{{$ticket['ticket']['title']}}} X <b>{{$ticket['qty']}}</b></td>
                            <td style="text-align: right;">
                                @if((int)ceil($ticket['full_price']) === 0)
                                    @lang("Public_ViewEvent.free")
                                @else
                                {{ money($ticket['full_price'], $event->currency) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                @if($order_total > 0)
                    <div class="panel-footer" id="replacePart">


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
                    </div>
                @endif

            </div>
            <div class="help-block">
                @lang("Public_ViewEvent.time", ["time"=>"<span id='countdown'></span>"])
            </div>
        </div>
        <div class="col-md-8 col-md-pull-4">
            <div class="event_order_form">

                @if($account_payment_gateway->payment_gateway_id === 5) <!-- payfort -->
                    {!! Form::open(['url' => route('postCreateOrder', ['event_id' => $event->id]),'id'=>'payfort_fort_form' ,'class' => ($order_requires_payment && @$payment_gateway->is_on_site) ? '' : '']) !!}
                @else
                    {!! Form::open(['url' => route('postCreateOrder', ['event_id' => $event->id]), 'class' => ($order_requires_payment && @$payment_gateway->is_on_site) ? 'ajax payment-form' : 'ajax', 'data-stripe-pub-key' => isset($account_payment_gateway->config['publishableKey']) ? $account_payment_gateway->config['publishableKey'] : '']) !!}
                @endif

                {!! Form::hidden('event_id', $event->id) !!}

                <h3> @lang("Public_ViewEvent.your_information")</h3>

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            {!! Form::label("order_first_name", trans("Public_ViewEvent.first_name")) !!}
                            {!! Form::text("order_first_name", null, ['required' => 'required', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            {!! Form::label("order_last_name", trans("Public_ViewEvent.last_name")) !!}
                            {!! Form::text("order_last_name", null, ['required' => 'required', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label("order_email", trans("Public_ViewEvent.email")) !!}
                            {!! Form::text("order_email", null, ['required' => 'required', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="p20 pl0">
                    <a href="javascript:void(0);" class="btn btn-primary btn-xs" id="mirror_buyer_info">
                        @lang("Public_ViewEvent.copy_buyer")
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="ticket_holders_details" >
                            <h3>@lang("Public_ViewEvent.ticket_holder_information")</h3>
                            <?php
                                $total_attendee_increment = 0;
                            ?>
                            @foreach($tickets as $ticket)
                                @for($i=0; $i<=$ticket['qty']-1; $i++)
                                <div class="panel panel-primary">

                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <b>{{$ticket['ticket']['title']}}</b>: @lang("Public_ViewEvent.ticket_holder_n", ["n"=>$i+1])
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {!! Form::label("ticket_holder_first_name[{$i}][{$ticket['ticket']['id']}]", trans("Public_ViewEvent.first_name")) !!}
                                                    {!! Form::text("ticket_holder_first_name[{$i}][{$ticket['ticket']['id']}]", null, ['required' => 'required', 'class' => "ticket_holder_first_name.$i.{$ticket['ticket']['id']} ticket_holder_first_name form-control"]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {!! Form::label("ticket_holder_last_name[{$i}][{$ticket['ticket']['id']}]", trans("Public_ViewEvent.last_name")) !!}
                                                    {!! Form::text("ticket_holder_last_name[{$i}][{$ticket['ticket']['id']}]", null, ['required' => 'required', 'class' => "ticket_holder_last_name.$i.{$ticket['ticket']['id']} ticket_holder_last_name form-control"]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {!! Form::label("ticket_holder_email[{$i}][{$ticket['ticket']['id']}]", trans("Public_ViewEvent.email_address")) !!}
                                                    {!! Form::text("ticket_holder_email[{$i}][{$ticket['ticket']['id']}]", null, ['required' => 'required', 'class' => "ticket_holder_email.$i.{$ticket['ticket']['id']} ticket_holder_email form-control"]) !!}
                                                </div>
                                            </div>
                                            @include('Public.ViewEvent.Partials.AttendeeQuestions', ['ticket' => $ticket['ticket'],'attendee_number' => $total_attendee_increment++])

                                        </div>

                                    </div>


                                </div>
                                @endfor
                            @endforeach
                        </div>
                    </div>
                </div>

                <style>
                    .offline_payment_toggle {
                        padding: 20px 0;
                    }
                </style>

                @if($order_requires_payment)

                <h3>@lang("Public_ViewEvent.payment_information")</h3>
                    @lang("Public_ViewEvent.below_payment_information_header")
                @if($event->enable_offline_payments)
                    <div class="offline_payment_toggle">
                        <div class="custom-checkbox">
                            <input data-toggle="toggle" id="pay_offline" name="pay_offline" type="checkbox" value="1">
                            <label for="pay_offline">@lang("Public_ViewEvent.pay_using_offline_methods")</label>
                        </div>
                    </div>
                    <div class="offline_payment" style="display: none;">
                        <h5>@lang("Public_ViewEvent.offline_payment_instructions")</h5>
                        <div class="well">
                            {!! Markdown::parse($event->offline_payment_instructions) !!}
                        </div>
                    </div>

                @endif


                @if(@$payment_gateway->is_on_site)
                    <div class="online_payment">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('card-holder-name', trans("Public_ViewEvent.card_holder")) !!}
                                    <input required="required" type="text" autocomplete="off" id="card_holder" placeholder="{{ trans("Public_ViewEvent.card_holder")}}" class="form-control card-holder" size="20" data-stripe="number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('card-number', trans("Public_ViewEvent.card_number")) !!}
                                    <input required="required" type="text" autocomplete="off" id="card_number" placeholder="**** **** **** ****" class="form-control card-number" size="20" data-stripe="number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('card-expiry-month', trans("Public_ViewEvent.expiry_month")) !!}
                                    {!!  Form::selectRange('card-expiry-month',1,12,null, [
                                            'class' => 'form-control card-expiry-month',
                                            'data-stripe' => 'exp_month',
                                            'id' => 'exp_month',
                                        ] )  !!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('card-expiry-year', trans("Public_ViewEvent.expiry_year")) !!}
                                    {!!  Form::selectRange('card-expiry-year',date('Y'),date('Y')+10,null, [
                                            'class' => 'form-control card-expiry-year',
                                            'data-stripe' => 'exp_year',
                                            'id' => 'exp_year'
                                        ] )  !!}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('card-expiry-year', trans("Public_ViewEvent.cvc_number")) !!}
                                    <input required="required" placeholder="***" id='cvv' class="form-control card-cvc" data-stripe="cvc">
                                </div>
                            </div>
                        </div>
                    </div>

                @endif

                @endif

                @if($event->pre_order_display_message)
                <div class="well well-small">
                    {!! nl2br(e($event->pre_order_display_message)) !!}
                </div>
                @endif

               {!! Form::hidden('is_embedded', $is_embedded) !!}
               {!! Form::submit(trans("Public_ViewEvent.checkout_submit"), ['class' => 'btn btn-lg btn-success card-submit','id'=>'payfort_fort_pay_action', 'style' => 'width:100%;']) !!}

            </div>
        </div>
    </div>
</section>
@if(session()->get('message'))
    <script>showMessage('{{session()->get('message')}}');</script>
@endif


@if($account_payment_gateway->payment_gateway_id === 5) <!-- payfort -->
    <script src="/assets/javascript/js/jquery-3.1.1.js"></script>
    <!-- the checkout.js file contains all javascript related to processing the payment transaction -->
    <script src="/assets/javascript/js/checkout.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#payfort_fort_pay_action').on('click',function(e) {
                e.preventDefault();
                payfortFortMerchant.process();
            });
        });
    </script>
@endif
