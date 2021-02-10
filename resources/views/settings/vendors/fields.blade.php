@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 100%;padding: 0 4px;" class="column">
    <div class="row">
        <!-- Name Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('first name', trans("lang.user_name"), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder"),'required']) !!}
                
            </div>
        </div>

        <!-- username Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('last name', trans("lang.last_name"), ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('last_name', null,  ['class' => 'form-control','placeholder'=>  trans('lang.last_name_placeholder'),'required']) !!}
            </div>
        </div>
    </div>
   
    <div class="row">
        <!-- Country Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('name', trans('lang.country'), ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                <select name="country" id="country" data-show-content="true" aria-controls="dataTableBuilder" class="form-control form-control-sm">
                    <option value="0" selected> Select</option>
                    <option data-content="<i class='france flag'></i> Eye"></option>
                    <option value="email"><i class="fa fa-edit"></i> Email</option>


                @foreach($countries as $country)

                        <option value="{{ $country->id }}"  @if(!empty($user->city_id)) @if($country->id==$user->cities->country_id) selected @endif @endif>
                            {{ $country->country_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- City Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('City', trans('lang.city'), ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                <select name="city" id="city" aria-controls="dataTableBuilder" class="form-control form-control-sm">
                    <option value="0" >select</option>
                    @if(!empty($cities))
                        @foreach($cities as $city)
                            <option value={{$city->id}} @if(!empty($user->city_id)) @if($city->id==$user->city_id) selected @endif @endif>
                                {{$city->city_name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Email Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('email', trans("lang.user_email"), ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('email', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_email_placeholder")]) !!}
            </div>
        </div>

        <!-- Password Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('password', trans("lang.user_password"), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::password('password', ['class' => 'form-control','placeholder'=>  trans("lang.user_password_placeholder")]) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Language Field -->
        <div class="form-group row col-md-6">
            {!! Form::label('Language', 'Lang', ['class' => 'col-3 control-label']) !!}
            <div class="col-9">
                {!! Form::text('language', null,  ['class' => 'form-control','placeholder'=> trans("lang.language_placeholder") ,'required']) !!}
                <div class="form-text text-muted">
                    Language
                </div>
            </div>
        </div>
        <!-- $FIELD_NAME_TITLE$ Field -->
        <div class="form-group row col-md-6">
        {!! Form::label('password', trans("lang.user_avatar"), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                <div class="image-upload-one">
                <div class="center">
                    <div class="form-input">
                        <label for="file-ip-1">
                            <img id="file-ip-1-preview" src="https://i.ibb.co/ZVFsg37/default.png">
                            <button type="button" class="imgRemove" onclick="myImgRemoveFunctionOne()"></button>
                        </label>
                        <input type="file" name="avatar" id="file-ip-1" accept="image/*" onchange="showPreviewOne(event);">
                    </div>
                    <small class="small">Use the ↺ icon to reset the image</small>
                </div>
            </div>
        </div>
    </div>
</div>
    
    @prepend('scripts')
   
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function () {
            var _this;
            $('#country').on('change',function(){
                var _this = $(this);
                $('#city').empty();
                var data = "id="+_this.val();
                var url = _this.closest('.card-body').data('route');
                $.post(url , data , function(res){
                    $city=['<option value="0" selected="">select </option>'];
                    for(var i=0 ;i<res.length;i++) {
                        $city.push('<option value="' + res[i]['id'] + '">' + res[i]['city_name'] + '</option>');
                        // $categoryslt.push('<li data-value="' + res[i]['id'] + '" class="option null selected">' + res[i]['name1'] + '</li>');
                    }
                    $('#city').empty();
                    $('#city').append($city);
                });
            });
        });
    </script>
    @endprepend





<div id="gmap" {!! $style !!}>
    {!! Mapper::render() !!}
</div>

<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.user')}}</button>
    <a href="{!! route('vendors.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>


