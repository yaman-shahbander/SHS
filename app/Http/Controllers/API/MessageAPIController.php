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


            $users = Message::join('users',  function ($join) {
                $join->on('messages.from', '=', 'users.device_token')
                    ->orOn('messages.to', '=', 'users.device_token');
            })
                ->where('messages.from', $user->device_token)
                ->orWhere('messages.to', $user->device_token)
                ->orderBy('messages.created_at', 'desc')
                ->get(['users.id','users.device_token','users.name','users.avatar','messages.message','messages.created_at', 'messages.is_read'])
                ->unique('id');

            foreach ($users as $key=>$usermessag){
                $usermessag->avatar= asset('storage/Avatar') . '/' . $usermessag->avatar;
                if($users[$key]['device_token']==$user->device_token)
                    unset($users[$key]);
            }

            $response[]=$users->toArray();
            return $this->sendResponse($response, 'Contacts retrieved successfully');

        } else {
            return $this->sendError('User not found', 401);
        }
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

//            $data = response()->json([
//                'from' => $message->from,
//                'to' => $message->to,
//                'message' => $message->message,
//                'created_at' => $message->created_at
//
//            ]);

             $data = ['from' => $message->from, 'to' => $message->to, 'message' => $message->message]; // sending from and to user id when pressed enter
             $pusher->trigger('yaman-channel', 'messaging-event', $data);
            return $this->sendResponse([], 'Message saved successfully');
         }

           return $this->sendError('Message not saved', 401);

        }
    }
}
