<div style="flex: 50%;padding: 0 4px;" class="column">

        <!-- Select type-->
        <div class="form-group row ">
        <div class="col-6 row">
            {!! Form::label('type', "Type", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="type" aria-controls="dataTableBuilder" class="form-control form-control-sm">
               <option value="1">Service Providers</option>
               <option value="2">Home Owners</option>
            </select>
            </div>
            </div>
        </div>

        <!-- Select country-->
        <div class="form-group row ">
        <div class="col-6 row">
            {!! Form::label('country', "Country", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="country" id="country" aria-controls="dataTableBuilder" class="form-control form-control-sm">
            <option value="0">select</option>
              @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->country_name }}</option>
              @endforeach
            </select>
            </div>
            </div>
            <div class="col-6 row">
            {!! Form::label('city', "City", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="city" id="city" aria-controls="dataTableBuilder" class="form-control form-control-sm">
            </select>
            </div>
            </div>
        </div>

        <!-- Select city-->

      <!-- Select category-->
      <div class="form-group row ">
        <div class="col-6 row">
            {!! Form::label('category', "Category", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="category" id="category" aria-controls="dataTableBuilder" class="form-control form-control-sm">
            <option value="0">select</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
            </div>
            </div>
        </div>


        <!-- Select subcategory-->
        <div class="form-group row ">
        <div class="col-6 row">
            {!! Form::label('subcategory', "Subcategory", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="subcategory" id="subcategory" aria-controls="dataTableBuilder" class="form-control form-control-sm">
            <option value="">select</option>
            </select>
            </div>
            </div>
        </div>

          <!-- title Field -->
          <div class="form-group row ">
          <div class="col-6 row">
            {!! Form::label('title', 'Title', ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('title', Request::is('*edit') ? $city->city_name : null  ,  ['class' => 'form-control','placeholder'=>  'Insert title']) !!}
            </div>
            </div>
          </div>

          <!-- body Field -->
          <div class="form-group row ">
          <div class="col-6 row">
            {!! Form::label('description', 'Body', ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('description', Request::is('*edit') ? $city->city_name : null  ,  ['class' => 'form-control','placeholder'=>  'Insert Description']) !!}
            </div>
            </div>
          </div>

        </div>

        </div>
        <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">


          @prepend('scripts')
            <script type="text/javascript">
              var var15866134771240834480ble = '';
              @if(isset($subcategory) && $subcategory->hasMedia('image'))
                      var15866134771240834480ble = {
                name: "{!! $city->getFirstMedia('image')->name !!}",
                size: "{!! $city->getFirstMedia('image')->size !!}",
                type: "{!! $city->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $city->getFirstMedia('image')->collection_name !!}"};
                      @endif
              var dz_var15866134771240834480ble = $(".dropzone.image").dropzone({
                        url: "{!!url('uploads/store')!!}",
                        addRemoveLinks: true,
                        maxFiles: 1,
                        init: function () {
                          @if(isset($subcategory) && $subcategory->hasMedia('image'))
                          dzInit(this,var15866134771240834480ble,'{!! url($subcategory->getFirstMediaUrl('image','thumb')) !!}')
                          @endif
                        },
                        accept: function(file, done) {
                          dzAccept(file,done,this.element,"{!!config('medialibrary.icons_folder')!!}");
                        },
                        sending: function (file, xhr, formData) {
                          dzSending(this,file,formData,'{!! csrf_token() !!}');
                        },
                        maxfilesexceeded: function (file) {
                          dz_var15866134771240834480ble[0].mockFile = '';
                          dzMaxfile(this,file);
                        },
                        complete: function (file) {
                          dzComplete(this, file, var15866134771240834480ble, dz_var15866134771240834480ble[0].mockFile);
                          dz_var15866134771240834480ble[0].mockFile = file;
                        },
                        removedfile: function (file) {
                          dzRemoveFile(
                                  file, var15866134771240834480ble, '{!! url("mscity/remove-media") !!}',
                                  'image', '{!! isset($city) ? $city->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
                          );
                        }
                      });
              dz_var15866134771240834480ble[0].mockFile = var15866134771240834480ble;
              dropzoneFields['image'] = dz_var15866134771240834480ble;
            </script>


        <script>
            // In your Javascript (external .js resource or <script> tag)
            $(document).ready(function () {
                var _this;
                // $('#projectinput1').select2();
                // $('#category').select2()


                $('#country').on('change',function(){
                    var _this = $(this);
                    $('#city').empty();
                    var data = "id="+_this.val();

                    var url = _this.closest('.card-body').data('route');
                      console.log(url);

                    $.post(url , data , function(res){
                        $city=['<option value="0" selected="">select </option>'];
                        //   $categoryslt=[];

                        // $menu.push('<option value="none" selected="" disabled="">select Menu Type</option>');

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
                var var1;
                // $('#projectinput1').select2();
                // $('#category').select2()


                $('#category').on('change',function(){
                    var var1 = $(this);
                    $('#subcategory').empty();
                    var data = "id="+var1.val();

                    var url = $('.subcategory').data('route');

                    $.post(url , data , function(res){
                        $subcategory=['<option value="0" selected="">select </option>'];
                        //   $categoryslt=[];

                        // $menu.push('<option value="none" selected="" disabled="">select Menu Type</option>');

                        for(var i=0 ;i<res.length;i++) {
                            $subcategory.push('<option value="' + res[i]['id'] + '">' + res[i]['name'] + '</option>');
                            // $categoryslt.push('<li data-value="' + res[i]['id'] + '" class="option null selected">' + res[i]['name1'] + '</li>');

                        }

                        $('#subcategory').empty();
                        $('#subcategory').append($subcategory);


                    });

                });

            });
        </script>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js">
                </script>
                <script src="https://www.gstatic.com/firebasejs/8.2.2/firebase-app.js"></script>
                <script src="https://www.gstatic.com/firebasejs/8.2.2/firebase-messaging.js"></script>

                <script>


                    var firebaseConfig = {
                        apiKey: "AIzaSyC1GWjZ1Irhj7_OB4Ob--_a_rcP0xnk1Js",
                        authDomain: "shs-chat-c425e.firebaseapp.com",
                        databaseURL: "https://shs-chat-c425e-default-rtdb.firebaseio.com/",
                        projectId: "shs-chat-c425e",
                        storageBucket: "shs-chat-c425e.appspot.com",
                        messagingSenderId: "963124896977",
                        appId: "1:963124896977:web:016e3a562edc51652211f0",
                        measurementId: "G-2MVVRHDF8M"
                    };

                    firebase.initializeApp(firebaseConfig);
                    const messaging = firebase.messaging();




                    messaging.onMessage(function(payload) {
                        const noteTitle = payload.notification.title;
                        const noteOptions = {
                            body: payload.notification.body,
                            icon: payload.notification.icon,
                        };
                        new Notification(noteTitle, noteOptions);
                    });

                </script>

          @endprepend
        </div>

      <!-- Submit Field -->
        <div class="form-group col-12 text-right">
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Notification</button>
          <a href="{!! route('notification.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>
