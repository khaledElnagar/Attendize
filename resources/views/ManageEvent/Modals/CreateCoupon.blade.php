<div role="dialog"  class="modal fade" style="display: none;">

    @include('ManageOrganiser.Partials.EventCreateAndEditJS');

    {!! Form::open(array('url' => route('postCreateCoupon',['event_id'=>$event_id]), 'class' => 'ajax gf')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-calendar"></i>
                    @lang("Coupon.create_coupon")</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('code', trans("Coupon.code"), array('class'=>'control-label required')) !!}
                            {!!  Form::text('code', Input::old('code'),array('class'=>'form-control','placeholder'=>trans("Coupon.code")))  !!}
                        </div>

                        <div class="form-group custom-theme">
                            {!! Form::label('type', trans("Coupon.type"), array('class'=>'control-label ')) !!}
                            {!! Form::select('type', ['fixed'=>trans("Coupon.fixed"),'percent'=>trans("Coupon.percent")], null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('value', trans("Coupon.value"), array('class'=>'control-label required')) !!}
                                    {!!  Form::text('value', Input::old('value'),array('class'=>'form-control','placeholder'=>trans("Coupon.value")))  !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('end_date', trans("Coupon.coupon_end_date"), array('class'=>'required control-label')) !!}
                                    {!!  Form::text('end_date', Input::old('end_date'),
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
                                    {!! Form::label('limit_number', trans("Coupon.limit_number"), array('class'=>'control-label required')) !!}
                                    {!!  Form::text('limit_number', Input::old('limit_number'),array('class'=>'form-control','placeholder'=>trans("Coupon.limit_number")))  !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                {!! Form::hidden('event_id', $event_id) !!}
            <div class="modal-footer">
                <span class="uploadProgress"></span>
                {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit(trans("Coupon.create_coupon"), ['class'=>"btn btn-success"]) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
