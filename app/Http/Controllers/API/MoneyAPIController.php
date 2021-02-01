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

    public function history(Request $request) {
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }

            $transactionsHistory = TransferTransaction::where('from_id', $user->id)->get(['from_id', 'to_id', 'amount', 'created_at']);
            return $this->sendResponse($transactionsHistory->toArray(), 'History retrieved successfully');
        }
    }

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
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();

            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }

            
            $userBalanceId = $user->balance_id;

            $userBalance   = Balance::find($userBalanceId);

            if ($userBalance == null) return $this->sendResponse($userBalance, 'failed to retrieve!');

            else return $this->sendResponse($userBalance->balance, 'Balance retrieved successfuly!');
        }
    }
}
