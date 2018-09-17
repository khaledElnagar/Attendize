@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang("Organiser.organiser_coupons")
@stop

@section('page_title')
    @lang("Organiser.organiser_name_coupons", ["name"=>$event->name])
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
    <div class="col-md-9">
        <div class="btn-toolbar">
            <div class="btn-group btn-group-responsive">
                <a href="#" data-modal-id="CreateCoupon" data-href="{{route('showCreateCoupon', ['event_id' => @$event->id])}}" class="btn btn-success loadModal"><i class="ico-plus"></i> @lang("Coupon.create_coupon")</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        {!! Form::open(array('url' => route('showOrganiserCoupons', ['event_id'=>$event->id]), 'method' => 'get')) !!}
        <div class="input-group">
            <input name="q" value="{{$search['q'] or ''}}" placeholder="Search Events.." type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
        </div>
        <input type="hidden" name='sort_by' value="{{$search['sort_by']}}"/>
        {!! Form::close() !!}
    </div>
@stop

@section('content')

    @if($coupons->count())
        <div class="row">
            <div class="col-md-3 col-xs-6">
                <div class="order_options">
                    <span class="event_count">
                        @lang("Coupon.num_coupons", ["num" => $coupons->count()])
                    </span>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        @if($coupons->count())
            @foreach($coupons as $coupon)
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @include('ManageEvent.Partials.CouponPanel')
                </div>
            @endforeach
        @else
            @if($search['q'])
                @include('Shared.Partials.NoSearchResults')
            @else
                @include('ManageOrganiser.Partials.EventsBlankSlate')
            @endif
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! $coupons->appends(['q' =>$search['q'], 'past' => $search['showPast']])->render() !!}
        </div>
    </div>
@stop
