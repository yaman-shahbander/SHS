<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TransferTransaction;
use App\Balance;
use App\Repositories\TransferTransactionRepository;
use DB;



class MoneyAPIController extends Controller
{

    function humanTiming ($time) {
        $time = time() - $time;
        // to get the time since that moment $time = ($time<1)? 1 : $time;
        $tokens = array ( 31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second' );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
         }
      }

    public function history(Request $request) {
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }
            $response = [];

            $transactionsHistory = $user->ToUserName->transform(function($q){
              $q['amount']       = round($q->pivot->amount,2);
              $q['payment_id']   = $q->payment_id;
              $q['payment_time'] = $this->humanTiming(strtotime($q->pivot->created_at));
              $q['type']         = $q->pivot->type;
              return $q->only('amount', 'payment_id', 'payment_time', 'type');

            });

            return $this->sendResponse($transactionsHistory->toArray(), 'History retrieved successfully');
        }
    }


    public function transferMoney(Request $request) {
        try {
            if($request->header('devicetoken')) {

                $user = User::where('device_token', $request->header('devicetoken'))->first();

                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }

                $vendor = User::where('payment_id', $request->vendor_payment_id)->first();

                $amount  = strip_tags($request->amount);

                //If the field has any character
                if(preg_match('/[a-zA-Z]/', $amount)) { 
                    return $this->sendError('The field must contain only numbers', 401);
                 }
                 
                 //If the field has any special character
                 if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) { 
                    return $this->sendError('The field must contain only numbers', 401);
                }
                // Transfering to the same user
                if ($user->payment_id == $request->vendor_payment_id) {
                    return $this->sendError('you cant transfer money to yourself ', 401);
                }

                //Vendor has no balance account
                if ($vendor->balance_id == null) {
                    return $this->sendError('There is no balance in vendor account', 401);
                }

                //homeowner has no balance account
                if ($user->balance_id == null) {
                    return $this->sendError('There is no balance in your account', 401);
                }

                //amount is empty
                if (empty($amount)) {
                    return $this->sendError('Transfer failed! amount should have a value', 401);
                 }

                 //amount should not be negative
                 if ($amount < 0) {
                    return $this->sendError('Transfer failed! amount should not be negative', 401);
                 } 
                

                if ($user->Balance->balance - $amount >= 0) {
                    $user->Balance->balance   = $user->Balance->balance - $amount;
                    $vendor->Balance->balance = $vendor->Balance->balance + $amount;
                    $user->Balance->save();
                    $vendor->Balance->save();

                    $transfer=[['from_id'=>$user->id,'to_id'=>$vendor->id,'amount'=>$amount]];

                    $user->ToUserName()->attach($transfer);

                    
        //for send notification 
        $SERVER_API_KEY = 'AAAA71-LrSk:APA91bHCjcToUH4PnZBAhqcxic2lhyPS2L_Eezgvr3N-O3ouu2XC7-5b2TjtCCLGpKo1jhXJqxEEFHCdg2yoBbttN99EJ_FHI5J_Nd_MPAhCre2rTKvTeFAgS8uszd_P6qmp7NkSXmuq';

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $data = [
            "registration_ids" => array($vendor->fcm_token),
            "notification" => [
                "title"    => config('notification_lang.Notification_SP_transfer_title_' . $vendor->language),
                "body"     =>  $user->name . ' '.config('notification_lang.Notification_SP_transfer_body_' . $vendor->language)
            ]
        ];

        $datayou = [
            "registration_ids" => array($user->fcm_token),
            "notification" => [
                "title"    => config('notification_lang.Notification_SP_transfer_title_' . $user->language),
                "body"     =>  config('notification_lang.Notification_SP_transfer_body_you_' . $user->language ) . ' ' . $vendor->name
            ]
        ];

        $dataString = json_encode($data);

        $datayouString = json_encode($datayou);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
         //return dd(curl_exec($ch));

        $response = curl_exec($ch);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $datayouString);

        $response = curl_exec($ch);


                    return $this->sendResponse([], 'Money transfered successfully');
                } else {
                    return $this->sendError('There is no enough balance in your account', 401);
                }
            }
      } catch (\Exception $e) {
          return $this->sendError($e->getMessage(), 401);
      }
    }

    public function currentBalance(Request $request) {
        try {
            if($request->header('devicetoken')) {

                $user = User::where('device_token', $request->header('devicetoken'))->first();

                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }

                $userBalance   = $user->Balance;


                if ($userBalance == null) return $this->sendError( 'No balance for the current user',401);

                $response = [
                    'balance' => $userBalance->balance,
                    'payment_id'=>$user->payment_id
                ];

                return $this->sendResponse($response, 'Balance retrieved successfuly!');
            }
    } catch(\Exception $e) {
        return $this->sendError('Error', 401);
    }
  }

}
