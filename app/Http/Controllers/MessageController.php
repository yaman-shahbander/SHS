<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class MessageController extends Controller
{
    public function index()
    {
        // select all users except logged in user
        // $users = User::where('id', '!=', Auth::id())->get();

        // count how many message are unread from the selected user

        $auth_device_token = Auth::user()->device_token;

        $users = DB::select("select users.device_token, users.name, users.avatar, users.email, count(is_read) as unread 
        from users LEFT  JOIN  messages ON users.device_token = messages.from and is_read = 0 and messages.to = ' . $auth_device_token . '
        where users.device_token != ' . $auth_device_token . ' 
        group by users.id, users.name, users.avatar, users.email");

        return view('home', ['users' => $users]);
    }

    public function getMessage($user_token)
    {

        $auth_device_token = Auth::user()->device_token;
        
        // Make read all unread message
        Message::where(['from' => $user_token, 'to' => $auth_device_token])->update(['is_read' => 1]);

        // Get all message from selected user
        $messages = Message::where(function ($query) use ($user_token, $auth_device_token) {
            $query->where('from', $user_token)->where('to', $auth_device_token);
        })->oRwhere(function ($query) use ($user_token, $auth_device_token) {
            $query->where('from', $auth_device_token)->where('to', $user_token);
        })->get();

        return view('messages.index', ['messages' => $messages]);

         
    }

    // $my_id = Auth::id();

    // // Make read all unread message
    // Message::where(['from' => "343046bfbbedaef29b023472b9f5b50197806868", 'to' => "343046bfbbedaef29b023472b9f5b5019780"])->update(['is_read' => 1]);

    // // Get all message from selected user
    // $messages = Message::where(function ($query)  {
    //     $query->where('from', "343046bfbbedaef29b023472b9f5b5019780")->where('to', "343046bfbbedaef29b023472b9f5b50197806868");
    // })->oRwhere(function ($query) {
    //     $query->where('from', "343046bfbbedaef29b023472b9f5b50197806868")->where('to', "343046bfbbedaef29b023472b9f5b5019780");
    // })->get();

    // return view('messages.index', ['messages' => $messages]);
      

    public function sendMessage(Request $request)
    {
        $from = Auth::user()->device_token;
        $to = $request->receiver_id;

        $message = $request->message;

        $data = new Message();
        $data->from = $from;
        $data->to = $to;
        $data->message = $message;
        $data->is_read = 0; // message will be unread when sending message
        $data->save();

        // pusher
        $options = array(
            'cluster' => 'ap2',
            'useTLS' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data = ['from' => $from, 'to' => $to, 'message' => $message]; // sending from and to user id when pressed enter
        $pusher->trigger('yaman-channel', 'messaging-event', $data);
    }

    public function getChats(Request $request) {
        $search = $request->searchValue;

        $Users = User::where('name', 'LIKE', "%".$search."%")
                     ->Orwhere("email", "LIKE", "%".$search."%")
                     ->get(['name', 'email', 'avatar', 'device_token']);

        if ($search == "emptyValue") {
            $Users = User::all('name', 'email', 'avatar', 'device_token');
        }
        
        $Users->transform(function($q) {
            $q['avatar'] = asset('/storage/Avatar') . '/' . $q['avatar'];
            return $q;
        });

        return $Users;
    }
    
}
