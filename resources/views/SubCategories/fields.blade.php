<div style="flex: 50%;max-width:100%;padding: 0 4px;" class="column">
     
      <div class="row">
          <!-- Name Field -->
          <div class="form-group row col-md-6">
            {!! Form::label('name', trans("lang.category_name"), ['class' => 'col-3 control-label ']) !!}
            <div class="col-9">
              {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
              <div class="form-text text-muted">
                {{ trans("lang.category_name_help") }}
              </div>
            </div>
          </div>
          <!-- Name Field -->
          <div class="form-group row col-md-6">
            {!! Form::label('name_en', "NameEn", ['class' => 'col-3 control-label ']) !!}
            <div class="col-9">
              {!! Form::text('name_en', null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
              <div class="form-text text-muted">
                {{ trans("lang.category_name_help") }}
              </div>
            </div>
          </div>
      </div>
      <div class="row">
        <!-- Select Category-->
        <div class="form-group row col-md-6">
            {!! Form::label('name', "Category", ['class' => 'col-3 control-label ']) !!}
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
        <!-- Description Field -->
        <div class="form-group row col-md-6">
          {!! Form::label('description', trans("lang.category_description"), ['class' => 'col-3 control-label ']) !!}
          <div class="col-9">
          <textarea name="description" class="form-control form-control-sm" rows="3" cols="50"></textarea>

            <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
          </div>
        </div>
      </div>  
      
      <div class="row">
        <!-- Description AR Field -->
        <div class="form-group row col-md-6">
          {!! Form::label('description', 'Description AR', ['class' => 'col-3 control-label ']) !!}
          <div class="col-9">
          <textarea name="description_ar" class="form-control form-control-sm" rows="3" cols="50"></textarea>

            <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
          </div>
        </div>
        <!-- Description En Field -->
        <div class="form-group row col-md-6">
          {!! Form::label('description_en', 'Description En', ['class' => 'col-3 control-label ']) !!}
          <div class="col-9">
            <textarea name="description_en" class="form-control form-control-sm" rows="3" cols="50"></textarea>
            
            <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
          </div>
        </div>
      </div>   
      <div class="row">
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
      <!-- Submit Field -->
        <div class="form-group col-12 text-right">
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Sub Category</button>
          <a href="{!! route('subcategory.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>