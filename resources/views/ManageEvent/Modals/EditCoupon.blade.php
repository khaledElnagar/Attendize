<div role="dialog"  class="modal fade" style="display: none;">

    @include('ManageOrganiser.Partials.EventCreateAndEditJS');

    {!! Form::open(array('url' => route('postEditCoupon',['coupon_id'=>$coupon->id]), 'class' => 'ajax gf')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-calendar"></i>
                    @lang("Coupon.edit_coupon")</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('code', trans("Coupon.code"), array('class'=>'control-label required')) !!}
                            {!!  Form::text('code', $coupon->code,array('class'=>'form-control','placeholder'=>trans("Coupon.code")))  !!}
                        </div>

                        <div class="form-group custom-theme">
                            {!! Form::label('type', trans("Coupon.type"), array('class'=>'control-label ')) !!}
                            {!! Form::select('type', ['fixed'=>trans("Coupon.fixed"),'percent'=>trans("Coupon.percent")], $coupon->type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('value', trans("Coupon.value"), array('class'=>'control-label required')) !!}
                                    @if($coupon->type == 'fixed')
                                        {!!  Form::text('value', $coupon->value,array('class'=>'form-control','placeholder'=>trans("Coupon.value")))  !!}
                                    @else
                                        {!!  Form::text('value', $coupon->percent_off,array('class'=>'form-control','placeholder'=>trans("Coupon.value")))  !!}
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('end_date', trans("Coupon.coupon_end_date"), array('class'=>'required control-label')) !!}
                                    {!!  Form::text('end_date', $coupon->getFormattedDate('end_date'),
                                                        [
                                                    'class'=>'form-control start hasDatepicker ',
                                                    'data-field'=>'datetime',
                                                    'data-startend'=>'start',
                                                    'data-startendelem'=>'.end',
                                                    'readonly'=>''
                                                ])  !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::hidden('is_active', 0) !!}
                                    {!! Form::label('is_active', trans("Coupon.is_active"), array('class'=>'control-label required')) !!}
                                    <input name="is_active" type="checkbox" value="1" id="is_active" @if($coupon->is_active == 1) checked @endif >
                                </div>
                            </div>
                             <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('limit_number', trans("Coupon.limit_number"), array('class'=>'control-label required')) !!}
                                    {!!  Form::text('limit_number', $coupon->limit_number,array('class'=>'form-control','placeholder'=>trans("Coupon.limit_number")))  !!}
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="uploadProgress"></span>
                {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit(trans("Coupon.update_coupon"), ['class'=>"btn btn-success"]) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
