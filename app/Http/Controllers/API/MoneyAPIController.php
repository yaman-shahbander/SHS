<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TransferTransaction;
use App\Balance;
use App\Repositories\TransferTransactionRepository;
use DB;
use Carbon\Carbon;


class MoneyAPIController extends Controller
{

    /** @var  CategoryRepository */
    private $TransferTransactionRepository;

    public function __construct(TransferTransactionRepository $TransferTransactionRepo)
    {
        parent::__construct();
        $this->TransferTransactionRepository = $TransferTransactionRepo;
    }

    public function history(Request $request) {
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }
            // date("g:i A"
            $transactionsHistory = $user->ToUserName->transform(function($q){
               return $q['created_at']=  Carbon::createFromFormat('Y-m-d H:i:s', $q->pivot->created_at)->format('Y-m-d H:i A');
               
            //    strtotime($q->pivot->created_at);
                // 'amount', 'created_at'
            });

            return $this->sendResponse($transactionsHistory->toArray(), 'History retrieved successfully');
        }
    }

    /*
    $time = strtotime('2010-04-28 17:25:43'); 
    echo 'event happened '.humanTiming($time).' ago'; 

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
    return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':''); } 
    */

    public function transferMoney(Request $request) {
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }

            $response['from_id'] = $user->id;//sender
            $response['to_id']   = $request->to_id;// Receiver
            $response['amount']  = $request->amount;// amount
            $fromUserBalanceID   = $user->balance_id;
            $toUserBalanceID     = User::find($response['to_id'])->balance_id;
            $fromUserBalance     = Balance::find($fromUserBalanceID)->balance;
            $toUserBalance       = Balance::find($toUserBalanceID)->balance;
            if ($fromUserBalance - $response['amount'] >= 0) {
                $subtractedAmount = $fromUserBalance - $response['amount'];
                $addAmountToUser  = $toUserBalance + $response['amount'];
                DB::table('balances')->where('id', $fromUserBalanceID)->update(['balance' => $subtractedAmount]);
                DB::table('balances')->where('id', $toUserBalanceID)->update(['balance' => $addAmountToUser]);
                $transfer = $this->TransferTransactionRepository->create($response);
                return $this->sendResponse($response, 'Money transfered successfully!');
            } else {
                return $this->sendResponse($response, 'failed! Not enough money');
            }
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
