<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
        <!-- Vendors Field -->
        <div class="form-group row ">
            {!! Form::label('vendors', "Vendors", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
            <select name="vendors" aria-controls="dataTableBuilder" class="form-control form-control-sm" required @if(Request::is('*edit')) readonly @endif>
              <option value="0">select</option>
                @foreach($vendors as $vendor)
                  <option value="{{ $vendor->id }}" @if(Request::is('*edit')) @if($offer->user_id == $vendor->id) selected @endif @endif>{{ $vendor->name }}</option>
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
              {!! Form::text('offername', Request::is('*edit') ? $offer->title : null ,  ['class' => 'form-control', 'required' => true, 'placeholder'=>  trans("lang.category_name_placeholder")]) !!}
              <div class="form-text text-muted">
                {{ trans("lang.category_name_help") }}
              </div>
            </div>
          </div>

          <!-- Description Field -->

          <div class="form-group row ">
            {!! Form::label('description', "Description", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              {!! Form::text('description', Request::is('*edit') ? $offer->description : null ,  ['class' => 'form-control', 'required' => true, 'placeholder'=>  "Insert Description"]) !!}
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
                  <option value="{{ $category->id }}" @if (Request::is('*edit')) @if(!empty($category->id)) @if($offer->subcategories->categories->id==$category->id) selected @endif @endif @endif>{{ $category->name }}</option>
                @endforeach
              </select>
               <div class="form-text text-muted">
                 select category
               </div>
              </div>
          </div>

        <!-- Select subcategory-->
        <div class="form-group row " Request::is('*edit') ? style="visibility:visible" : style="visibility:hidden" id="sub">
            {!! Form::label('subcategory', "Subcategory", ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
              <select name="subcategory" id="subcategory" aria-controls="dataTableBuilder" class="form-control form-control-sm" required>
              <option value="" >select</option>
                @if(Request::is('*edit'))
                    @foreach($subcategories as $subcategory)
                            <option value={{$subcategory->id}} @if(!empty($subcategory->id)) @if($offer->subcategory_id==$subcategory->id) selected @endif @endif>
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
              {!! Form::file('image' , ['class' => 'form-control']) !!}
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


            
          @endprepend
        </div>
        
      <!-- Submit Field -->
        <div class="form-group col-12 text-right">
          <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> Save Offer</button>
          <a href="{!! route('city.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>