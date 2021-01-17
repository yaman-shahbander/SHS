<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

        <!-- Select Category-->
        <div class="form-group row ">
            {!! Form::label('name', "Category", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="category" aria-controls="dataTableBuilder" class="form-control form-control-sm">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @if(!empty($subcategory->category_id)) @if( $subcategory->category_id==$category->id) selected @endif @endif>
                    {{ $category->name }}</option>
                @endforeach
            </select>
                <div class="form-text text-muted">
                Select Category
                </div>
            </div>
        </div>

          <!-- Name Field -->
          <div class="form-group row ">
            {!! Form::label('name', trans("lang.category_name"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
              <div class="form-text text-muted">
                {{ trans("lang.category_name_help") }}
              </div>
            </div>
          </div>

          <!-- Description Field -->
          <div class="form-group row ">
            {!! Form::label('description', trans("lang.category_description"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::textarea('description', null, ['class' => 'form-control','placeholder'=>
               trans("lang.category_description_placeholder")  ]) !!}
              <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
            </div>
          </div>
        </div>
        <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

          <!-- Image Field -->
          <div class="form-group row">
            {!! Form::label('image', trans("lang.category_image"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              <div style="width: 100%" class="dropzone image" id="image" data-field="image">
                <input type="hidden" name="image">
              </div>
              <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
              <div class="form-text text-muted w-50">
                {{ trans("lang.category_image_help") }}
              </div>
            </div>
          </div>
          @prepend('scripts')
            <script type="text/javascript">
              var var15866134771240834480ble = '';
              @if(isset($subcategory) && $subcategory->hasMedia('image'))
                      var15866134771240834480ble = {
                name: "{!! $subcategory->getFirstMedia('image')->name !!}",
                size: "{!! $subcategory->getFirstMedia('image')->size !!}",
                type: "{!! $subcategory->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $subcategory->getFirstMedia('image')->collection_name !!}"};
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
                                  file, var15866134771240834480ble, '{!! url("msubcategory/remove-media") !!}',
                                  'image', '{!! isset($subcategory) ? $subcategory->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
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
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Sub Category</button>
          <a href="{!! route('subcategory.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>