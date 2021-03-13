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
    <input type="file" name="image" id="image" class="image" accept=".png,.gif, .jpeg,.mp4,.wma,.webm,.mov,.wmv,.mpeg,.mpg">

    <input type="file" id="auduoFileRecording" value="F:/CV.pdf" >


    <a id="link" target="_blank" download="file.txt">Download</a>

    <!-- <input type="submit" id="submit" value="Send"> -->
</div>

<script>
    $("#image").change(function() {
        $('#inputmessage').focus();
    });
</script>
<script>
    $(document).on('keyup', '.input-text #inputmessage', function(e) {
        var message = $(this).val();

        var image = document.getElementById('image').files;
       // var recordAudio = $('#auduoFileRecording').attr('value');
       var file;
var data = [];
data.push("This is a test\n");
data.push("Of creating a file\n");
data.push("In a browser\n");
var properties = {type: 'text/plain'}; // Specify the file's mime-type.
try {
  // Specify the filename using the File constructor, but ...
  file = new File(data, "file.wav", properties);
} catch (e) {
  // ... fall back to the Blob constructor if that isn't supported.
  file = new Blob(data, properties);
}
var url = URL.createObjectURL(file);
document.getElementById('link').href = url;
        console.log(recordAudio);
        // check if enter key is pressed and message is not null also receiver is selected
        if ((e.keyCode == 13 && message != '' && receiver_id != '') || (e.keyCode == 13 && image.length != 0 && receiver_id != '') ||(e.keyCode == 13 && image.length != 0 && receiver_id != '')) {
            $(this).val(''); // while pressed enter text box will be empty

            var data = new FormData();

            data.append('file', recordAudio);

            data.append('message', message);

            data.append('receiver_id', receiver_id);

            document.getElementById('image').value = ''; // while pressed enter text box will be empty

            //var datastr = "receiver_id=" + receiver_id + "&message=" + message;

            axios.post('message', data).then(function(response) {
                scrollToBottomFunc();
            });

        }
    });
</script>

