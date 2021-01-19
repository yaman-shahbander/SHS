<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use App\Facades\ChatifyMessenger as Chatify;
use DB;

class ChatAPIController extends Controller
{
    public function history(Request $request) {
        $id=$request->id;
        // $messages = Message::where('from_id', $request->id)->orderBy('created_at','desc')->distinct('to_id')->get();
        // $message = Message::latest()->where('from_id', 1)->get();
//   $max_created=new dateTime();
        // return Message::where('from_id', $request->id)
		// 	->orWhere('to_id', $request->id)
        //     ->orderBy('created_at', 'desc')
        //     ->get()
        //     ->unique('id');
            // ->getlast(function($query) use($id){
            //     if($query->to_id==$id)

            //     return $query->
            // })

        //     $users = Message::join('users',  function ($join) {
        //         $join->on('messages.from_id', '=', 'users.id')
        //             ->orOn('messages.to_id', '=', 'users.id');
        //     })
        //         ->where('messages.from_id', $request->id)
        //         ->orWhere('messages.to_id', $request->id)
        //         ->orderBy('messages.created_at', 'desc')
        //         ->get()
        //         ->unique('id');
    
        //     if ($users->count() > 0) {
        //         // fetch contacts
        //         $contacts = null;
        //         foreach ($users as $user) {
        //             if ($user->id != $request->id) {
        //                 // Get user data
        //                 $userCollection = User::where('id', $user->id)->first();
        //                 $contacts .= Chatify::getContactItem($request['messenger_id'], $userCollection);
        //             }
        //         }
        //     }
        // $response = [];
        // return $chats = Message::select(DB::raw('*, max(created_at) as created_at'))
        // ->where('from_id',$request->id)
        // ->orWhere('to_id',$request->id)
        // ->orderBy('created_at', 'desc')
        // ->groupBy('to_id')
        // ->get();

        $users = DB::select( DB::raw(
            "SELECT * FROM messages msg

             INNER JOIN ( 

                  SELECT max(created_at) created_at FROM messages GROUP BY to_id ORDER BY created_at

            ) m2 

            ON msg.created_at = m2.created_at
            "
     ) 
  );

        return $users;
        // foreach($messages as $message) {
        //     $response[] = [
        //         $message
        //     ];
        // }
       
    }
}
