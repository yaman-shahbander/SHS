@extends('layouts.app')
@push('css_lib')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <!-- bootstrap wysihtml5 - text editor -->

    {{--dropzone--}}
    <link rel="stylesheet" href="{{asset('plugins/dropzone/bootstrap.min.css')}}">
@endpush
@section('css_custom')
    <style>body {
            background-color: #eee
        }

        .card {
            background-color: #fff;
            border: none
        }

        .form-color {
            background-color: #fafafa
        }

        .form-control {
            height: 48px;
            border-radius: 25px
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #35b69f;
            outline: 0;
            box-shadow: none;
            text-indent: 10px
        }

        .c-badge {
            background-color: #35b69f;
            color: white;
            height: 20px;
            font-size: 11px;
            width: 92px;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2px
        }

        .comment-text {
            font-size: 13px
        }

        .wish {
            color: #35b69f
        }

        .user-feed {
            font-size: 14px;
            margin-top: 12px
        }


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

        .custom-star-edit {
            position: relative;
            left: 65px;
            margin-bottom: 4px;
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
        .stars-100:after { width: 100; }

    </style>
    @endsection
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

                    <!-- Profile Image -->
                    <div class="card ">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-user mr-2"></i> {{trans('lang.user_about_me')}}</h3>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img src="{{$user->getFirstMediaUrl('avatar','icon')}}" class="profile-user-img img-fluid img-circle" alt="{{$user->name}}">
                            </div>
                            <h3 class="profile-username text-center">{{$user->name}}</h3>
                            <p id="fixture" class="text-muted custom-star-edit">
                            {{ $user->rating }}</p>
                            <p class="text-muted text-center">{{implode(', ',$rolesSelected)}}</p>
                            <a class="btn btn-outline-{{setting('theme_color')}} btn-block" href="mailto:{{$user->email}}"><i class="fa fa-envelope mr-2"></i>{{$user->email}}
                            </a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- About Me Box -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list mr-2"></i>Working Hours</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body ">
                                @foreach($user->days as $hour)
                                    <div class="row">
                                        <div class="col-md-3">
                                        <strong >{{ substr($hour->name_en,0,3)}}</strong>
                                        </div>
                                        <div class="col-md-5">

                                        <strong style="font-size: 13px;">{{ date("g:i A",strtotime($hour->pivot->start))}}</strong>
                                        </div>
                                        <div class="col-md-4">
                                        <strong style="font-size: 13px;">{{ date("g:i A",strtotime($hour->pivot->end)) }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- /.card-body -->
                        </div>
                    <!-- /.card -->

                <!-- sub-categories vendor offers-->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list mr-2"></i>{{trans('lang.subcategory')}}</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="row">
                                @foreach($user->subcategories as $subcategory)
                                        <div class="col-md-6">
                                        <strong>{{ $subcategory->name }}</strong>
                                        </div>
                                @endforeach
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    <!-- /.card -->


                    <!-- vendor special-offers-->
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list mr-2"></i>Special Offers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="row">
                                @foreach($user->specialOffers as $specialOffer)
                                    <div class="col-md-5">
                                        <strong>{{ $specialOffer->image }}</strong>
                                    </div>
                                    <div class="col-md-7">
                                            <strong>{{ $specialOffer->title }}</strong>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>{{ $specialOffer->description }}</strong>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    <!-- /.card -->

                    <!-- available-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list mr-2"></i>Available Cities</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body ">
                            <div class="row">
                            @foreach($user->vendor_city as $city)

                                    <div class="col-md-6">
                                        <strong>{{ $city->city_name}}</strong>
                                    </div>

                            @endforeach
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

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

                    <!-- users who added this vendor as a favorite-->
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list mr-2"></i> service provider</h3>
                                <h6 style="font-size:11px"><i>Users who added this as a favorite </i></h6>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="row">
                                @foreach($favoriteVendor as $favorite)
                                <div class="col-md-12">
                                    <strong>
                                    <i class="fa fa-user mr-2"></i>{{ $favorite->name }} </strong>
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
                                        <a class="nav-link pt-1" href="{!! route('restaurants.create') !!}"><i class="fa fa-check-o"></i> {{trans('lang.app_setting_become_restaurant_owner')}}</a>
                                    </li>
                                </div>
                                @endhasrole
                            </ul>
                        </div>

                        <div class="card-body select_cities" data-route="{{url('api/user/select')}}">
                            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'patch']) !!}
                            <div class="row">
                                @include('settings.vendors.fields')
                            </div>
                            {!! Form::close() !!}
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body">
                            @include('settings.vendors.table')
                            <div class="clearfix"></div>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="container mt-5 mb-5">
                        <div class="row height d-flex justify-content-center align-items-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="p-3">
                                        <h6>Ratings</h6>
                                    </div>
                                    <div class="mt-2">

@foreach($user->clients as $client)
    @if($client->pivot->approved==1)
                                            <?php
                                            $sum = 0;
                                            $sum += $client->pivot->price_rating+$client->pivot->service_rating+$client->pivot->trust_rating+$client->pivot->speed_rating+$client->pivot->knowledge_rating;
                                            if($sum!=0)

                                        $sum=$sum/(5);
                                            ?>
                                        <div class="d-flex flex-row p-3"> <img src="https://i.imgur.com/agRGhBc.jpg" width="40" height="40" class="rounded-circle mr-3">
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-row align-items-center"> <span class="mr-2">{{$client->name}}</span> <div id="fixture">{{$sum}}</div> </div> <small>{{$client->pivot->created_at}}</small>
                                                </div>
                                                <p class="text-justify comment-text mb-0">{{$client->pivot->description}}</p>
                                                <div class="d-flex flex-row user-feed" style="display: block !important"> <span class="wish" >
                                                        Quality Rating</span><div id="fixture">{{$client->pivot->service_rating}}</div>
                                                    <span class="wish">    &nbsp;&nbsp;Price Rating</span><div id="fixture">{{$client->pivot->price_rating}}</div>
                                                       <span class="wish"> &nbsp;&nbsp;Speed Rating</span><div id="fixture">{{$client->pivot->speed_rating}}</div>
                                                       <span class="wish"> &nbsp;&nbsp;Trust Rating</span><div id="fixture">{{$client->pivot->trust_rating}}</div>
                                                       <span class="wish"> &nbsp;&nbsp;Knowledge Rating</span><div id="fixture">{{$client->pivot->knowledge_rating}}</div></span>
                                                    </div>
                                            </div>
                                        </div>
                                            @endif
    @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
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
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->

    {{--dropzone--}}
    <script src="{{asset('plugins/dropzone/dropzone.js')}}"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;
        var dropzoneFields = [];
    </script>
@endpush
@section('script')
{{--<script>--}}
{{--    $(function() {--}}
{{--        function addScore(score, $domElement) {--}}
{{--            $("<span class='stars-container'>")--}}
{{--                .addClass("stars-" + score.toString())--}}
{{--                .text("★★★★★")--}}
{{--                .appendTo($domElement);--}}
{{--        }--}}

{{--        addScore(88, $("#fixture"));--}}
{{--    });--}}
{{--</script>--}}
<script>

    $(function() {
        setTimeout(function(){
            //startd=$("#fixture");
            $(document).find($("[id=fixture]")).each(function() {
               // alert($(".wish div").text());

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
        },100);

    });

</script>
    @endsection
