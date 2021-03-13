<div class="message-wrapper">
    <ul class="messages">
        @foreach($messages as $message)
        <li class="message clearfix">
            {{--if message from id is equal to auth id then it is sent by logged in user --}}
            <div class="{{ ($message->from == Auth::user()->device_token) ? 'sent' : 'received' }}">
                <p>{{ $message->message }}</p>
                <p class="date">{{ date('d M y, h:i a', strtotime($message->created_at)) }}</p>
                @if($message->type == 'image')
                    <a href="{{ asset('storage/chat') . '/' . $message->fileName }}" download>
                         <img src="{{ asset('storage/chat') . '/' . $message->fileName }}" alt="" width="250px" height="100px">
                    </a>

         @elseif($message->type == 'video')
                     
         <video width="320" height="240" controls>
  <source src="{{ asset('storage/chat') . '/' . $message->fileName }}" type="video/mp4">

</video>
                @endif

       
            </div>
        </li>
        @endforeach
    </ul>
</div>

<div class="input-text">
    <input type="text" name="message" id="inputmessage" class="submit" autofocus>
    <input type="file" name="image" id="image" class="image"  accept=".png,.gif, .jpeg,.mp3,.mp4,.wav,.wma,.webm,.mov,.wmv,.mpeg,.mpg">
    <!-- <input type="submit" id="submit" value="Send"> -->
</div>
<script>
$("#image").change(function () {
    $('#inputmessage').focus();
});</script>
<script>
    $(document).on('keyup', '.input-text #inputmessage', function(e) {
        var message = $(this).val();

        var image = document.getElementById('image').files;

        // check if enter key is pressed and message is not null also receiver is selected
        if ((e.keyCode == 13 && message != '' && receiver_id != '') || (e.keyCode == 13 && image.length != 0 && receiver_id != '')) {
            $(this).val(''); // while pressed enter text box will be empty

            var data = new FormData();

            data.append('file', image[0]);

            data.append('message', message);

            data.append('receiver_id', receiver_id);

            document.getElementById('image').value = ''; // while pressed enter text box will be empty

            //var datastr = "receiver_id=" + receiver_id + "&message=" + message;

            axios.post('message', data).then(function(response) {
                scrollToBottomFunc();
            });

            // $.ajax({
            //     type: "post",
            //     url: "message", // need to create this post route
            //     data: datastr,
            //     cache: false,
            //     success: function(data) {

            //     },
            //     error: function(jqXHR, status, err) {},
            //     complete: function() {
            //         scrollToBottomFunc();
            //     }
            // })
        }
    });


    // $('#submit').click(function(event) {
    //     event.preventDefault();

    //     var data = new FormData();

    //     data.append('file', document.getElementById('image').files[0]);

    //     //axios.post('test123', data);

    //     //

    //     // console.log(image);
    //     // axios({
    //     //     method: 'post',
    //     //     url: 'test123',
    //     //     data: {
    //     //         name: "Mousa",
    //     //         surname: "Kalouk",
    //     //         age: 23,
    //     //         description: "sdafsdf",
    //     //         image: data,

    //     //     }
    //     // });

    //     //console.log(data);

    //     // $.post("/test123", {
    //     //     data: data
    //     //     }, function(data, status) {
    //     //         alert('value stored');
    //     //     });

    //     // $.ajax({
    //     //     url: "test123",
    //     //     method: 'POST',
    //     //     data: {
    //     //         name : 'ss',
    //     //         age : 12,
    //     //         iamge : data
    //     //     },

    //     //     success: function(result) {

    //     //     },
    //     //     error: function(data) {
    //     //         console.log(data);
    //     //     }
    //     // });

    // });
</script>