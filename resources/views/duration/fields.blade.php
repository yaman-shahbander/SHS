@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
<!-- Vendor Name Field -->
<div class="form-group row ">
  {!! Form::label('vendor_name', 'Vendor Name', ['class' => 'col-4 control-label text-right', 'style' => 'text-align: left !important']) !!}
  <div class="col-8">
    {!! Form::text('vendor_name', $duration->name,  ['class' => 'form-control','placeholder'=>  trans("lang.category_name_placeholder"), 'readonly']) !!}
  </div>
</div>

<!-- Select duration-->
<div class="form-group row ">
    {!! Form::label('Duration', "Duration", ['class' => 'col-3 control-label text-right', 'style' => 'text-align: left !important;']) !!}
    <div class="col-9">
    <select name="duration" aria-controls="dataTableBuilder" class="form-control form-control-sm">
         @foreach($durations as $dur)
           <option value="{{ $dur->id }}"
           @if(!empty($duration->duration_id)) @if( $duration->duration_id==$dur->id) selected @endif @endif
           >{{  $dur->duration  }}</option>
         @endforeach
    </select>
        <div class="form-text text-muted">
        Select Duration
        </div>
    </div>
</div>

<!-- Start date-->
<div class="form-group row ">
    {!! Form::label('start_date', "Start Date", ['class' => 'col-3 control-label text-right', 'style' => 'text-align: left !important;']) !!}
    <div class="col-9">   
        <div class="input-group date">
            <input  name="start_date"  type="text" class="form-control datepicker" value = {{ $duration->start_date }}>
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>  
    </div>
</div>

<!-- Start date-->
<div class="form-group row ">
    {!! Form::label('expire', "Expire", ['class' => 'col-3 control-label text-right', 'style' => 'text-align: left !important;']) !!}
    <div class="col-9">   
        <div class="input-group date" >
            <input name="expire" type="text" class="form-control datepicker" value = {{ $duration->expire }} >
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>  
    </div>
</div>




</div>
@if($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
  <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
  {!! $customFields !!}
</div>
@endif

@prepend('scripts')
<script type="text/javascript">
    var var15866134771240834480ble = '';
    @if(isset($category) && $category->hasMedia('image'))
    var15866134771240834480ble = {
        name: "{!! $country->getFirstMedia('image')->name !!}",
        size: "{!! $country->getFirstMedia('image')->size !!}",
        type: "{!! $country->getFirstMedia('image')->mime_type !!}",
        collection_name: "{!! $country->getFirstMedia('image')->collection_name !!}"};
    @endif
    var dz_var15866134771240834480ble = $(".dropzone.image").dropzone({
        url: "{!!url('uploads/store')!!}",
        addRemoveLinks: true,
        maxFiles: 1,
        init: function () {
        @if(isset($category) && $category->hasMedia('image'))
            dzInit(this,var15866134771240834480ble,'{!! url($category->getFirstMediaUrl('image','thumb')) !!}')
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
                file, var15866134771240834480ble, '{!! url("categories/remove-media") !!}',
                'image', '{!! isset($country) ? $country->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
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
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Duration</button>
  <a href="{!! route('country.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>

<script>
$('.datepicker').pickadate()
</script>