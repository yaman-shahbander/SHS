@extends('layouts.app')
@section('css_custom')
  <style>

    .stars-container {
      position: relative;
      display: inline-block;
      color: transparent;
    }

    .stars-container:before {
      position: absolute;
      top: 0;
      left: 0;
      content: '★★★★★';
      color: lightgray;
    }

    .stars-container:after {
      position: absolute;
      top: 0;
      left: 0;
      content: '★★★★★';
      color: gold;
      overflow: hidden;
      height: 100%;
    }

    .stars-0:after { width: 0%; }
    .stars-1:after { width: 1%; }
    .stars-2:after { width: 2%; }
    .stars-3:after { width: 3%; }
    .stars-4:after { width: 4%; }
    .stars-5:after { width: 5%; }
    .stars-6:after { width: 6%; }
    .stars-7:after { width: 7%; }
    .stars-8:after { width: 8%; }
    .stars-9:after { width: 9%; }
    .stars-10:after { width: 10%; }
    .stars-11:after { width: 11%; }
    .stars-12:after { width: 12%; }
    .stars-13:after { width: 13%; }
    .stars-14:after { width: 14%; }
    .stars-15:after { width: 15%; }
    .stars-16:after { width: 16%; }
    .stars-17:after { width: 17%; }
    .stars-18:after { width: 18%; }
    .stars-19:after { width: 19%; }
    .stars-20:after { width: 20%; }
    .stars-21:after { width: 21%; }
    .stars-22:after { width: 22%; }
    .stars-23:after { width: 23%; }
    .stars-24:after { width: 24%; }
    .stars-25:after { width: 25%; }
    .stars-26:after { width: 26%; }
    .stars-27:after { width: 27%; }
    .stars-28:after { width: 28%; }
    .stars-29:after { width: 29%; }
    .stars-30:after { width: 30%; }
    .stars-31:after { width: 31%; }
    .stars-32:after { width: 32%; }
    .stars-33:after { width: 33%; }
    .stars-34:after { width: 34%; }
    .stars-35:after { width: 35%; }
    .stars-36:after { width: 36%; }
    .stars-37:after { width: 37%; }
    .stars-38:after { width: 38%; }
    .stars-39:after { width: 39%; }
    .stars-40:after { width: 40%; }
    .stars-41:after { width: 41%; }
    .stars-42:after { width: 42%; }
    .stars-43:after { width: 43%; }
    .stars-44:after { width: 44%; }
    .stars-45:after { width: 45%; }
    .stars-46:after { width: 46%; }
    .stars-47:after { width: 47%; }
    .stars-48:after { width: 48%; }
    .stars-49:after { width: 49%; }
    .stars-50:after { width: 50%; }
    .stars-51:after { width: 51%; }
    .stars-52:after { width: 52%; }
    .stars-53:after { width: 53%; }
    .stars-54:after { width: 54%; }
    .stars-55:after { width: 55%; }
    .stars-56:after { width: 56%; }
    .stars-57:after { width: 57%; }
    .stars-58:after { width: 58%; }
    .stars-59:after { width: 59%; }
    .stars-60:after { width: 60%; }
    .stars-61:after { width: 61%; }
    .stars-62:after { width: 62%; }
    .stars-63:after { width: 63%; }
    .stars-64:after { width: 64%; }
    .stars-65:after { width: 65%; }
    .stars-66:after { width: 66%; }
    .stars-67:after { width: 67%; }
    .stars-68:after { width: 68%; }
    .stars-69:after { width: 69%; }
    .stars-70:after { width: 70%; }
    .stars-71:after { width: 71%; }
    .stars-72:after { width: 72%; }
    .stars-73:after { width: 73%; }
    .stars-74:after { width: 74%; }
    .stars-75:after { width: 75%; }
    .stars-76:after { width: 76%; }
    .stars-77:after { width: 77%; }
    .stars-78:after { width: 78%; }
    .stars-79:after { width: 79%; }
    .stars-80:after { width: 80%; }
    .stars-81:after { width: 81%; }
    .stars-82:after { width: 82%; }
    .stars-83:after { width: 83%; }
    .stars-84:after { width: 84%; }
    .stars-85:after { width: 85%; }
    .stars-86:after { width: 86%; }
    .stars-87:after { width: 87%; }
    .stars-88:after { width: 88%; }
    .stars-89:after { width: 89%; }
    .stars-90:after { width: 90%; }
    .stars-91:after { width: 91%; }
    .stars-92:after { width: 92%; }
    .stars-93:after { width: 93%; }
    .stars-94:after { width: 94%; }
    .stars-95:after { width: 95%; }
    .stars-96:after { width: 96%; }
    .stars-97:after { width: 97%; }
    .stars-98:after { width: 98%; }
    .stars-99:after { width: 99%; }
    .stars-100:after { width: 100%; }

  </style>
  @endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.reviews')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.pending_reviews')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('reviews.index') !!}">{{trans('lang.reviews')}}</a>
          </li>
          <li class="breadcrumb-item active"></li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<div class="content">
  <div class="clearfix"></div>
  @include('flash::message')
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.pending_reviews_list')}}</a>
        </li>

        @include('layouts.right_toolbar', compact('dataTable'))
      </ul>
    </div>
    <div class="card-body">
      @include('pending_reviews.table')
      <div class="clearfix"></div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script>

    // $(function() {
    //   setTimeout(function(){
    //     startd=$("tr td:nth-child(2)");
    //     $(document).find(startd).each(function() {
    //       //alert(startd.text());
    //
    //       function addScore(score, $domElement) {
    //
    //         $("<span class='stars-container'>")
    //                 .addClass("stars-" + score.toString())
    //                 .text("★★★★★")
    //                 .appendTo($domElement);
    //       }
    //       var star = Math.round(parseInt($(this).text()));
    //       $(this).text('');
    //
    //       addScore(star, $(this))
    //     });
    //   },800);

    // });
    $('#dataTableBuilder').on( 'processing.dt', function () {
      // setTimeout(function () {
      startd = $("tr td:nth-child(5)");
      $(document).find(startd).each(function () {
        //alert(startd.text());

        function addScore(score, $domElement) {

          $("<span class='stars-container'>")
                  .addClass("stars-" + score.toString())
                  .text("★★★★★")
                  .appendTo($domElement);
        }

        var star = Math.round(parseInt($(this).text()));
        $(this).text('');

        addScore(star, $(this))
      });
      // } ,50);
    });

  </script>
  @endsection

