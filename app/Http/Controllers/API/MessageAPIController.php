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

            //  $users = DB::select("select users.device_token, users.name, users.avatar, users.email ,count(is_read) as unread
            //  from users left join messages on  users.device_token = messages.from and is_read = 0 and messages.to = ' . $user->device_token . '
            //  and users.device_token != ' . $user->device_token . '
            //  group by users.device_token, users.name, users.avatar, users.email");

            // $response = [];

            // $users = DB::select( "SELECT  u1.avatar, u1.device_token, CASE WHEN u2.device_token = '$user->device_token'
            // THEN u1.name
            // ELSE u2.name
            // END as user_name
            // FROM messages m
            // JOIN users u1 ON m.from=u1.device_token
            // JOIN users u2 ON m.to=u2.device_token
            // WHERE m.id IN (
            // SELECT MAX(id)
            // FROM messages
            // WHERE messages.from = '$user->device_token' OR messages.to = '$user->device_token'
            // GROUP BY messages.id) ORDER BY m.id DESC
            // ;");

            // foreach($users as $user) {
            //     $user->avatar= asset('storage/Avatar') . '/' . $user->avatar;
            //     $response['chats'][] = $user;
            // }

            // $device_token = $user->device_token;
            // $collocutor = User::orWhereHas('messages_to', function ($q) use ($device_token) {
            //     $q->where('from', $device_token);
            //      })->orWhereHas('messages_to', function ($q) use ($device_token) {
            //     $q->where('to', $device_token);
            //  });

//            $users = DB::select("select m.from, m.to, u.avatar, m.is_read, m.created_at from messages m, users u where u.device_token = m.to and m.from = '$user->device_token' or m.to = '$user->device_token' group by m.from, m.to ");
//
//            foreach($users as $user) {
//                $user->avatar= asset('storage/Avatar') . '/' . $user->avatar;
//                $response['chats'][] = $user;
//            }


            $users = Message::join('users',  function ($join) {
                $join->on('messages.from', '=', 'users.device_token')
                    ->orOn('messages.to', '=', 'users.device_token');
            })
                ->where('messages.from','32345ad6b67d36e8dd1e93751eafe59512eb')
                ->orWhere('messages.to','32345ad6b67d36e8dd1e93751eafe59512eb')
                ->orderBy('messages.created_at', 'desc')
                ->get(['users.id','users.device_token','users.name','users.avatar','messages.message'])
                ->unique('id');

            foreach ($users as $key=>$usermessag){

                $usermessag->avatar= asset('storage/Avatar') . '/' . $usermessag->avatar;
                if($users[$key]['device_token']==$user->device_token)
                    
                    unset($users[$key]);
            }

        }




        return $this->sendResponse($users->toArray(), 'Contacts retrieved successfully');
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
