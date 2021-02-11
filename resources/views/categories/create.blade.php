@extends('layouts.app')
@push('css_lib')
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
<!-- select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<style>
 

 .center {
   display:inline;
   margin: 3px;
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
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.category_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.category_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('categories.index') !!}">{{trans('lang.category_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.category_create')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="clearfix"></div>
  @include('flash::message')
  @include('adminlte-templates::common.errors')
  <div class="clearfix"></div>
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        @can('categories.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('categories.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.category_table')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.category_create')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      {!! Form::open(['route' => 'categories.store','files' => true,'method' => 'post'])  !!}
      <div class="row">
        @include('categories.fields')
      </div>
      {!! Form::close() !!}
      <div class="clearfix"></div>
    </div>
  </div>
</div>
@include('layouts.media_modal')
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