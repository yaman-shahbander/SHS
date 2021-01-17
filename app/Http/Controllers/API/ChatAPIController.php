<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chat;

class ChatAPIController extends Controller
{
    public function store(Request $request)
    {
        try{
            $restaurant_id = $request->input('restaurant_id');
            $order_id = $request->input('order_id');
            $chat = Chat::where('restaurant_id',$restaurant_id)->where('order_id',$order_id)->first();
            if(!isset($chat))
            {
                $chat = new Chat();
                $chat->order_id = $request->input('order_id');
                $chat->restaurant_id = $request->input('restaurant_id');
                $chat->user_id = $request->input('user_id');
                $chat->save();
                return $this->sendResponse(true, 'chat created successfully');
            }
            return $this->sendResponse(true, 'chat already exist');
            
            
        }
        catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        
    }
}
