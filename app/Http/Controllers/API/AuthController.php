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
        $input = $request->all();
        $userid = $input['id'];
        $useridPassword =( User::find($userid))->password; 
         
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), $useridPassword)) == false) {
                    $response = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), $useridPassword)) == true) {
                    $response = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
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


    public function change_phone(Request $request)
    {
        $input = $request->all();
        $userid = $input['id'];
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
