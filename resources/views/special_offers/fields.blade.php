<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
        <!-- Vendors Field -->
        <div class="form-group row ">
            {!! Form::label('vendors', "Vendors", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="vendors" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
              <option value="0">select</option>
                @foreach($vendors as $vendor)
                  <option value="{{ $vendor->id }}" @if(Request::is('*edit')) @if($notification->category == $category->id) selected @endif @endif>{{ $vendor->name }}</option>
                 @endforeach
              </select>
              <div class="form-text text-muted">
                Select Vendor
              </div>
            </div>
          </div>
        
          <!-- Name Field -->
          <div class="form-group row ">
            {!! Form::label('offername', "Offer Name", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('offername', null ,  ['class' => 'form-control', 'required' => true, 'placeholder'=>  trans("lang.category_name_placeholder")]) !!}
              <div class="form-text text-muted">
                {{ trans("lang.category_name_help") }}
              </div>
            </div>
          </div>

          <!-- Description Field -->

          <div class="form-group row ">
            {!! Form::label('description', "Description", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('description', null ,  ['class' => 'form-control', 'required' => true, 'placeholder'=>  "Insert Description"]) !!}
              <div class="form-text text-muted">
                 Insert Description
              </div>
            </div>
          </div>

          <!-- Select category-->
        <div class="form-group row">
              {!! Form::label('category', "Category", ['class' => 'col-3 control-label text-right']) !!}
              <div class="col-9">
              <select name="category" id="category" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
              <option value="0">select</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" @if(Request::is('*edit')) @if($notification->category == $category->id) selected @endif @endif>{{ $category->name }}</option>
                @endforeach
              </select>
               <div class="form-text text-muted">
                 select category
               </div>
              </div>
          </div>

        <!-- Select subcategory-->
        <div class="form-group row " style="visibility:hidden" id="sub">
            {!! Form::label('subcategory', "Subcategory", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              <select name="subcategory" id="subcategory" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
              <option value="" >select</option>
                @if(Request::is('*edit'))
                    @foreach($subcategories as $subcategory)
                            <option value={{$subcategory->id}} @if(!empty($subcategory->id)) @if($notification->subcategory==$subcategory->id) selected @endif @endif>
                                {{$subcategory->name}}</option>
                    @endforeach
                @endif
              </select>
              <div class="form-text text-muted">
                 select subcategory
               </div>
            </div>
        </div>
          
        </div>
        <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
        
        <!-- image Field -->

        <div class="form-group row">
            {!! Form::label('image', "Image", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::file('image' , ['class' => 'form-control', 'required' => true]) !!}
              <div class="form-text text-muted">
                 Select Image
              </div>
            </div>
          </div>
          
          @prepend('scripts')

          <script>
            $(document).ready(function () {
                var var1;

                $('#category').on('change',function(){
                    var var1 = $(this);
                    $('#subcategory').empty();
                    $("#sub").css('visibility', "visible");

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
          @endprepend
        </div>
        
      <!-- Submit Field -->
        <div class="form-group col-12 text-right">
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Offer</button>
          <a href="{!! route('city.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>