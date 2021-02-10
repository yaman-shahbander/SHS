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

    /** @var  CategoryRepository */
    private $TransferTransactionRepository;

    public function __construct(TransferTransactionRepository $TransferTransactionRepo)
    {
        parent::__construct();
        $this->TransferTransactionRepository = $TransferTransactionRepo;
    }

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

                if ($vendor->balance_id == null) {
                    return $this->sendError('There is no balance in vendor account', 401);
                }

                if ($user->balance_id == null) {
                    return $this->sendError('There is no balance in your account', 401);
                }
                    
                if ($user->Balance->balance - $request->amount >= 0) {
                    $user->Balance->balance   = $user->Balance->balance - $request->amount;
                    $vendor->Balance->balance = $vendor->Balance->balance + $request->amount;
                    $user->Balance->save();
                    $vendor->Balance->save();
                    return $this->sendResponse([], 'Money transfered successfully');
                } else {
                    return $this->sendError('There is no enough balance in your account', 401);
                }
            }
      } catch (\Exception $e) {
          return $this->sendError('Something is worng', 401);
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


                if ($userBalance == null) return $this->sendError([], 'No balance for the current user');

                $response = [
                    'balance' => $userBalance->balance
                ];

                return $this->sendResponse($response, 'Balance retrieved successfuly!');
            }
    } catch(\Exception $e) {
        return $this->sendError('Error', 401);
    }
  }
   
}
