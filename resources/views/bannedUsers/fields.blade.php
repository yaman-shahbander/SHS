<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">


        <!-- Select Name-->
      <div class="form-group row ">
          {!! Form::label('username', "Name", ['class' => 'col-3 control-label text-right']) !!}
          <div class="col-9">
          <select name="username" aria-controls="dataTableBuilder" class="form-control form-control-sm">
              @if(Request::is('*create'))
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if(!empty($bannedUser->user_id)) @if( $bannedUser->user_id==$user->id) selected @endif @endif>
                    {{ $user->name }}</option>
                @endforeach
              @endif
              @if(Request::is('*edit'))
                <option value="{{ $bannedUsers->user_id }}">{{ $bannedUsers->user->name }}
                </option>
              @endif
          </select>
              <div class="form-text text-muted">
              Select Name
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

           <!-- temporary_ban Field -->
           <div class="form-group row ">
            {!! Form::label('temp_ban', 'temp Ban', ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
             @if(Request::is('*create'))
                {!! Form::date('temp_ban', null ,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
             @endif

             @if(Request::is('*edit'))
                {!! Form::date('temp_ban', $bannedUsers->temporary_ban ,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
             @endif
              <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
            </div>
          </div>


        <!-- Select Ban forever-->
        <div class="form-group row ">
            {!! Form::label('banValue', "Ban Forever", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="banValue" aria-controls="dataTableBuilder" class="form-control form-control-sm">
            @if(Request::is('*create'))
              <option value="0">0</option>
              <option value="1">1</option>
             @endif
            
             @if(Request::is('*edit'))
             <option value="0" @if(($bannedUsers->forever_ban) == 0) selected @endif>0
             </option>
             <option value="1" @if(($bannedUsers->forever_ban) == 1) selected @endif>1
             </option>
             @endif
            </select>
                <div class="form-text text-muted">
                Select Value
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
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Ban Info</button>
          <a href="{!! route('bannedUsers.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>