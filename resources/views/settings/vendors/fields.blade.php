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
            {!! Form::label('first name', trans("lang.user_name").'*', ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.user_name_placeholder"), 'required']) !!}

            </div>
        </div>

        <!-- username Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('nickname', trans("lang.businessName") . '*', ['class' => 'col-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('nickname', null,  ['class' => 'form-control','placeholder'=>  trans('lang.last_name_placeholder'), 'required']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Country Field -->
        <div class="form-group col-md-6 row">
            {!! Form::label('name', trans('lang.country').'*', ['class' => 'col-3 control-label']) !!}
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
            {!! Form::label('City', trans('lang.city').'*', ['class' => 'col-3 control-label']) !!}
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
            {!! Form::label('password', trans("lang.user_password").'*', ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::password('password', ['class' => 'form-control','placeholder'=>  trans("lang.user_password_placeholder")]) !!}
            </div>
        </div>

        <!-- phone Field -->
        <div class="form-group col-md-6 row">

        {!! Form::label('phone', trans("lang.phone"), ['class' => 'col-md-3 control-label']) !!}

        @if(Request::is('*edit') || Request::is('*create'))
        <div class="form-group col-md-8 row">
            <div class="col-md-12">
                <select name="countries_code" id="countries_code" data-show-content="true" aria-controls="dataTableBuilder" class="form-control form-control-sm" style="margin-top: 1px; height: 36px;">

                @foreach($countries_codes as $countries_code)
                  <option value="{{ $countries_code['prefix'] }}"
                  @if(Request::is('*edit')) @if($user->country_prefix == $countries_code['prefix']) selected @endif @endif>
                  {{$countries_code['name']}} {{ $countries_code['prefix'] }}
                  </option>
                @endforeach
                </select>
            </div>
        </div>
        @endif
        @if(!(Request::is('*edit') || Request::is('*create')))
        <div class="col-md-9">
                {!! Form::text('phone',null , ['class' => 'form-control phone','placeholder'=>  "Insert phone number",'id' =>'phone']) !!}
        </div>
        @else
        <div class="col-md-9">
                {!! Form::text('phone',null , ['class' => 'form-control phone','placeholder'=>  "Insert phone number", 'style' => 'position: relative; left: 121px; top: -5px;',
                'id' =>'phone']) !!}
            </div>
        @endif
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
        <div class="form-group col-md-6 row">
            {!! Form::label('owner_name', trans('lang.ownername'), ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::text('owner_name', null ,['class' => 'form-control','placeholder'=>  "insert owner name"]) !!}
            </div>
        </div>
        <!-- Roles Field -->
    @can('permissions.index')
        <!-- Roles Field -->
            <div class="form-group col-md-12 row">
                <div class="form-group col-md-6 row">
                    {!! Form::label('Language', trans('lang.lang'), ['class' => 'col-3 control-label']) !!}
                    <div class="col-9" >

                        <select name="language" id="language" data-show-content="true" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
                            <option value="en" @if(Request::is('*edit')) @if($user->language=='en') selected @endif  @endif >Speaking English</option>

                            <option value="ar"  @if(Request::is('*edit')) @if($user->language=='ar') selected @endif @endif >  يتحدث العربية </option>
                        </select>

                        <div class="form-text text-muted">
                            Language
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        <div class="form-group row col-md-6">
            {!! Form::label('description', trans("lang.vendor_about"), ['class' => 'col-3 control-label']) !!}
            <div class="col-9">
                {!! Form::textarea('description', null, ['class' => 'form-control','style' => 'height: 150px;', 'placeholder'=>
                trans("lang.category_description_placeholder")  ]) !!}

                <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
            </div>
        </div>
        <div class="form-group row col-md-6">
            {!! Form::label('caption', trans("lang.caption"), ['class' => 'col-3 control-label']) !!}
            <div class="col-9">
                {!! Form::textarea('caption', null, ['class' => 'form-control','style' => 'height: 150px;', 'placeholder'=>
                trans("lang.category_description_placeholder")  ]) !!}

                <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
            </div>
        </div>

        <div class="form-group row col-md-6">
            {!! Form::label('facebook', trans("lang.facebook_link"), ['class' => 'col-3 control-label']) !!}
            <div class="col-9">
              {!! Form::text('facebook', null ,['class' => 'form-control','placeholder'=>  "Insert Facebook link"]) !!}
            </div>
        </div>
        <div class="form-group row col-md-6">
            {!! Form::label('instagram', trans("lang.instagram_link"), ['class' => 'col-3 control-label']) !!}
            <div class="col-9">
              {!! Form::text('instagram', null ,['class' => 'form-control','placeholder'=>  "Insert Instagram link"]) !!}
            </div>
        </div>

    </div>

    <div class="row">


        <div class="col-lg-12">
            <div class="div-center">
<?php
                if(Request::is('*edit'))
               $subarray=$user->subcategories->transform(function($q){return $q->id;})
                ?>

                <input id="subcategorySelected" name="subcategorySelected" value="" type="text"
                        style="display: none">
                <h2>select categories</h2>
                <select name="langOptgroup[]" multiple="" id="langOptgroup" >
                    @forelse($categories as $caregory)
                        <optgroup label="{{$caregory->name}}">
                            @forelse( $caregory->subCategory as $subCategory)
                                <option value="{{$subCategory->id}}" @if(Request::is('*edit'))@if(in_array($subCategory->id,$subarray->toArray())) selected @endif @endif >{{$subCategory->name}}</option>

                            @empty
                            @endforelse

                        </optgroup>

                        @empty
                        @endforelse
{{--                    <optgroup label="Programming Languages">--}}
{{--                        <option value="C++ / C#">C++ / C#</option>--}}
{{--                        <option value="Java">Java</option>--}}
{{--                        <option value="Objective-C">Objective-C</option>--}}
{{--                    </optgroup>--}}
{{--                    <optgroup label="Client-side scripting Languages">--}}
{{--                        <option value="JavaScript">JavaScript</option>--}}
{{--                    </optgroup>--}}
{{--                    <optgroup label="Server-side scripting Languages">--}}
{{--                        <option value="Perl">Perl</option>--}}
{{--                        <option value="PHP">PHP</option>--}}
{{--                        <option value="Ruby on Rails">Ruby on Rails</option>--}}
{{--                    </optgroup>--}}
{{--                    <optgroup label="Mobile Platforms">--}}
{{--                        <option value="Android">Android</option>--}}
{{--                        <option value="iOS">iOS</option>--}}
{{--                    </optgroup>--}}
{{--                    <optgroup label="Document Markup Languages">--}}
{{--                        <option value="HTML">HTML</option>--}}
{{--                        <option value="XML">XML</option>--}}
{{--                    </optgroup>--}}
                </select>

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


<!--  -->
@if(Request::is('*create') || Request::is('*edit'))
<div class="form-group row col-md-6">
    {!! Form::label('map', trans('lang.SP_location'), ['class' => 'col-md-12 control-label', 'style' => 'font-size:15px']) !!}
    <div id="gmMapLocation" style="width:500px; height: 300px; margin-left: 20px;">
        {!! Mapper::render() !!}
    </div>

    @if(Request::is('*edit'))
    <input type="text" name="latitude" id="latitude" value="{{ $user->coordinates != null ? $user->coordinates->latitude : 36.216667 }}" style="display:none">
    <input type="text" name="longitude" id="longitude" value="{{ $user->coordinates != null ? $user->coordinates->longitude : 37.166668 }}" style="display:none">
    @else
    <input type="text" name="latitude" id="latitude" value="36.216667" style="display:none">
    <input type="text" name="longitude" id="longitude" value="37.166668" style="display:none">
    @endif


</div>
@endif
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

        <script>
            $(document).ready(function () {
                $('#countries_code').on('change',function(){
                    $('.phone').val('');
                    $('.phone').val($('#countries_code').val());
                });
            });
        </script>
        <script>



            $('#langOptgroup').multiselect({
                columns: 2,
                placeholder: 'Select Categories',
                search: true,
                selectAll: true
            });
        </script>



    @endprepend





    <div id="gmap" {!! $style !!}>
        {!! Mapper::render() !!}
    </div>

    <div class="row">
<div class="col-md-12">
        <div class="range-head">
            <div class="range-values">
                <span class="r-0">0:00</span>
                <span class="r-3">3:00</span>
                <span class="r-6">6:00</span>
                <span class="r-9">9:00</span>
                <span class="r-12">12:00</span>
                <span class="r-15">15:00</span>
                <span class="r-18">18:00</span>
                <span class="r-21">21:00</span>
                <span class="r-24">24:00</span>
            </div>
        </div>

        <div class="range-day" id="range-day-1" data-day="1">
            <input type="checkbox" name="day-1" id="day-1" value="1" class="range-checkbox" checked>
            <label for="day-1" class="range-label">Monday:</label>
            <div id="range-slider-1" class="range-slider"></div>
            <span id="range-time-1" class="range-time"></span>
        </div>

        <div class="range-day" id="range-day-2" data-day="2">
            <input type="checkbox" name="day-2" id="day-2" value="1" class="range-checkbox" checked>
            <label for="day-2" class="range-label">Tuesday:</label>
            <div id="range-slider-2" class="range-slider"></div>
            <span id="range-time-2" class="range-time"></span>
        </div>

        <div class="range-day" id="range-day-3" data-day="3">
            <input type="checkbox" name="day-3" id="day-3" value="1" class="range-checkbox" checked>
            <label for="day-3" class="range-label">Wednesday:</label>
            <div id="range-slider-3" class="range-slider"></div>
            <span id="range-time-3" class="range-time"></span>
        </div>

        <div class="range-day" id="range-day-4" data-day="4">
            <input type="checkbox" name="day-4" id="day-4" value="1" class="range-checkbox" checked>
            <label for="day-4" class="range-label">Thursday‎:</label>
            <div id="range-slider-4" class="range-slider"></div>
            <span id="range-time-4" class="range-time"></span>
        </div>

        <div class="range-day" id="range-day-5" data-day="5">
            <input type="checkbox" name="day-5" id="day-5" value="1" class="range-checkbox" checked>
            <label for="day-5" class="range-label">Friday:</label>
            <div id="range-slider-5" class="range-slider"></div>
            <span id="range-time-5" class="range-time"></span>
        </div>

        <div class="range-day" id="range-day-6" data-day="6">
            <input type="checkbox" name="day-6" id="day-6" value="1" class="range-checkbox" checked>
            <label for="day-6" class="range-label">Saturday:</label>
            <div id="range-slider-6" class="range-slider"></div>
            <span id="range-time-6" class="range-time"></span>
        </div>

        <div class="range-day" id="range-day-7" data-day="7">
            <input type="checkbox" name="day-7" id="day-7" value="1" class="range-checkbox">
            <label for="day-7" class="range-label">Sunday:</label>
            <div id="range-slider-7" class="range-slider"></div>
            <span id="range-time-7" class="range-time"></span>
        </div>

        <br style="clear:both">
    </div>
    </div>
        <div class="row">
            <input type="text" id="dayWorkingHours" value="">
    <div class="col-md-12" id="scheduleTable">

    </div>
    </div>
    <!-- Submit Field -->
    <div class="form-group col-12 text-right">
        <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} </button>
        <a href="{!! route('vendors.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
    </div>
