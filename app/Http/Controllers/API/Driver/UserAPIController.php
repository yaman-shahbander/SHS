<?php
/**
 * File name: UserAPIController.php
 * Last modified: 2020.05.21 at 17:25:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers\API\Driver;

use App\Events\UserRoleChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Country;
use App\Models\reviews;
use Validator;
use PHPMailer\PHPMailer\PHPMailer;

class UserAPIController extends Controller
{
    private $userRepository;
    private $uploadRepository;
    private $roleRepository;
    private $customFieldRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, UploadRepository $uploadRepository, RoleRepository $roleRepository, CustomFieldRepository $customFieldRepo)
    {
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
        $this->roleRepository = $roleRepository;
        $this->customFieldRepository = $customFieldRepo;
    }

    function login(Request $request)
    {
        $IsEmail = false;

        try {
            if(empty($request->header('devicetoken'))){
                return $this->sendError('nothing to process', 401);

            }
            if($request->email){

                $this->validate($request, [

                    'email' => 'required|email',
                    'password' => 'required',
                ]);
                if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                    $user = auth()->user();
                }
                $IsEmail = true;


            }
            elseif($request->phone){
                $this->validate($request, [

                    'phone' => 'required',
                    'password' => 'required',
                ]);
                if (auth()->attempt(['phone' => $request->input('phone'), 'password' => $request->input('password')])) {
                    $user = auth()->user();

                }
                $IsEmail = false;

            }

//            if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
//                // Authentication passed...
//                $user = auth()->user();
//                if (!$user->hasRole('homeowner')) {
//                    return $this->sendError('User not homeowner', 401);
//                }
            if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
                $user->device_token = $request->header('devicetoken');
                $user->save();
                if($user->is_verified==0) {
                    $user->activation_code = rand(1000, 9999); // activation code

                    //Expiration code date
                    $now = time();
                    $time_plus_15_minutes = $now + (15 * 60);
                    $packageEndDate = date('Y-m-d H:i:s', strtotime('+15 minute'));


                    $user->activation_code_exp_date = $packageEndDate;

                    if ($IsEmail) {
                        require '../vendor/autoload.php'; // load Composer's autoloader

                        $mail = new PHPMailer(true); // Passing `true` enables exceptions

                        try {

                            // Mail server settings

                            //$mail->SMTPDebug = 4;
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'yamanworkshahbandar@gmail.com';
                            $mail->Password = "\$_POST!'Yamahn'!";
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            $mail->setFrom('yamanworkshahbandar@gmail.com', 'Smart Home Services');
                            $mail->addAddress($user->email);
                            $mail->isHTML(true);

                            $mail->Subject = 'SHS - Verification Code';
                            $mail->Body = 'Your verification code is: ' . $user->activation_code;

                            $mail->send();
                            return $this->sendResponse(['activation_code'=>$user->activation_code], 'user not verified');

                        } catch (Exception $e) {
                            return $this->sendError('error message ', 401);
                        }
                    }
                }
                $response=
                    ['id'=>$user->id,
                        'first_name'=>$user->name,
                        'last_name'=>$user->last_name,
                        'email'=>$user->email,
                        'avatar'=>asset('storage/Avatar').'/'.$user->avatar,
                        'lang'=>$user->language,
                        'device_token'=>$user->device_token,
                        'phone'=>$user->phone,
                        'city'=>$user->cities->city_name,
                        'country'=>(Country::find($user->cities->country_id))->country_name,

                        ];
                return $this->sendResponse($response, 'User retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('error', 401);
        }

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    function register(Request $request)
    {

        $IsEmail = false;
        try {
            if(empty($request->header('devicetoken'))){
                return $this->sendError('nothing to process', 401);

            }
            if($request->email) {

                $this->validate($request, [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                ]);
                $IsEmail = true;
                $user=User::where('email',$request->email)->first();
            }
            elseif($request->phone){
                $this->validate($request, [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone' => 'required',
                    'password' => 'required',
                ]);
                $IsEmail = false;
                $user=User::where('phone',$request->phone)->first();

            }
            if(!empty($user)){
                return $this->sendError('user is already exist!!', 401);

            }
            $user = new User;
            $user->name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email','');
           $user->city_id = $request->input('city_id');

            $user->language = $request->input('lang')==null ? 'en':$request->input('lang','');
            $user->phone = $request->input('phone')==null ? '':$request->input('phone','');


            $user->activation_code = rand(1000,9999); // activation code

            //Expiration code date
            $now = time();
            $time_plus_15_minutes = $now + (15 * 60);
            $packageEndDate = date('Y-m-d H:i:s', strtotime('+15 minute'));


            $user->activation_code_exp_date = $packageEndDate;

//            $user->avatar = $request->input('avatar');
            $user->device_token = $request->header('devicetoken');
            $user->password = Hash::make($request->input('password'));
           // $user->api_token = str_random(60);
            $user->save();

            $user->assignRole('homeowner');


          //  $IsEmail ? $user->notify(new \App\Notifications\VerifyEmail($user->activation_code)) : $unused = false ;


            if($IsEmail) {
                require '../vendor/autoload.php'; // load Composer's autoloader

                $mail = new PHPMailer(true); // Passing `true` enables exceptions

                try {

                    // Mail server settings

                    //$mail->SMTPDebug = 4;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'yamanworkshahbandar@gmail.com';
                    $mail->Password = "\$_POST!'Yamahn'!";
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('yamanworkshahbandar@gmail.com', 'Smart Home Services');
                    $mail->addAddress($user->email);
                    $mail->isHTML(true);

                    $mail->Subject = 'SHS - Verification Code';
                    $mail->Body    = 'Your verification code is: ' . $user->activation_code;

                    $mail->send();

                } catch (Exception $e) {
                    return $this->sendError('error message ', 401);
                }
            } else {
                $unused = false ;
            }

            $response=
                ['id'=>$user->id,
                    'first_name'=>$user->name,
                    'last_name'=>$user->last_name,
                    'email'=>$user->email,
                    'activation_cod'=>$user->activation_code,
                   // 'avatar'=>$user->avatar,
                    'lang'=>$user->language,
                    'device_token'=>$user->device_token,
                    'phone'=>$user->phone,
                    //'city'=>$user->cities->city_name,
                   // 'country'=>(Country::find($user->cities->country_id))->country_name,

                ];

            event(new UserRoleChangedEvent($user));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }


        return $this->sendResponse($response, 'User retrieved successfully');
    }
    public function verify(Request $request){
        if($request->header('devicetoken')) {
                   try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }


                $user->city_id = $request->input('city_id');
                //$user->language = $request->input('lang');
                $user->avatar = 'avatar.png';
                $user->is_verified = 1;
                $user->save();
                $response =
                    ['id' => $user->id,
                        'first_name' => $user->name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        //'activation_cod'=>$user->activation_code,
                        'avatar' => asset('storage/Avatar') . '/' . $user->avatar,
                        'lang' => $user->language,
                        'device_token' => $user->device_token,
                        'phone' => $user->phone,
                        'city' => $user->cities->city_name,
                        'country' => (Country::find($user->cities->country_id))->country_name,

                    ];
                event(new UserRoleChangedEvent($user));
            } catch (\Exception $e) {
                return $this->sendError('error save', 401);
            }


            return $this->sendResponse($response, 'User updated successfully');

        }
        else {
            return $this->sendError('error!', 401);
        }
    }
    function logout(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();
        if (!$user) {
            return $this->sendError('User not found', 401);
        }
        try {
            auth()->logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
        return $this->sendResponse($user['name'], 'User logout successfully');

    }

    function user(Request $request)
    {
        $hiddenColumns = ['custom_fields', 'has_media', 'media'];

        $user = $this->userRepository->where('api_token', $request->input('api_token'))->get(['id', 'name', 'avatar', 'description', 'last_name']);

        $user->makeHidden($hiddenColumns);

        if (!$user) {
            return $this->sendError('User not found', 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function settings(Request $request)
    {
        $settings = setting()->all();
        $settings = array_intersect_key($settings,
            [
                'default_tax' => '',
                'default_currency' => '',
                'default_currency_decimal_digits' => '',
                'app_name' => '',
                'currency_right' => '',
                'enable_paypal' => '',
                'enable_stripe' => '',
                'enable_razorpay' => '',
                'main_color' => '',
                'main_dark_color' => '',
                'second_color' => '',
                'second_dark_color' => '',
                'accent_color' => '',
                'accent_dark_color' => '',
                'scaffold_dark_color' => '',
                'scaffold_color' => '',
                'google_maps_key' => '',
                'mobile_language' => '',
                'app_version' => '',
                'enable_version' => '',
                'distance_unit' => '',
            ]
        );

        if (!$settings) {
            return $this->sendError('Settings not found', 401);
        }

        return $this->sendResponse($settings, 'Settings retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param Request $request
     *
     */
    public function update($id, Request $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }
        $input = $request->except(['password', 'api_token']);
        try {
            if ($request->has('device_token')) {
                $user = $this->userRepository->update($request->only('device_token'), $id);
            } else {
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
                $user = $this->userRepository->update($input, $id);

                foreach (getCustomFieldsValues($customFields, $request) as $value) {
                    $user->customFieldsValues()
                        ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
    }

    function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse(true, 'Reset link was sent successfully');
        } else {
            return $this->sendError([
                'error' => 'Reset link not sent',
                'code' => 401,
            ], 'Reset link not sent');
        }

    }

    public function delete(Request $request) {
        $id = $request->id;

        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }

        $this->userRepository->delete($id);

        return $this->sendResponse(true, 'User Deleted Successfully');
    }



    public function myReviews(Request $request) {
        //$id = $request->id; // logged in user ID
        if($request->device_token) {
            try{
            $user = User::where('device_token', $request->device_token)->first();
            if (empty($user)) {
                return $this->sendError('User not found', 401);

            }
                try{
            $userLatitude = $user->coordinates->latitude;
            $userLongitude = $user->coordinates->longitude;
                }
                catch (\Exception $e){
                    return $this->sendError('You have to turn on gps', 401);

                }
            $reviewsHiddenColumns = ['custom_fields', 'media', 'has_media'];
            $attrs = $user->vendorsAPI->makeHidden($reviewsHiddenColumns); // vendors I reviewd
            $respone = [];
            $i = 0;
            foreach ($attrs as $attr) {
                $respone[$i]['id'] = $attr->id;
                $respone[$i]['name'] = $attr->name;
                $respone[$i]['avatar'] =asset('storage/Avatar').'/'.$attr->avatar;
                $respone[$i]['last_name'] = $attr->last_name;
                $respone[$i]['description'] = $attr->pivot->description;
                $respone[$i]['rating'] = myReviewRating($attr);
                $respone[$i]['distance'] = $attr->coordinates ? distance(floatval($userLatitude), floatval($userLongitude), floatval($attr->coordinates->latitude), floatval($attr->coordinates->longitude)) : 'No coordinates provided for the current vendor';
                $i++;
            }

            return $this->sendResponse($respone, 'reviews retrieved successfully');
            }
            catch (\Exception $e){
                return $this->sendError('Error', 401);

            }
        }
        else
            return $this->sendError('You dont have permission', 401);

    }

    public function bookMark(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            $hiddenElems = ['custom_fields', 'has_media'];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
//
                try{
                    $userLatitude = $user->coordinates->latitude;
                    $userLongitude = $user->coordinates->longitude;
                }
                catch (\Exception $e){
                    return $this->sendError('You have to turn on gps', 401);

                }
                $HiddenColumns = ['custom_fields', 'media', 'has_media', 'pivot'];
                $attrs = $user->vendorFavoriteAPI->makeHidden($HiddenColumns);
                $respone = [];
                $i = 0;
                foreach ($attrs as $attr) {
                    $respone[$i]['id'] = $attr->id;
                    $respone[$i]['name'] = $attr->name;
                    $respone[$i]['avatar'] = asset('storage/Avatar').'/'.$attr->avatar;
                    $respone[$i]['last_name'] = $attr->last_name;
                    $respone[$i]['description'] = $attr->description;
                    $respone[$i]['rating'] = getRating($attr);
                    $respone[$i]['distance'] = $attr->coordinates ? distance(floatval($userLatitude), floatval($userLongitude), floatval($attr->coordinates->latitude), floatval($attr->coordinates->longitude)) : 'No coordinates provided for the current vendor';
                    $i++;
                }
                return $this->sendResponse($respone, 'favorites retrieved successfully');
            } catch (\Exception $e) {
                return $this->sendError($e->getMessage(), 401);
            }
        }
        else
        return $this->sendError('nothing to process', 401);

    }

    public function history(Request $request) {
        $id = $request->id; // logged in user ID
        $user = User::find($id);
        $userLatitude = $user->coordinates->latitude;
        $userLongitude = $user->coordinates->longitude;
        $HiddenColumns = ['custom_fields', 'media', 'has_media', 'pivot'];
        $attrs = $user->homeOwnerHistoryAPI->makeHidden($HiddenColumns);
        $respone = [];
        $i = 0;
        foreach($attrs as $attr) {
            $respone[$i]['id'] = $attr->id;
            $respone[$i]['name'] = $attr->name;
            $respone[$i]['avatar'] = $attr->getFirstMediaUrl('avatar', 'icon');
            $respone[$i]['last_name'] = $attr->last_name;
            $respone[$i]['description'] = $attr->description;
            $respone[$i]['rating'] = getRating($attr);
            $respone[$i]['distance'] = $attr->coordinates ? distance(floatval($userLatitude), floatval($userLongitude), floatval($attr->coordinates->latitude), floatval($attr->coordinates->longitude)) : 'No coordinates provided for the current vendor';
            $i++;
        }
        return $this->sendResponse($respone, 'history retrieved successfully');
    }

    public function leaveReview(Request $request) {
        $input = $request->all();

        $input['approved'] = 0;

        $rules = [
            'price_rating'      => 'required',
            'service_rating'    => 'required',
            'speed_rating'      => 'required',
            'trust_rating'      => 'required',
            'knowledge_rating'  => 'required',
            'vendor_id'         => 'required',
            'client_id'         => 'required',
            'description'       => 'required'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {

            $response = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());

            return $this->sendResponse($response, 'Error');

        } else {

            reviews::create($input);

            return $this->sendResponse($input, 'Review Added successfully');

        }
    }
}
