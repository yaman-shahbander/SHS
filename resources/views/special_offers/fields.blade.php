<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

        <!-- Select country-->
        <div class="form-group row ">
            {!! Form::label('name', "Country", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="user" aria-controls="dataTableBuilder" class="form-control form-control-sm">
                @foreach($users as $user)
                    <option  
                    value="{{ $user->id }}" @if(!empty($offer->user_id)) @if( $offer->user_id==$user->id) selected @endif @endif>
                    {{ $country->country_name }}</option>
                @endforeach
            </select>
                <div class="form-text text-muted">
                Select Country
                </div>
            </div>
        </div>
        
          <!-- Name Field -->
          <div class="form-group row ">
            {!! Form::label('description', trans("lang.category_name"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('description', Request::is('*edit') ? $city->city_name : null  ,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
              <div class="form-text text-muted">
                {{ trans("lang.category_name_help") }}
              </div>
            </div>
          </div>

          <!-- Description Field -->

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
          @endprepend
        </div>

      <!-- Submit Field -->
        <div class="form-group col-12 text-right">
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save City</button>
          <a href="{!! route('city.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>