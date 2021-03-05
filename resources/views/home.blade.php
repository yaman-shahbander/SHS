@extends('layoutschat.app')

<style>

.search-bar {
  height: 44px;
  width: 425px;
  border-radius: 40px;
  display: flex;
  align-items: center;
  padding: 0 0 0 20px;
  position: relative;
  background: #fff;
  margin-bottom: 10px
}

.input {
  border: none;
  height: 25px;
  width: 150px;
  color: #1b1b1b;
  font-size: 15px;
  outline: none;
}

.input:not(:placeholder-shown) + .label {
  font-size: 9px;
  top: 3px;
  color: #00c853;
}

.input:focus ~ .label {
  font-size: 9px;
  top: 3px;
  color: #4279a3;
  transition: all 0.5s ease;
}

.label {
  color: #aaaaaa;
  position: absolute;
  top: 13px;
  pointer-events: none;
  transition: all 0.5s ease;
}

.highlight {
  width: 0px;
  height: 1px;
  background: #4279a3;
  position: absolute;
  bottom: 8px;
  transition: all 1s ease;
}

.input:focus ~ .highlight {
  width: 250px;
  transition: all 1s ease;
}

.input {
    margin: 0 25px 0 0 !important
}

</style>

@section('content')

    <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-4">

            <div class="search-bar">
                <input type="text" id="search" class="input" placeholder="Search..." onkeyup="searchFunction()">  
                <span class="highlight"></span>
            </div>
           
                <div class="user-wrapper" id="user-wrapper">
                    <ul class="users" id="test">
                        @foreach($users as $user)
                            <li class="user" id="{{ $user->device_token }}" >
                                {{--will show unread count notification--}}
                                @if($user->unread)
                                    <span class="pending">{{ $user->unread }}</span>
                                @endif

                                <div class="media">
                                    <div class="media-left">
                                        <img src="{{ asset('storage/Avatar/' . $user->avatar) }}" alt="" class="media-object">
                                    </div>

                                    <div class="media-body">
                                        <p class="name">{{ $user->name }}</p>
                                        <p class="email">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            <div class="col-md-8" id="messages">

            </div>
        </div>
    </div>
    
@endsection


<script>

    function searchFunction() {
        var elemVal = document.getElementById("search").value;
         $.ajax({
            url : "{{ url('api/search') }}",
            type: "POST",
            data: { searchValue: elemVal },
            success: function(Users) {
                $div = $("#user-wrapper");
                $ul = $("#user-wrapper").find('ul');

                $ul.empty();

                Users.forEach(function(User) {
                    var li = document.createElement('li');
                    li.className = "user";
                    li.id = User["device_token"];

                    var divMedia = document.createElement('div');
                    divMedia.className = "media";

                    var mediaLeft = document.createElement('div');
                    mediaLeft.className = "media-left";

                    var img = document.createElement('img');
                    img.className = "media-object";
                    img.src = User["avatar"];

                    var mediaBody = document.createElement('div');
                    mediaBody.className = "media-body";

                    var pName = document.createElement('p');
                    pName.className = "name";
                    pName.innerHTML = User['name'];


                    var pEmail = document.createElement('p');
                    pEmail.className = "email";
                    pEmail.innerHTML = User['email'];

                    mediaBody.appendChild(pName);

                    mediaBody.appendChild(pEmail);

                    mediaLeft.appendChild(img);

                    divMedia.appendChild(mediaLeft);

                    divMedia.appendChild(mediaBody);

                    li.appendChild(divMedia);
                    
                   // $ul.insertBefore(li, $ul.firstChild);

                   $ul.prepend(li);   

                }); // foreach end
                 $div.append($ul);   
            } // success fundtion end
         });
    }


    document.addEventListener("click", function(event){
        var targetElement = event.target || event.srcElement;

        if(targetElement.className == "name" || targetElement.className == "email") {
            var parentID = targetElement.parentElement.parentElement.parentElement.id;

        } else if (targetElement.className == "media-object") {
            var parentID = targetElement.parentElement.parentElement.parentElement.id;

        } else if (targetElement.className == "media-body") {
            var parentID = targetElement.parentElement.parentElement.id;
        } else if (targetElement.className == "user") {
            var parentID = targetElement.id; //Not parent but the same element
        } 

        if (parentID != null) {
            receiver_id = parentID;
            $.ajax({
                type: "get",
                url: "message/" + receiver_id, // need to create this route
                data: "",
                cache: false,
                success: function (data) {
                    $('#messages').html(data);
                    
                    scrollToBottomFunc();
                }
            });
        }
        
    });
   
</script>


