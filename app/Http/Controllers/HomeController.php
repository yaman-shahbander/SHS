<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use WebToPay;
use WebToPayException;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('test');

        return redirect('/');

    }


    public function mousa()
    {
        // $to_name = ‘RECEIVER_NAME’;
        // $to_email = ‘RECEIVER_EMAIL_ADDRESS’;
        // $data = array(‘name’=>”Ogbonna Vitalis(sender_name)”, “body” => “A test mail”);
        // Mail::send(‘emails.mail’, $data, function($message) use ($to_name, $to_email) {
        // $message->to($to_email, $to_name)
        // ->subject(Laravel Test Mail’);
        // $message->from(‘SENDER_EMAIL_ADDRESS’,’Test Mail’);
        // });

        //yamanshahbandar4@gmail.com


        // $user = (\App\Models\User::where('email', 'tred@hgvgh.com'))->first();

        // return ($user->notify(new \App\Notifications\VerifyEmail()));

        // // $user = (\App\Models\User::where('email', 'yamanshahbandar4@gmail.com'))->first();

        // // return ($user->notify(new \App\Notifications\VerifyEmail()));
        // return view('home');

        echo '<script type="text/javascript">alert("sss") </script>';


    }


    public function sendNotification(Request $request)
    {
        $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        $SERVER_API_KEY = 'AAAA4D63pNE:APA91bHZnOMtZp1E5zvs5hmd0mniLA2JRWQwECU5Rc-aI4cvHfENc4PuMTwNnHtFwFex11IFsdEns2ErZ05GXfn-sJVDMit8lfc5RSMTF9GHfHadBQ0OMfGA8MJ0H4DQ5t3LAl-Nx6y2';
//return dd($firebaseToken);
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        // dd($data);
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);


        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        // dd(curl_exec($ch));
        $response = curl_exec($ch);

      return  dd(curl_exec($ch));
    }
}
