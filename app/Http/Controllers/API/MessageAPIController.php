<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Chat;
use Pusher\Pusher;
use DB;

class MessageApiController extends Controller
{

    public function index(Request $request)
    {
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }

            $users = DB::select("select users.device_token, users.name, users.avatar, users.email ,count(is_read) as unread 
             from users LEFT JOIN  messages ON users.device_token = messages.from and is_read = 0 and messages.to = ' . $user->device_token . '
             where users.device_token != ' . $user->device_token . ' 
             group by users.device_token, users.name, users.avatar, users.email");


             foreach($users as $user) {
                $user->avatar = asset('storage/Avatar') . '/' . $user->avatar;
             }

        }

        return $this->sendResponse($users, 'Contacts retrieved successfully');
    }

    public function getMessage(Request $request)
    {

        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }
        }

        // Make read all unread message
        Message::where(['from' => $request->to, 'to' => $user->device_token])->update(['is_read' => 1]);

        $user_id = $request->to;
        $my_id   = $user->device_token;

        // Get all message from selected user
        $messages = Message::where(function ($query) use ($user_id, $my_id) {
            $query->where('from', $user_id)->where('to', $my_id);
        })->oRwhere(function ($query) use ($user_id, $my_id) {
            $query->where('from', $my_id)->where('to', $user_id);
        })->get(['from', 'to', 'message', 'is_read', 'created_at']);

        return $this->sendResponse($messages, 'Messages retrieved successfully');
    }

    public function store(Request $request)
    {

        if($request->header('devicetoken')) {

         $user = User::where('device_token', $request->header('devicetoken'))->first();

         if (empty($user)) {
             return $this->sendError('User not found', 401);
         }
       
         $message          = new Message();

         $message->from    = $user->device_token;

         $message->to      = $request->to;

         $message->message = $request->message;

         $message->is_read = 0;   

         if($message->save()){

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
            
            $data = response()->json([
                'from' => $message->from,
                'to' => $message->to,
                'message' => $message->message
            ]);

            $pusher->trigger('yaman-channel', 'messaging-event', $data);
            return $this->sendResponse([], 'Message saved successfully');
         }
         
           return $this->sendError('Message not saved', 401);
        
        }
        
        // message - sender_id - chat_id
        // order_id - restaurant_id - sender_id  - message
        
        // try{
        //     $is_customer = $request->input('is_customer');;
        //     $restaurant_id = $request->input('restaurant_id');
        //     $order_id = $request->input('order_id');
        //     $chat = Chat::where('restaurant_id',$restaurant_id)->where('order_id',$order_id)->first();
        //     $chat_id = $chat->id;
        //     $sender_id = $request->input('sender_id');
        //     $msg = $request->input('message');
        //     $message = new Message();
        //     $message->chat_id = $chat_id;
        //     $message->sender_id = $sender_id;
        //     $message->msg = $msg;
        //     if(isset($is_customer))
        //     {
        //         $message->is_customer = $is_customer;
        //     }
        //     $message->save();
            
        //     return $this->sendResponse(true, 'message created successfully');
        // }
        // catch (Exception $e) {
        //     return $this->sendError($e->getMessage());
        // }
        



    }
}
