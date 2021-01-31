<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function forgot_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                    $message->subject($this->getEmailSubject());
                });
                switch ($response) {
                    case Password::RESET_LINK_SENT:
                        return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                    case Password::INVALID_USER:
                        return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
                }
            } catch (\Swift_TransportException $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            } catch (Exception $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            }
        }
        return \Response::json($arr);
    }

    public function change_password(Request $request)
    {
        if($request->header('devicetoken')) {
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
        $input = $request->all();
        $useridPassword =$user->password;

        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:8',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
              //  return ''.request('old_password').(Hash::check(request('old_password'), $useridPassword));

                if ((Hash::check(request('old_password'), $useridPassword)) == false) {
                    return $this->sendError('Check your old password.');


                   // $response = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), $useridPassword)) == true) {
                    return $this->sendError("Please enter a password which is not similar then current password.");

                 //   $response = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    $user->password= Hash::make($input['new_password']);
                    $user->save();
                    $response = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $response = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        return $this->sendResponse($response, 'Password Updated Successfully');
            }
            catch (\Exception $e) {
                return $this->sendError('error save', 401);
            }
        }
        else {
            return $this->sendError('error!', 401);
        }
    }


    public function change_phone(Request $request)
    {
        if($request->header('devicetoken')) {
        $input = $request->all();

        $userid = User::where('device_token', $request->header('devicetoken'))->get('id');

        $userid = $userid[0]->id;

        $useridPhone =(User::find($userid))->phone;

        $rules = array(
            'new_number' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                 if (request('new_number') == $useridPhone) {
                    $response = array("status" => 400, "message" => "Please enter a phone number which is not similar then current phone number.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['phone' => $input['new_number']]);
                    $response = array("status" => 200, "message" => "phone number updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $response = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        return $this->sendResponse($response, 'Phone Number Updated Successfully');
    }
  }
}
