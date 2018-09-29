<div class="panel panel-success event">
    <div class="panel-heading" data-style="background-color: {{{$coupon->bg_color}}};background-image: url({{{$coupon->bg_image_url}}}); background-size: cover;">
        <ul class="event-meta">
            <li class="event-title">
                <a title="{{{$coupon->code}}}" href="#">
                    {{{ str_limit($coupon->code, $limit = 75, $end = '...') }}} /  <strong>@if($coupon->is_active) Active @else Not active @endif</strong>
                </a>
            </li>
            <li class="event-organiser">
                By <a href='#'>{{{$coupon->organiser->name}}}</a>
            </li>
            <li class="end-date">
                Ends at <strong>{{{$coupon->end_date}}}</strong>

            </li>
        </ul>

    </div>

    <div class="panel-body">
        <ul class="nav nav-section nav-justified mt5 mb5">
            <li>
                <div class="section">
                </div>
            </li>

            <li>
                <div class="section">
                </div>
            </li>
        </ul>
    </div>


    <div class="panel-footer">
        <ul class="nav nav-section nav-justified">
            <li>
                <a href="#" data-modal-id="EditCoupon" data-href="{{route('showEditCoupon', ['coupon_id' => @$coupon->id,'event_id'=>$event->id])}}" class="loadModal">
                    <i class="ico-edit"></i> @lang("basic.edit")
                </a>
            </li>
        </ul>
    </div>
</div>