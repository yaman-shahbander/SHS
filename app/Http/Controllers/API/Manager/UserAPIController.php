<?php
/**
 * File name: UserAPIController.php
 * Last modified: 2020.08.11 at 23:04:35
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API\Manager;

use App\Country;
use App\Criteria\Users\DriversOfRestaurantCriteria;
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
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\reviews;
use Validator;
use PHPMailer\PHPMailer\PHPMailer;

class UserAPIController extends Controller
{
    /**
     * @var UserRepository
     */
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
            if (!$user->hasRole('vendor')) {
                    return $this->sendError('User not Vendor', 401);
                }
//                $user->device_token = $request->header('devicetoken');
//                $user->save();
            $user->language = $request->input('lang')==null ? 'en':$request->input('lang','');

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
                            $user->save();

                            $response_cod=
                                ['id'=>$user->id,
                                    'first_name'=>$user->name,
                                    'last_name'=>$user->last_name,
                                    'email'=>$user->email,
                                    'avatar'=>asset('storage/Avatar').'/'.$user->avatar,
                                    'device_token'=>$user->device_token,
                                    'phone'=>$user->phone,
                                    'country'=>null,
                                    'activation_code'=>$user->activation_code

                                ];
                            return $this->sendResponse($response_cod, 'user not verified');

                        } catch (Exception $e) {
                            return $this->sendError('error message ', 401);
                        }
                    }
                }
                   $user->save();

            $response=
                    ['id'=>$user->id,
                        'first_name'=>$user->name,
                        'last_name'=>$user->last_name,
                        'email'=>$user->email,
                        'avatar'=>asset('storage/Avatar').'/'.$user->avatar,
                        'lang'=>$user->language,
                        'device_token'=>$user->device_token,
                        'phone'=>$user->phone,

                        'country'=>[
                            'id'=>(Country::find($user->cities->country_id))->id,
                            'country_name'=>(Country::find($user->cities->country_id))->country_name,
                            'city'=>[
                                'id'=>$user->cities->id,
                                'name'=>$user->cities->city_name,
                                     ]
                            ]

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

            if($request->email) {

                $this->validate($request, [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|unique:users|email',
                    'password' => 'required',
                ]);
                $IsEmail = true;
            }
            elseif($request->phone){
                $this->validate($request, [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone' => 'required|unique:users',
                    'password' => 'required',
                ]);
                $IsEmail = false;
            }
            $user = new User;
            $user->name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email','');
//            $user->city_id = $request->input('city_id');

            $user->language = $request->input('lang')==null ? '':$request->input('lang','');
            $user->phone = $request->input('phone')==null ? '':$request->input('phone','');


//            $user->language = $request->input('lang');
            $user->activation_code = rand(1000,9999); // activation code

            //Expiration code date
            $now = time();
            $time_plus_15_minutes = $now + (15 * 60);

            $packageEndDate = date('Y-m-d H:i:s', strtotime('+15 minute'));

            $user->activation_code_exp_date = $packageEndDate;

//            $user->avatar = $request->input('avatar');
             //$user->device_token = $request->header('devicetoken');

             //Generate a random string.
            $token = openssl_random_pseudo_bytes(16);

            $user->save();

            //Convert the binary data into hexadecimal representation.
            $token = bin2hex($user->id . $token);

            $user->device_token = $token;

            $user->password = Hash::make($request->input('password'));



//            $user->device_code = str_random(60) . time();
           // $user->api_token = str_random(60);
            $user->save();

            $user->assignRole('vendor');

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
                     return back()->with('error','Message could not be sent.');
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

    //$request->header('devicetoken')



    public function completeRegistration(Request $request) {
        if ($request->header('devicetoken')) {
            $vendor = User::where('device_token', $request->header('devicetoken'))->first();
            if (!empty($vendor)) {
                // $days = [
                //    [ 'day_id'=>5,
                //     'start'  => '12:00:00',
                //     'end'    => '04:00:00'
                //    ],
                //    [
                //     'day_id'=>7,
                //     'start'  => '12:00:00',
                //     'end'    => '05:00:00'
                //     ],
                //     [ 'day_id'=>6,
                //     'start'  => '12:00:00',
                //     'end'    => '05:00:00'
                // ]];
                $vendor->subcategories()->sync($request->subcategories);
                return  $vendor->daysApi()->sync($request->days);
                return $this->sendResponse([], 'Data saved successfully');
            } else {
                return $this->sendResponse([], 'User not found');
            }
        } else {
            return $this->sendResponse([], 'Error');
        }
    }
//    function register(Request $request)
//    {
//        try {
//            if($request->email) {
//
//                $this->validate($request, [
//                    'first_name' => 'required',
//                    'email' => 'required|unique:users|email',
//                    'password' => 'required',
//                ]);
//            }
//            elseif($request->phone){
//                $this->validate($request, [
//                    'first_name' => 'required',
//                    'phone' => 'required|unique:users',
//                    'password' => 'required',
//                ]);
//            }
//            $user = new User;
//            $user->name = $request->input('first_name');
//            $user->last_name = $request->input('last_name');
//            $user->email = $request->input('email');
//            $user->city_id = $request->input('city_id');
//            $user->language = $request->input('lang');
//            $user->phone = $request->input('phone');
//            $user->activation_code = "123456";
//            $user->avatar = $request->input('avatar');
//            $user->device_token = $request->input('device_token', '');
//            $user->password = Hash::make($request->input('password'));
//            $user->api_token = str_random(60);
//            $user->save();
//
//
//            $user->assignRole('manager');
//            $response=
//                ['id'=>$user->id,
//                    'first_name'=>$user->name,
//                    'last_name'=>$user->last_name,
//                    'email'=>$user->email,
//                    'activation_cod'=>$user->activation_code,
//                    'avatar'=>$user->avatar,
//                    'lang'=>$user->language,
//                    'device_token'=>$user->device_token,
//                    'phone'=>$user->phone,
//                    'city'=>$user->cities->city_name,
//                    'country'=>(Country::find($user->cities->country_id))->country_name,
//
//                ];
//
//            event(new UserRoleChangedEvent($user));
//        } catch (\Exception $e) {
//            return $this->sendError($e->getMessage(), 401);
//        }
//
//
//        return $this->sendResponse($response, 'User retrieved successfully');
//    }

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

    /**
     * Display a listing of the Drivers.
     * GET|HEAD /restaurants
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function driversOfRestaurant($id, Request $request)
    {
        try{
            $this->userRepository->pushCriteria(new RequestCriteria($request));
            $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->userRepository->pushCriteria(new DriversOfRestaurantCriteria($id));
            $users = $this->userRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($users->toArray(), 'Drivers retrieved successfully');
    }

    public function myReviews(Request $request) {
        //$id = $request->id; // logged in user ID
        if($request->header('devicetoken')) {
            try{
            $user = User::where('device_token', $request->header('devicetoken'))->first();
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
        if($request->header('devicetoken')) {
            $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
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
    }

    public function leaveReview(Request $request) {
        if($request->header('devicetoken')) {

            $user = User::where('device_token', $request->header('devicetoken'))->first();
            if (empty($user)) {
                return $this->sendError('User not found', 401);
            }

            $input = $request->all();

            $input['approved'] = 0;

            $rules = [
                'price_rating'      => 'required',
                'service_rating'    => 'required',
                'speed_rating'      => 'required',
                'trust_rating'      => 'required',
                'knowledge_rating'  => 'required',
                'vendor_id'         => 'required',
                'description'       => 'required'
            ];

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {

                $response = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());

                return $this->sendResponse($response, 'Error');

            } else {
                $input['client_id']  = $user->id;
                

                reviews::create($input);

                return $this->sendResponse($input, 'Review Added successfully');

            }
        }
    }

    public function backgroundPic(Request $request) {

        if(empty($request->header('devicetoken'))){
            return $this->sendError('device token not found', 401);
        }

        $vendor = User::where('device_token', $request->header('devicetoken'))->first();

        if (empty($vendor)) {
            return $this->sendError('User not found', 401);
        }

            if (!empty ($request->file('background_profile'))) {

                $imageName = uniqid() . $request->file('background_profile')->getClientOriginalName();

                $request->file('background_profile')->move(public_path('storage/vendors_background'), $imageName);

                $vendor->background_profile = $imageName;

                $vendor->save();

                //

//                if ($request->file('avatar')) {
//                    $input['avatar'] = $request->file('avatar');
//                    return dd($mediaItem);
//
//      $cacheUpload = $this->uploadRepository;
//                    $cacheUpload->avatar=$request->file('avatar');
//    return dd($cacheUpload);
//                    $mediaItem = $cacheUpload->getMedia('avatar')->first();
//
//                    $mediaItem->copy($vendor, 'avatar');
//
//                }

                return $this->sendResponse([], 'photo Saved successfully');
            }


            else
                return $this->sendResponse([], 'Error! background image is empty');
        }

        // 'reviews'         => ($vendor->clientsAPI)->transform(function($q){
        //     return $q->select(['name', 'description'])->get();
        // })

        public function vendorprofile(Request $request) { //for vendor screens

            if(empty($request->header('devicetoken'))){
                return $this->sendError('device token not found', 401);
            }
            $vendor = User::where('device_token', $request->header('devicetoken'))->first();
            if (empty($vendor)) {
                return $this->sendError('User not found', 401);
            }
            $response = [];
            $response = [
                'name'            => $vendor->name,
                'rating'          => round((getRating($vendor)/20)*2)/2,
                'count_reviews'   => count($vendor->clients),
                'count_contected' => count($vendor->messages->unique('from_id')),
                'reviews'         => ($vendor->clientsAPI)->transform(function($q){
                                    return $q=[
                                        'name' => $q->name,
                                        'description'=>$q->pivot->description,
                                        'image'=> asset('storage/Avatar') . '/' . $q->avatar,
                                    ];
                                }),
                'offers'          => $vendor->specialOffers->makeHidden(['user_id', 'created_at', 'updated_at'])
            ];
            return $this->sendResponse($response, 'User retrieved successfully');

        }

}


