<div class="message-wrapper">
    <ul class="messages">
        @foreach($messages as $message)
        <li class="message clearfix">
            {{--if message from id is equal to auth id then it is sent by logged in user --}}
            <div class="{{ ($message->from == Auth::user()->device_token) ? 'sent' : 'received' }}">
                <p>{{ $message->message }}</p>
                <p class="date">{{ date('d M y, h:i a', strtotime($message->created_at)) }}</p>
            </div>
        </li>
        @endforeach
    </ul>
</div>

<div class="input-text">
    <input type="text" name="message" id="inputmessage" class="submit" autofocus>
    <form name="data-form" id="dataform" class="data-form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="file" name="image" id="image" class="image">
        <input type="submit" value="Send">
    </form>
</div>

<script>
    $(document).on('keyup', '.input-text input', function(e) {
        var message = $(this).val();

        // check if enter key is pressed and message is not null also receiver is selected
        if (e.keyCode == 13 && message != '' && receiver_id != '') {
            $(this).val(''); // while pressed enter text box will be empty

            var datastr = "receiver_id=" + receiver_id + "&message=" + message;

            $.ajax({
                type: "post",
                url: "message", // need to create this post route
                data: datastr,
                cache: false,
                success: function(data) {

                },
                error: function(jqXHR, status, err) {},
                complete: function() {
                    scrollToBottomFunc();
                }
            })

            //  $('#inputmessage').attr('autofocus');

        }
        //$("#Box1").

    });


    $('#dataform').submit(function(event) {
        event.preventDefault();
        var image = document.getElementById('image').files;
        //var data = new FormData($('#dataform')[0]);
        // {
        //     'receiver_id': receiver_id,
        //     'image': image
        // };

            console.log(image);
        axios({
            method: 'post',
            url: 'test123',
            data: {
                name : "Mousa",
                surname: "Kalouk",
                age: 23,
                description: "sdafsdf",
                image: image,
               
            }
        });

        //console.log(data);

        // $.post("/test123", {
        //     data: data
        //     }, function(data, status) {
        //         alert('value stored');
        //     });

        // $.ajax({
        //     url: "test123",
        //     method: 'POST',
        //     data: data,

        //     success: function(result) {

        //     },
        //     error: function(data) {
        //         console.log(data);
        //     }
        // });

    });
</script>