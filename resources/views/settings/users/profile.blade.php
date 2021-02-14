@extends('layouts.app')
@push('css_lib')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <style>
 

 .center {
   display:inline;
   /* margin: 3px; */
 }

 .form-input {
   width:100px;
   padding:3px;
   background:#fff;
   border:2px dashed #427bff;
 }
 .form-input input {
   display:none;
 }
 .form-input label {
   display:block;
   /* width:100px; */
   height: auto;
   max-height: 100px;
   background:#333;
   border-radius:10px;
   cursor:pointer;
 }

 .form-input img {
   width:90px;
   height: 100px;
   margin: 2px;
   opacity: .4;
 }

 .imgRemove{
   position: relative;
   bottom: 114px;
   left: 68%;
   background-color: transparent;
   border: none;
   font-size: 30px;
   outline: none;
 }
 .imgRemove::after{
   content: ' \21BA';
   color: #fff;
   font-weight: 900;
   border-radius: 8px;
   cursor: pointer;
 }
 .small{
   color: firebrick;
 } 
 </style>
 <style>

.content {
  padding: 10px;
}
#view {
  color: red;
  cursor: pointer;
}
.hidden-content {
  display: none;
}
.hidden-content .active {
  display: block;
}
</style>
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{!! trans('lang.user_profile') !!} <small>{{trans('lang.media_desc')}}</small></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('lang.user_profile')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-user mr-2"></i> {{trans('lang.user_about_me')}}</h3>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img src="{{$user->getFirstMediaUrl('avatar','icon')}}" class="profile-user-img img-fluid img-circle" alt="{{$user->name}}">
                            </div>
                            <h3 class="profile-username text-center">{{$user->name}}</h3>
                           
                            <p class="text-muted text-center">{{implode(', ',$rolesSelected)}}</p>
                            <a class="btn btn-outline-{{setting('theme_color')}} btn-block" href="mailto:{{$user->email}}"><i class="fa fa-envelope mr-2"></i>{{$user->email}}
                            </a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    
                @if($customFields)
                    <!-- About Me Box -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list mr-2"></i>{{trans('lang.custom_field_plural')}}</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @foreach($customFieldsValues as $value)
                                    <strong>{{trans('lang.user_'.$value->customField->name)}}</strong>
                                    <p class="text-muted">
                                        {!! $value->view !!}
                                    </p>
                                    @if(!$loop->last)
                                        <hr> @endif
                                @endforeach
                            </div>
                            <!-- /.card-body -->
                        </div>
                    <!-- /.card -->
                    @endif

                    <!-- favorites-->
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list mr-2"></i>Favorite service providers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="row">
                                @foreach($user->vendorFavorite as $favorite)
                                        <div class="col-md-12">
                                        <strong><a data-toggle="tooltip" data-placement="bottom" title="Vendor Profile" href="{{ route('vendors.profile', ['id'=>$favorite->id]) }}" class='btn btn-link'>
                                        <i class="fa fa-user mr-2"></i>{{ $favorite->name }} </a></strong>
                                        </div>
                                @endforeach
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    <!-- /.card -->

                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    @include('flash::message')
                    @include('adminlte-templates::common.errors')
                    <div class="clearfix"></div>
                    <div class="card">
                    
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item">
                                    <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-cog mr-2"></i>{{trans('lang.app_setting')}}</a>
                                </li>
                                @hasrole('client')
                                <div class="ml-auto d-inline-flex">
                                    <li class="nav-item">
                                        <a class="nav-link pt-1" href="#"><i class="fa fa-check-o"></i> {{trans('lang.app_setting_become_restaurant_owner')}}</a>
                                    </li>
                                </div>
                                @endhasrole
                            </ul>
                        </div>
                        
                        <div class="card-body">
                            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'patch']) !!}
                            <div class="row">
                                @include('settings.users.fields')
                            </div>
                            {!! Form::close() !!}
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('layouts.media_modal',['collection'=>null])
@endsection
@push('scripts_lib')
    <!-- iCheck -->
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
    <!-- select2 -->
    <script src="{{asset('plugins/select2/select2.min.js')}}"></script>
    <script>

function showPreviewOne(event){
  if(event.target.files.length > 0){
    let src = URL.createObjectURL(event.target.files[0]);
    let preview = document.getElementById("file-ip-1-preview");
    preview.src = src;
    preview.style.display = "block";
  } 
}
function myImgRemoveFunctionOne() {
  document.getElementById("file-ip-1-preview").src = "https://i.ibb.co/ZVFsg37/default.png";
}

</script>
@endpush