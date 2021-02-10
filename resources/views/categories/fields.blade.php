@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 100%;padding: 0 4px;" class="column">
    <div class="row">
      <!-- Name Field -->
      <div class="form-group row col-md-6">
        {!! Form::label('name', trans("lang.category_name"), ['class' => 'col-3 control-label']) !!}
        <div class="col-9">
          {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
          <div class="form-text text-muted">
            {{ trans("lang.category_name_help") }}
          </div>
        </div>
      </div>
      <!-- NameEn Field -->
      <div class="form-group row col-md-6">
        {!! Form::label('name_en', 'NameEn', ['class' => 'col-3 control-label ']) !!}
        <div class="col-9">
          {!! Form::text('name_en', Request::is('*edit') ? $category->name_en : null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
          <div class="form-text text-muted">
            {{ trans("lang.category_name_help") }}
          </div>
        </div>
      </div>
    </div>
  <div class="row">
    <!-- Description Field -->
    <div class="form-group row col-md-6">
      {!! Form::label('description', trans("lang.category_description"), ['class' => 'col-3 control-label']) !!}
      <div class="col-9">
      {!! Form::text('description', null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
        <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
      </div>
    </div>
    <!-- Description AR Field -->
    <div class="form-group row col-md-6">
          {!! Form::label('description_ar', 'Description AR', ['class' => 'col-3 control-label ']) !!}
          <div class="col-9">
          {!! Form::text('description_ar', Request::is('*edit') ? $category->description_ar : null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}

            <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
          </div>
        </div>
  </div>
  <div class="row">
        <!-- Description En Field -->
        <div class="form-group row col-md-6">
          {!! Form::label('description_en', 'Description En', ['class' => 'col-3 control-label ']) !!}
          <div class="col-9">
            {!! Form::text('description_en', Request::is('*edit') ? $category->description_en : null,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
            <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
          </div>
        </div>
         <!-- Image Field -->
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
                    <small class="small">The image must have a png extension</small>
                </div>
            </div>
        </div>

  </div>  
  </div>
  




</div>
      </div>


<!-- Submit Field -->
<div class="form-group col-12 text-right">
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.category')}}</button>
  <a href="{!! route('categories.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
