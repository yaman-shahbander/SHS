@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif

<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans("lang.delegate_name"), ['class' => 'col-4 control-label text-right']) !!}
        <div class="col-8">
            {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder"),'required']) !!}
            <div class="form-text text-muted">
                 Name
            </div>
        </div>
    </div>

     <!-- username Field -->
    <div class="form-group row ">
            {!! Form::label('phone', 'Phone', ['class' => 'col-4 control-label text-right']) !!}
            <div class="col-8">
                {!! Form::text('phone', null,  ['class' => 'form-control','placeholder'=>  'phone','required']) !!}
                <div class="form-text text-muted">
                phone
                </div>
            </div>
    </div>
     <!-- balance Field -->
     <div class="form-group row ">
            {!! Form::label('balance', 'Balance', ['class' => 'col-4 control-label text-right']) !!}
            <div class="col-8">
                {!! Form::text('balance', null,  ['class' => 'form-control','placeholder'=>  'Balance','required']) !!}
                <div class="form-text text-muted">
                balance
                </div>
            </div>
    </div>
</div>
 
@if($customFields)
    {{--TODO generate custom field--}}
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.user')}}</button>
    <a href="{!! route('users.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>