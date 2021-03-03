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
            {!! Form::label('nickname', trans("lang.nickname"), ['class' => 'col-3 control-label']) !!}
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
                    <option value="0" @if(Request::is('*create')) selected @endif> Select</option>
{{--                    <option data-content="<i class='france flag'></i> Eye"></option>--}}
{{--                    <option value="email"><i class="fa fa-edit"></i> Email</option>--}}


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

        <!-- phone Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('phone', trans("lang.phone"), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('phone',null , ['class' => 'form-control','placeholder'=>  "Insert phone number"]) !!}
            </div>
        </div>

        <!-- website Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('website', trans('lang.website'), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::url('website',null ,['class' => 'form-control','placeholder'=>  "Insert website url"]) !!}
            </div>
        </div>

        <!-- address Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('address', trans('lang.address'), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('address', null ,['class' => 'form-control','placeholder'=>  "Insert physical address"]) !!}
            </div>
        </div>
        <!-- Roles Field -->
    @can('permissions.index')
        <!-- Roles Field -->
            <div class="form-group col-md-6 row">
                    <div class="form-group col-md-12 row">
                        {!! Form::label('Language', trans('lang.lang'), ['class' => 'col-3 control-label']) !!}
                        <div class="col-9" >
                            {!! Form::text('language', null,  ['class' => 'form-control','placeholder'=> trans("lang.language_placeholder") ,'required']) !!}
                            <div class="form-text text-muted">
                                Language
                            </div>
                        </div>
                    </div>

                <div class="col-md-12 row">
                {!! Form::label('roles[]', trans("lang.user_role_id"),['class' => 'col-3 control-label']) !!}
                <div class="col-9">
                    {!! Form::select('roles[]', $role, $rolesSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple','placeholder'=>trans('lang.user_role_id_placeholder')]) !!}
                </div>
                </div>
            </div>
        @endcan
        <div class="form-group row col-md-6">
            {!! Form::label('description', trans("lang.category_description"), ['class' => 'col-3 control-label']) !!}
            <div class="col-9">
                {!! Form::textarea('description88', null, ['class' => 'form-control','style' => 'height: 150px;', 'placeholder'=>
                trans("lang.category_description_placeholder")  ]) !!}

                <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
            </div>
        </div>

    </div>
    <div class="row">
        <!-- Language Field -->
        <!-- $FIELD_NAME_TITLE$ Field -->

        <div class="form-group row col-md-6">
        {!! Form::label('avatar', trans("lang.avatar"), ['class' => 'col-md-3 control-label', 'style' => 'font-size:15px']) !!}
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


</div>

    @prepend('scripts')
        <script>
            $(document).ready(function(){
                $(".hidden-content").hide();
                $(".view").on('click', function(){

                    $(this).parents().parents().find(".hidden-content").slideToggle(500).toggleClass("active");

                    if($(this).parents().parents().siblings().find(".hidden-content").hasClass('active')){
                        $(this).parents().parents().siblings().find(".hidden-content").removeClass('active');
                        $(this).parents().parents().siblings().find(".hidden-content").hide();
                    }
                });
            });

        </script>
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function () {
            var _this;


            $('#country').on('change',function(){
                var _this = $(this);
                $('#city').empty();
                var data = "id="+_this.val();

                var url = _this.closest('.card-body').data('route');


               // var url = _this.closest('.select_cities').data('route');
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
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} </button>
    <a href="{!! route('vendors.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
