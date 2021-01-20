<div style="flex: 50%;max-width:100%;padding: 0 4px;" class="column">

    <div class="row">
        <!-- Name Field -->
        <div class="form-group row col-md-6">
          {!! Form::label('name', trans("lang.category_name"), ['class' => 'col-3 control-label text-right']) !!}
          <div class="col-9">
            {!! Form::text('name', Request::is('*edit') ? $city->city_name : null  ,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
            <div class="form-text text-muted">
              {{ trans("lang.category_name_help") }}
            </div>
          </div>
        </div>
          <!-- Name Field -->
          <div class="form-group row col-md-6">
          {!! Form::label('name_en', 'Name En', ['class' => 'col-3 control-label text-right']) !!}
          <div class="col-9">
            {!! Form::text('name_en', Request::is('*edit') ? $city->city_name : null  ,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder")]) !!}
            <div class="form-text text-muted">
              {{ trans("lang.category_name_help") }}
            </div>
          </div>
        </div>
  </div>
  <div class="row">
        <!-- Select country-->
        <div class="form-group row col-md-6">
            {!! Form::label('name', "Country", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="country" aria-controls="dataTableBuilder" class="form-control form-control-sm">
                @foreach($countries as $country)
                    <option  
                    value="{{ $country->id }}" @if(!empty($city->country_id)) @if( $city->country_id==$country->id) selected @endif @endif>
                    {{ $country->country_name }}</option>
                @endforeach
            </select>
                <div class="form-text text-muted">
                Select Country
                </div>
            </div>
        </div>
          <!-- Description Field -->
          <div class="form-group row " style="display:none">
            {!! Form::label('description', trans("lang.category_description"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::textarea('description', 'ff', ['class' => 'form-control','placeholder'=>
               trans("lang.category_description_placeholder")  ]) !!}
              <div class="form-text text-muted">{{ trans("lang.category_description_help") }}</div>
            </div>
          </div>
</div>     
        

      <!-- Submit Field -->
        <div class="form-group col-12 text-right">
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save City</button>
          <a href="{!! route('city.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>