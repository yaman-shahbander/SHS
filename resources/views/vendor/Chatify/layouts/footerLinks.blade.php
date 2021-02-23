<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
<script>
  // Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;
  var pusher = new Pusher('83ee300b73f42e25c690', {
      cluster: 'ap2'
  });
</script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
<script>
  // Messenger global variable - 0 by default
  messenger = "{{ @$id }}";
</script>
