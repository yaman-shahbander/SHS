@extends('layouts.settings.default')
@push('css_lib')
  <meta name="csrf-token" content="{{ csrf_token() }}">

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

<link rel="stylesheet" href="{{asset('flag.css')}}"/>

@endpush
@section('settings_title',trans('lang.user_table'))
@section('settings_content')
  @include('flash::message')
  @include('adminlte-templates::common.errors')
  <div class="clearfix"></div>
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link" href="{!! route('users.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.user_table')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.user_create')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body" data-route="{{url('api/user/select')}}">
      {!! Form::open(['route' => 'users.store','files' => true,'method' => 'post']) !!}
      <div class="row">
        @include('settings.users.fields')
      </div>
      {!! Form::close() !!}
      <div class="clearfix"></div>
    </div>
  </div>
</div>
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