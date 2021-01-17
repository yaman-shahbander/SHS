{{-- Meta tags --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="route" content="{{ $route }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.path') }}" data-user="{{ Auth::user()->id }}">

{{-- scripts --}}
<script src="{{ asset('js/chatify/font.awesome.min.js') }}"></script>
<script src="{{ asset('js/chatify/autosize.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

{{-- styles --}}
<link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>
<link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/chatify/'.$dark_mode.'.mode.css') }}" rel="stylesheet" />
<link href="{{ asset('css/app.css') }}" rel="stylesheet" />

<style type="text/css">
            [data-role="controls"] > button {
                margin-top: 8px;
                margin-right: 5px;
                width:25px !important;
                height:25px !important;
                outline: none;
                display: block;
                border: none;
                background-color: #D9AFD9;
                background-image: -webkit-gradient(linear, left bottom, left top, from(#D9AFD9), to(#97D9E1));
                background-image: -o-linear-gradient(bottom, #D9AFD9 0%, #97D9E1 100%);
                background-image: linear-gradient(0deg, #D9AFD9 0%, #97D9E1 100%);
                border-radius: 58%;
                text-indent: -1000em;
                cursor: pointer;
                -webkit-box-shadow: 0px 5px 5px 2px rgba(0,0,0,0.3) inset,
                    0px 0px 0px 0px #fff, 0px 0px 0px 6px #333;
                        box-shadow: 0px 5px 5px 2px rgba(0,0,0,0.3) inset,
                    0px 0px 0px 0px #fff, 0px 0px 0px 6px #333;
            }
            [data-role="controls"] > button:hover {
                margin-top: 8px;
                margin-right: 5px;
                background-color: #ee7bee;
                background-image: -webkit-gradient(linear, left bottom, left top, from(#ee7bee), to(#6fe1f5));
                background-image: -o-linear-gradient(bottom, #ee7bee 0%, #6fe1f5 100%);
                background-image: linear-gradient(0deg, #ee7bee 0%, #6fe1f5 100%);
            }
            [data-role="controls"] > button[data-recording="true"] {
                margin-top: 8px;
                margin-right: 5px;
                background-color: #ff2038;
                background-image: -webkit-gradient(linear, left bottom, left top, from(#ff2038), to(#b30003));
                background-image: -o-linear-gradient(bottom, #ff2038 0%, #b30003 100%);
                background-image: linear-gradient(0deg, #ff2038 0%, #b30003 100%);
            }
            [data-role="recordings"] > .row {
                width: auto;
                height: auto;
                padding: 20px;
            }
            [data-role="recordings"] > .row > audio {
                outline: none;
            }
            [data-role="recordings"] > .row > a {
                display: inline-block;
                text-align: center;
                font-size: 20px;
                line-height: 50px;
                vertical-align: middle;
                width: 50px;
                height: 50px;
                border-radius: 20px;
                color: #fff;
                font-weight: bold;
                text-decoration: underline;
                background-color: #0093E9;
                background-image: -webkit-gradient(linear, left bottom, left top, from(#0093E9), to(#80D0C7));
                background-image: -o-linear-gradient(bottom, #0093E9 0%, #80D0C7 100%);
                background-image: linear-gradient(0deg, #0093E9 0%, #80D0C7 100%);
                float: right;
                margin-left: 20px;
                cursor: pointer;
            }
            [data-role="recordings"] > .row > a:hover {
                text-decoration: none;
            }
            [data-role="recordings"] > .row > a:active {
                background-image: -webkit-gradient(linear, left top, left bottom, from(#0093E9), to(#80D0C7));
                background-image: -o-linear-gradient(top, #0093E9 0%, #80D0C7 100%);
                background-image: linear-gradient(180deg, #0093E9 0%, #80D0C7 100%);
            }
        </style>
        <script type="text/javascript" src="https://code.jquery.com/jquery.min.js"></script>
        <script src="{{ url('/js/recorder.js') }}"></script>
        <script>
    jQuery(document).ready(function () {
        var $ = jQuery;
        var myRecorder = {
            objects: {
                context: null,
                stream: null,
                recorder: null
            },
            init: function () {
                if (null === myRecorder.objects.context) {
                    myRecorder.objects.context = new (
                            window.AudioContext || window.webkitAudioContext
                            );
                }
            },
            start: function () {
                var options = {audio: true, video: false};
                navigator.mediaDevices.getUserMedia(options).then(function (stream) {
                    myRecorder.objects.stream = stream;
                    myRecorder.objects.recorder = new Recorder(
                            myRecorder.objects.context.createMediaStreamSource(stream),
                            {numChannels: 1}
                    );
                    myRecorder.objects.recorder.record();
                }).catch(function (err) {});
            },
            stop: function (listObject) {
                if (null !== myRecorder.objects.stream) {
                    myRecorder.objects.stream.getAudioTracks()[0].stop();
                }
                if (null !== myRecorder.objects.recorder) {
                    myRecorder.objects.recorder.stop();

                    // Validate object
                    if (null !== listObject
                            && 'object' === typeof listObject
                            && listObject.length > 0) {
                        // Export the WAV file
                        myRecorder.objects.recorder.exportWAV(function (blob) {
                            var url = (window.URL || window.webkitURL)
                                    .createObjectURL(blob);

                            // Prepare the playback
                            var audioObject = $('<audio controls></audio>')
                                    .attr('src', url);


                        //     ////////////////////
                        //     if (audioObject != null) {
                        //     $.ajaxSetup({
                        //         headers: {
                        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        //         }
                        //     });

                        //     $.ajax({
                        //         url: './chatify/sendMessage',
                        //         method: "POST",
                        //         processData: false,
                        //         cache: false,
                        //         dataType: "json",
                        //         data: { audioTag : audioObject[0] }
                        //     });
                        //   }
                          console.log(audioObject[0]);
                            ////////////////////////
                            

                            // Wrap everything in a row
                            var holderObject = $('<div class="row"></div>')
                                    .append(audioObject);

                            // Append to the list
                            listObject.append(holderObject);
                        });
                    }
                }
            }
        };

        // Prepare the recordings list
        var listObject = $('[data-role="recordings"]');

        // Prepare the record button
        $('[data-role="controls"] > button').click(function () {
            // Initialize the recorder
            myRecorder.init();

            // Get the button state
            var buttonState = !!$(this).attr('data-recording');

            // Toggle
            if (!buttonState) {
                $(this).attr('data-recording', 'true');
                myRecorder.start();
            } else {
                $(this).attr('data-recording', '');
                myRecorder.stop(listObject);
            }
        });
    });
</script>
{{-- Messenger Color Style--}}
@include('Chatify::layouts.messengerColor')