<style>
    .control-label {
        padding-left: 27px;
    }
</style>

@if($customFields)
    <h5 class="col-12 pb-4 control-label">{!! trans('lang.main_fields') !!}</h5>
@endif

<div style="flex: 50%;max-width: 100%;padding: 0 4px;" class="column">
    <div class="row">
        <!-- Name Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('first name', trans("lang.user_name"), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder"), 'required']) !!}

            </div>
        </div>

        <!-- username Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('nickname', "Nickname", ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('nickname', null,  ['class' => 'form-control','placeholder'=>  trans('lang.last_name_placeholder'), 'required']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Country Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('name', trans('lang.country'), ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                <select name="country" id="country" data-show-content="true" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
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
                <select name="city" id="city" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
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
                {!! Form::text('email', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_email_placeholder"), 'required']) !!}
            </div>
        </div>

        <!-- Password Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('password', trans("lang.user_password"), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::password('password', ['class' => 'form-control','placeholder'=>  trans("lang.user_password_placeholder")]) !!}
            </div>
        </div>
        <!-- Roles Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('roles[]', trans("lang.user_role_id"),['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::select('roles[]', $role, $rolesSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple','placeholder'=>trans('lang.user_role_id_placeholder')]) !!}
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
        {!! Form::label('avatar', trans("lang.user_avatar"), ['class' => 'col-md-3 control-label', 'style' => 'font-size:15px']) !!}
            <div class="col-md-9">
                <!-- $FIELD_NAME_TITLE$ Field -->
                <div class="row">
                    <div class="box">
                        <div class="content">

                <!-- Custom File Uploader  -->

                    <div class="left">
                        @if(Request::is('*edit'))
                            @if($user->avatar != null ) 
                                <img id="img-uploaded" class="img2" src="{{asset('storage/Avatar' . "/" . $user->avatar)}}" alt="your image" />
                            @endif
                        @else
                            <img id="img-uploaded" class="img2" src="{{asset('storage/Avatar/avatar.png')}}" alt="your image" />
                        @endif
                      
                    </div>

                     <div class="right">

                      <input type="text" class="img-path newimg" placeholder="Image Path">
                      <span class="file-wrapper">
                      <input type="file" name="avatar" id="imgInp" class="uploader newimg"  />
                      <span class="btn2 btn-large btn-alpha">Upload Image</span>
                     </span>
                   </div>
            </div>
        </div>
    </div>
        </div>
    </div>
    

    <div class="form-group row col-md-6">
                        {!! Form::label('description', trans("lang.category_description"), ['class' => 'col-3 control-label']) !!}
                        <div class="col-9">
                            {!! Form::textarea('description', null, ['class' => 'form-control','style' => 'height: 150px;', 'placeholder'=>
                            trans("lang.category_description_placeholder")  ]) !!}
                            <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
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


                var url = _this.closest('.select_cities').data('route');
                //  console.log(url);



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