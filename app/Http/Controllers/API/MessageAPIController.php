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
        if ($request->header('devicetoken')) {

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
                ->get(['users.id', 'users.device_token', 'users.name', 'users.avatar', 'messages.message', 'messages.created_at', 'messages.is_read'])
                ->unique('id');
            $response = [];
            foreach ($users as $key => $usermessag) {

                $usermessag->avatar = asset('storage/Avatar') . '/' . $usermessag->avatar;
                if ($users[$key]['device_token'] == $user->device_token) {
                    unset($users[$key]);
                    continue;
                }
                $response[] = $usermessag;
            }
            if (count($users) == 0)
                $response = [];

            $support = User::find(1);

                $response['support'] = [
                    'id'            => $support->id,
                    'device_token'  => $support->device_token,
                    'name'          => $support->name,
                    'avatar'        => asset('storage/Avatar') . '/' . $support->avatar
                ];

            return $this->sendResponse($response, 'Contacts retrieved successfully');
        } else {
            return $this->sendError('User not found', 401);
        }
    }

    public function getMessage(Request $request)
    {

        if ($request->header('devicetoken')) {

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

        if ($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }

            $message          = new Message();

            $message->from    = $user->device_token;

            $message->to      = $request->to;

            $message->message = $request->message;

            $message->is_read = 0;

            if ($message->save()) {

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

                $data = ['from' => $message->from, 'to' => $message->to, 'message' => $message->message]; // sending from and to user id when pressed enter
                $pusher->trigger('yaman-channel', 'messaging-event', $data);



                //for send notification 

                $reciver = User::where('device_token', $request->to)->first();

                $SERVER_API_KEY = 'AAAA71-LrSk:APA91bHCjcToUH4PnZBAhqcxic2lhyPS2L_Eezgvr3N-O3ouu2XC7-5b2TjtCCLGpKo1jhXJqxEEFHCdg2yoBbttN99EJ_FHI5J_Nd_MPAhCre2rTKvTeFAgS8uszd_P6qmp7NkSXmuq';

                $headers = [
                    'Authorization: key=' . $SERVER_API_KEY,
                    'Content-Type: application/json',
                ];

                $data = [
                    "registration_ids" => array($reciver->fcm_token),
                    "notification" => [
                        "title"    => config('notification_lang.Notification_SP_message_title_' . $reciver->language),
                        "body"     =>  $user->name . ' ' . config('notification_lang.Notification_SP_message_body_' . $reciver->language) . ' ' . $request->message
                    ]
                ];

                $dataString = json_encode($data);

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);


                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                //return dd(curl_exec($ch));
                $response = curl_exec($ch);


                return $this->sendResponse([], 'Message saved successfully');
            }

            return $this->sendError('Message not saved', 401);
        }
    }
}
