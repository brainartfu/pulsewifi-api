<?php

namespace App\Http\Controllers;

use App\Models\Wi_fi_users;
use App\Models\PDOA;
use App\Models\WiFiDevice;
use App\Models\WiFiUserVerify;
use App\Models\Payments;
use App\Models\Mail_server;
use App\Models\WifiUserAccount;
use App\Models\Email_logs;
use App\Models\Sms_logs;
use Illuminate\Http\Request;
//WiFiUserVerifyController;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use DB;
use App\Mail\SendEmail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;


use App\Models\Sms_template;
use App\Models\Sms_gateway;
use App\Models\Network_setting;
use App\Models\Internet_plans;
use Auth;

class WiFiUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:wifiapi', ['except' => ['login', 'register', 'direct_login', 'register_user', 'verify_otp', 'verify_email','register_user_pmwani','get_login_url']]);
    }
    public function user_active_session(Request $request, $pdoa_id){
        $user = auth()->user();
        if($user){
            $phone = $user->phone;
            $radWiFiUser = DB::connection('mysql2')->table('radcheck')->where('username', $phone)->where('pdoa', $pdoa_id)->first();
            $now = time();// date("Y-m-d H:i:s");
            if(!$radWiFiUser){
                return response()->json([
		            'status' => 'false',
		            'message' => 'Paid Access not available',
		        ], 201);
            }
            if ($radWiFiUser->logout_time > $now) {
                $data_available = $radWiFiUser->download_limit;

		        $start_date = $radWiFiUser->plan_start_date;
                $session_info = DB::connection('mysql2')->table('radacct')
                    ->select(DB::raw('IFNULL(ceil(sum(acctinputoctets/(1024*1024))),0) as downloads, IFNULL(ceil(sum(acctoutputoctets/(1024*1024))),0) as uploads'))
                    ->where('username', $phone)
                    ->where('pdoa_id', $pdoa_id)
                    ->where('acctstarttime', '>', $start_date)
                    ->get();
                $total_download = $session_info[0]->downloads + $session_info[0]->uploads;
                      
                if($total_download >=$data_available ){
		            return response()->json([
		                'status' => 'false',
		                'message' => 'Paid Access not available',
		            ], 201);
                }else{
                    $data = array();
                    $data['logout_time'] = date('d M Y H:i:s', $radWiFiUser->logout_time);
                    $data['data_available'] = $data_available - $total_download;
                    return response()->json([
		                'status' => 'true',
		                'message' => 'Paid Access is available',
                        'data' => $data
		            ], 201);
                }    
            }else{
            	return response()->json([
		            'status' => 'false',
		            'message' => 'Paid Access not available',
		        ], 201);
            }    
        }
    }
    public function profile()
    {
        $wifi_user = auth()->user();
        return response()->json([
            'success' => true,
            'message' => "Getting WiFi user Success!",
            'data' => $wifi_user
            ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function direct_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usermac' => 'required|string|exists:wi_fi_devices,usermac',
            'challenge' => 'required|string|min:20',
            'pdoa' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
                ], 200);
            // return response()->json($validator->errors()->toJson(), 400);
        }
        //$request->pdoa = 'aaa';
        $wifidevice = WiFiDevice::where('usermac',$request->usermac)->where('pdoa',$request->pdoa)->where('status','Verified')->first();
        $now = time();

        if($wifidevice){
            $phone = $wifidevice->phone;
            $radius_wifiuser = DB::connection('mysql2')->table('radcheck')
            ->where('username',$phone)
            ->where('pdoa',$request->pdoa)
            ->where('logout_time','>',$now)
            ->first();

            $username = $password = $request->usermac;

            if($radius_wifiuser){
                $username = $password = $phone;
            }else{

                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->where('username', $request->usermac)->where('pdoa', $request->pdoa)->first();

                if(! $radius_wifiuser){

                    $network_settings = Network_setting::where('pdoa_id', $request->pdoa)->first();
                    $logout_time = time()+$network_settings->freeDailySession*60;
                    $bandwidth = $network_settings->freeBandwidth;
                    $dw_limit = $network_settings->freeDataLimit;
                    $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->insert([
                        'username' => $request->usermac,
                        'value' => $request->usermac,
                        'pdoa' => $request->pdoa, 
                        'attribute' => 'Cleartext-Password',
                        'op' => ':=',
                        'bandwidth' => $bandwidth,
                        'logout_time' => $logout_time,
                        'download_limit' => $dw_limit
                    ]);
                    
                }else{
                    $network_settings = Network_setting::where('pdoa_id', $request->pdoa)->first();
                    $logout_time = time()+$network_settings->freeDailySession*60;
                    $bandwidth = $network_settings->freeBandwidth;
                    $dw_limit = $network_settings->freeDataLimit;
                    $radius_wifiuser = DB::connection('mysql2')->table('radcheck')
                    ->where('username', $request->usermac)
                    ->where('pdoa', $request->pdoa)
                    ->update([
                        'attribute' => 'Cleartext-Password',
                        'op' => ':=',
                        'bandwidth' => $bandwidth,
                        'logout_time' => $logout_time,
                        'download_limit' => $dw_limit
                    ]);
                }
            }


            $challenge = $request->challenge;
            $uamsecret = '';

            $hexchal = pack ("H32", $challenge);
            $newchal = $uamsecret ? pack("H*", md5($hexchal . $uamsecret)) : $hexchal;
            $response = md5("\0" .$password . $newchal);
            $newpwd = pack("a32", $password);
            $pappassword = implode ('', unpack("H32", ($newpwd ^ $newchal)));

            $data = array();
            
            $data['login_redirect_url'] = 'http://172.22.100.1:3990/logon?username='.$username.'&response='.$response.'&userurl=';
            $data['response'] = $response;
            // $data['username'] = $username;

            $user = WifiUserAccount::find($wifidevice['wifi_user_account_id']);
            $credentials = [
                'email' => $user->email,
                'password' => $request->password
            ];
            $token = auth()->guard('wifiapi')->attempt($credentials);
                            
            return response()->json([
            'success' => true,
            'message' => "WiFi Login Successful",
            "data" => $data,
            'token' => $this->respondWithToken($token)->original,
            "request" => $request->all()
            ], 200);

        }else{
            return response()->json([
                'success' => false,
                'message' => "WiFi Login Failed1",
                "request" => $request->all()
            ], 200);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors()
            ]);
        }
        $user = WifiUserAccount::where('email', '=', $request->input('username'))->orWhere('phone', '=', $request->input('username'))->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'This account does not exist, please login with a different account or register below',
            ]);
        }
        $wifi_device = WiFiDevice::where('wifi_user_account_id', '=', $user['id'])->get()->first();
        if ($user = WifiUserAccount::where('email', '=', $request->input('username'))->get()->first()) {
            $credentials = [
                'email' => $user->email,
                'password' => $request->password
            ];
        } else {
            $user = WifiUserAccount::where('phone', '=', $request->input('username'))->get()->first();
            $credentials = [
                'phone' => $user->phone,
                'password' => $request->password
            ];
        }
        $token = auth()->guard('wifiapi')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'AuthenticateError',
                'data' => 'This user is unauthorized.'
            ]);
        }
        /*
        if (!$user->email_verified) {
            return response()->json([
                'success' => false,
                'message' => 'EmailVerifyError.',
                'data' => 'This user email is not verified. Please verify your email.',
            ]);
        }
        if (!$user->account_verified) {
            return response()->json([
                'success' => false,
                'message' => 'AccountVerifyError',
                'data' => [
                    'msg' => 'This user account is not verified. Please verify your account.',
                    'device' => $wifi_device
                    ],
            ]);
        }
        */
        $data =  $this->respondWithToken($token)->original;
        $data['user_id'] = $user->id;
        $data['phone'] = $user->phone;
        return response()->json([
            'success' => true,
            'message' => "WiFi User Login Successful",
            "data" => $data
            ]);
    }

    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string|exists:wi_fi_devices,usermac',
    //         'challenge' => 'required|string|min:20',
    //         'pdoa' => 'required|string'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json([
    //             'success' => false,
    //             'message' => $validator->errors()->first(),
    //             'data' => null
    //             ], 400);
    //         // return response()->json($validator->errors()->toJson(), 400);
    //     }

    //     //$network_group = network_groups::findOrFail($request->network_group);
    //     //$network_group_name = $network_group['name'];

    //     // $global_values = global_values::findOrFail('1');
    //     $uamsecret = '';//$global_values->uam_secret;
    //     $wifidevice = WiFiDevice::where('usermac',$request->username)->where('pdoa',$request->pdoa)->where('status','Verified')->first();

    //     if( !$wifidevice){
    //         return response()->json([
    //             'success' => false,
    //             'message' => "WiFi Login Failed",
    //             "request" => $request
    //             ], 200);
    //     }else{

    //         $username = $request->username;
    //         $password = $request->username;
    //         $challenge = $request->challenge;

    //         $hexchal = pack ("H32", $challenge);
    //         $newchal = $uamsecret ? pack("H*", md5($hexchal . $uamsecret)) : $hexchal;
    //         $response = md5("\0" .$password . $newchal);
    //         $newpwd = pack("a32", $password);
    //         $pappassword = implode ('', unpack("H32", ($newpwd ^ $newchal)));

    //         $data = array();
            
    //         $data['login_redirect_url'] = 'http://172.22.100.1:3990/logon?username='.$username.'&response='.$response.'&userurl=';
    //         $data['response'] = $response;
    //         // $data['username'] = $username;

    //         return response()->json([
    //         'success' => true,
    //         'message' => "WiFi Login Successful",
    //         "data" => $data,
    //         "request" => $request
    //         ], 200);

    //     }
    // }

    // public function register(Request $request){

    //     $validator = Validator::make($request->all(), [
    //         'email' => 'email|nullable',
    //         'phone' => 'required|string|min:10',
    //         'name' => 'string|min:3|nullable',
    //         'pdoa' =>  'required|string',
    //         'usermac' => 'required|string|min:17',
    //         'challenge' => 'required|string',
    //         'os' => 'required|string',
    //         'brand' => 'required|string',
    //         'location_id' => 'required|integer',
    //     ]);

    //     $inputs = $request->all();

    //     if($validator->fails()){
    //         return response()->json([
    //             'success' => false,
    //             'message' => $validator->errors()->first(),
    //             'data' => null
    //         ], 400);
    //     }

    //     //$wifiUserVerify = new WiFiUserVerifyController;
    //     $validated = $validator->validated();
    //     $otp = Self::generate_code('integer',4);
    //     $wifiuser = Wi_fi_users::where('phone',$request->phone)->where('pdoa',$request->pdoa)->first();
    //     if(! $wifiuser){
    //         $validated['phone'] = $validated['phone'];
    //         $password = Str::random(20);
    //         $wifiuser = Wi_fi_users::create(array_merge(
    //             $validated,
    //             ['password' => bcrypt($password),'password_hash' => $password]
    //         ));
    //     }
    //     $wifidevice = WiFiDevice::where('phone',$request->phone)->where('usermac',$request->usermac)->where('pdoa',$request->pdoa)->first();
    //     $url_code = Self::generate_code('string',6);
    //     if($wifidevice){
    //         $wifidevice->otp = $otp;
    //         $wifidevice->url_code = $url_code;
    //         $wifidevice->otp_generate_time = Carbon::now();
    //         $wifidevice->status = 'Not Verified'; 
    //         $wifidevice->save();
    //         // $verification_status = false;         
    //     }else{
    //         $wifi_device = array();
    //         $wifi_device['otp'] = $otp;
    //         $wifi_device['usermac'] = $validated['usermac'];
    //         $wifi_device['phone'] = $validated['phone'];
    //         $wifi_device['challenge'] = $validated['challenge'];
    //         $wifi_device['os'] = $validated['os'];
    //         $wifi_device['brand'] = $validated['brand'];
    //         $wifi_device['location_id'] = $validated['location_id'];
    //         $wifi_device['pdoa'] = $validated['pdoa'];
    //         $wifi_device['otp_generate_time'] = Carbon::now();
    //         $wifi_device['url_code'] = $url_code;
    //         $wifi_device['status'] = 'Not Verified'; 

    //         // $wifi_device[''] = $validated['usermac'];
    //         // $wifi_device[''] = $validated['usermac'];

    //         $wifidevice = WiFiDevice::create($wifi_device);
    //     }
        
    //     $otp_msg = 'Your OTP for Pulse WiFi login is '.$otp.'.';

    //     $sms_send_resonse = Self::send_sms($request->pdoa, $otp, $url_code, $validated['phone'],$validated['os']);
    //     $sms_template = Sms_template::where('pdoa_id',$request->pdoa)->first();

    //     return response()->json([
    //         'success' => true,
    //         'message' => "User Registered",
    //         'data' => $wifiuser,
    //         'wifidevice' => $wifidevice,
    //         "otp_msg_status" => $sms_send_resonse,
    //         'sms_template' => $sms_template
    //     ], 200);	
    // }

    public function verify_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'pdoa' => 'required|string',// |exists:network_groups,id',
            'id' => 'required|integer',
            'otp' => 'required',
            'password' => 'required'
        ]);


        $inputs = $request->all();

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 400);
        }

        $wifidevice = WiFiDevice::where('id',$request->id)->where('pdoa',$request->pdoa)->where('otp',$request->otp)->first();
        if($wifidevice){
            $wifidevice->status = 'Verified';
            $wifidevice->save();
            // Check radius and insert/update
            $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->where('username',$wifidevice->usermac)->where('pdoa',$wifidevice->pdoa)->first();

            if(! $radius_wifiuser){

                $network_settings = Network_setting::where('pdoa_id',$request->pdoa)->first();
                $logout_time = time()+$network_settings->freeDailySession*60;
                $bandwidth = $network_settings->freeBandwidth;
                $dw_limit = $network_settings->freeDataLimit;
                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->insert([
                    'username' => $wifidevice->usermac,
                    'value' => $wifidevice->usermac,
                    'pdoa' => $wifidevice->pdoa, 
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'bandwidth' => $bandwidth,
                    'logout_time' => $logout_time,
                    'download_limit' => $dw_limit
                ]);
            }else{
                $network_settings = Network_setting::where('pdoa_id',$request->pdoa)->first();
                $logout_time = time()+$network_settings->freeDailySession*60;
                $bandwidth = $network_settings->freeBandwidth;
                $dw_limit = $network_settings->freeDataLimit;
                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')
                ->where('username', $wifidevice->usermac)
                ->where('pdoa', $wifidevice->pdoa)
                ->update([
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'bandwidth' => $bandwidth,
                    'logout_time' => $logout_time,
                    'download_limit' => $dw_limit
                ]);
            }
            $user = WifiUserAccount::find($wifidevice['wifi_user_account_id']);
            if($user) $user->update(['account_verified' => 1]);
            $credentials = [
                'email' => $user->email,
                'password' => $request->password
            ];
            $token = auth()->guard('wifiapi')->attempt($credentials);
            return response()->json([
                'success' => true,
                'message' => "User Login OTP Verified",
                "data" => $wifidevice,
                'token' => $this->respondWithToken($token)->original,
                'radius_wifiuser' => $radius_wifiuser
            ], 200);	
        } else {
            return response()->json([
                'success' => false,
                'message' => "User Login OTP Verification failed",
                "data" => null,
            ], 200);
        }
    }

    public function get_session_log($wifi_user_id){
        $user = WifiUserAccount::select('id', 'phone', 'first_name', 'last_name')->where('id', '=', $wifi_user_id)->get()->first();
        $device = WiFiDevice::where('wifi_user_account_id', '=', $wifi_user_id)->get()->first();
        $sessions = [];
        if($device) {
            $user['pdoa_id'] = $device['pdoa'];
            $usermac = $device['usermac'];
            $sessions = DB::connection('mysql2')->table('radacct')                        
                        ->where('username', $usermac)
                        ->get();
        }        
        return response()->json([
                    'success' => true,
                    'message' => "Getting WiFi user Session Log success",
                    'data' => $sessions,
                    'user' => $user,
                ], 200);	
    }

    public function get_payment_log($wifi_user_id){
        $user = WifiUserAccount::select('id', 'phone', 'first_name', 'last_name')->where('id', '=', $wifi_user_id)->get()->first();
        
        $payments = Payments::leftJoin('location', 'payments.location_id', '=', 'location.id')
                ->leftJoin('wi_fi_orders', 'payments.order_id', '=', 'wi_fi_orders.id')
                ->leftJoin('internet_plans', 'wi_fi_orders.internet_plan_id', '=', 'internet_plans.id')
                ->select('payments.*', 'internet_plans.name as plan_name', 'location.name as location_name')
                ->where('payments.wifi_user_id', '=', $wifi_user_id)
                ->get();
        return response()->json([
                    'success' => true,
                    'message' => "Getting WiFi user Payment Log success",
                    'data' => $payments,
                    'user' => $user,
                ], 200);	
    }
    
    public function register_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:wifi_user_accounts',
            'phone' => 'required|string|min:10|unique:wifi_user_accounts',
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'password' =>  'required|confirmed',             
            'usermac' => 'required|string|min:17',
            'challenge' => 'required|string',
            'os' => 'required|string',
            'brand' => 'required|string',
            'location_id' => 'required|integer',
        ]);

        $inputs = $request->all();

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors()
            ]);
        }

        $url_code = Self::generate_code('string',6);
        $validated = $validator->validated();
        $wifi_user = WifiUserAccount::where('phone', '=', $validated['phone'])
                                    ->where('pdoa_id', '=', $validated['pdoa_id'])
                                    ->get()->first();
        $wifi_user_ = WifiUserAccount::where('phone', '=', $validated['phone'])
                                    ->where('pdoa', '=', $validated['pdoa_id'])
                                    ->where('usermac', '=', $validated['usermac'])
                                    ->get()->first();

        if($wifi_user && $wifi_user_) {
            $wifi_user->update(['password' => Hash::make($request->input('password'))]);
            if (!$user['email_verified']) {
                return response()->json([
                    'success' => false,
                    'message' => 'EmailVerifyError.',
                    'data' => 'This user email is not verified. Please verify your email.',
                ]);
            }
            if (!$user['account_verified']) {
                return response()->json([
                    'success' => false,
                    'message' => 'AccountVerifyError',
                    'data' => [
                        'msg' => 'This user account is not verified. Please verify your account.',
                        'device' => $wifi_device
                        ],
                ]);
            }

            if(!$token = auth()->guard('wifiapi')->attempt(['phone'=>$input['phone'], 'password'=>$input('password')])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Getting Token Error!',
                ]);
            }
            $data = $this->respondWithToken($token)->original;
            $data['user_id'] = $wifi_user->id;
            $data['phone'] = $wifi_user->phone;
            return response()->json([
                'success' => true,
                'message' => 'Wifi User login success!',
                'data' => $data
            ]);

        } else {
            $otp = Self::generate_code('integer', 4);
            $email_verify_code = Self::generate_code('integer', 4);
            $validated['otp_code'] = $otp;
            $validated['email_verify_code'] = $email_verify_code;
            $validated['pdoa_id'] = $inputs['pdoa'];
            $validated['password'] = Hash::make($request->input('password'));
            $wifi_user = WifiUserAccount::create($validated);
            $validated['pdoa'] = $validated['pdoa_id'];
            $wifiuser = Wi_fi_users::create($validated);
            $wifi_device = array();
            $wifi_device['otp'] = $otp;
            $wifi_device['usermac'] = $validated['usermac'];
            $wifi_device['phone'] = $validated['phone'];
            $wifi_device['challenge'] = $validated['challenge'];
            $wifi_device['os'] = $validated['os'];
            $wifi_device['brand'] = $validated['brand'];
            $wifi_device['location_id'] = $validated['location_id'];
            $wifi_device['pdoa'] = $validated['pdoa'];
            $wifi_device['otp_generate_time'] = Carbon::now();
            $wifi_device['url_code'] = $url_code;
            $wifi_device['status'] = 'Not Verified'; 
            $wifi_device['wifi_user_account_id'] = $wifi_user->id; 
            $wifidevice = WiFiDevice::create($wifi_device);
        
            $otp_msg = 'Your OTP for Pulse WiFi login is '.$otp.'.';

            $sms_send_resonse = Self::send_sms($request->pdoa, $otp, $url_code, $validated['phone'],$validated['os']);
            $sms_template = Sms_template::where('pdoa_id',$request->pdoa)->first();
            $req_array['email_verification_code'] = $email_verify_code;

            $server = PDOA::where(["id" => $request->pdoa])->get()->first();
            $mail_server = Mail_server::where(["pdoa_id" => $request->pdoa])->get()->first();
            $logo = "https://api.pulsewifi.net/default_logo.png";
            if ($server["brand_logo"] != null && $server["brand_logo"]) {
                $server["brand_logo"] = str_replace("public", "storage", $server["brand_logo"]);
                $logo = "https://api.pulsewifi.net/" . $server["brand_logo"];
            }

            $content = '<!doctype html>
                <html lang="en-US">

                <head>
                <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
                <title>Reset Password Email Template</title>
                <meta name="description" content="Reset Password Email Template.">
                <style type="text/css">
                    a:hover {
                    text-decoration: underline !important;
                    }

                </style>
                </head>

                <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
                <!--100% body table-->
                <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
                    style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: Open Sans, sans-serif;">
                    <tr>
                    <td>
                        <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                        align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="height:80px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                            <a href="https://'.$server["domain_name"].'" title="logo" target="_blank">
                                <img src="'.$logo.'" title="logo" alt="logo" width="auto" height="60px" />
                            </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                <td style="height:40px;">&nbsp;</td>
                                </tr>
                                <tr>
                                <td style="padding:0 35px;">
                                    <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family: Rubik,sans-serif;">
                                    Hi, ' . $wifi_user["first_name"] . '! Please verify your Email</h1>
                                    <span
                                    style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                    <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
                                    You registered as new WiFi User of '.$server["brand_name"].'.<br>
                                    Please click below button to verify your email.
                                    </p>
                                    <a href="https://'.$server["domain_name"].'/login-verify-email?email='.$wifi_user["email"].'&code='.$wifi_user['email_verify_code'].'" target="_blank"
                                    style="background:#20e277;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">Verify Email</a>
                                </td>
                                </tr>
                                <tr>
                                <td style="height:40px;">&nbsp;</td>
                                </tr>
                            </table>
                            </td>
                        <tr>
                            <td style="height:20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                            <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">
                                &copy; <strong>'.$server["brand_name"].'</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:80px;">&nbsp;</td>
                        </tr>
                        </table>
                    </td>
                    </tr>
                </table>
                </body>

                </html>';

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($mail_server["sender_email"], $mail_server["sender_name"]);
            $email->setSubject("Verify Email request from " . $server["brand_name"]);
            $email->addTo($wifi_user["email"], $wifi_user["first_name"] . " " . $wifi_user["last_name"]);
            $email->addContent(
                "text/html", $content
            );
            $sendgrid = new \SendGrid($mail_server["api_key"]);
            try {
                $response = $sendgrid->send($email);
                Email_logs::create([
                    'receiver_email' => $wifi_user["email"],
                    'subject' => "Verify Email request from " . $server["brand_name"],
                    'pdoa_id' => $request->pdoa,
                ]);
            } catch (Exception $e) {
                echo 'Caught exception: ' . $e->getMessage() . "\n";
            }
            return response()->json([
                'success' => true,
                'message' => "Wifi User is Registered successfully",
                'data' => $wifi_user,
                'wifidevice' => $wifidevice,
                "otp_msg_status" => $sms_send_resonse,
            ], 200);
        }
        
    }

    public function register_user_pmwani(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10',
            'password' =>  'required|string',             
            'domain' => 'required|string'
        ]);

        $inputs = $request->all();

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors()
            ]);
        }
        $pdoa = PDOA::where('domain_name', $inputs['domain'])->first();
        /*
        return response()->json([
            'success' => true,
            'message' => 'Wifi User login success!',
            'request' => $request->all(),
            'pdoa' => $pdoa
        ]);
        */
        if($pdoa){
            $pdoa_id = $pdoa->id;
        
            $validated = $validator->validated();
            $wifi_user = WifiUserAccount::where('phone', '=', $validated['phone'])
                ->where('pdoa_id', '=', $pdoa_id)
                ->get()->first();

            if($wifi_user){
                $wifi_user->update(['password' => Hash::make($request->input('password'))]);
            }else{
                $otp = Self::generate_code('integer', 4);
                $email_verify_code = Self::generate_code('integer', 4);
                $validated['otp_code'] = $otp;
                $validated['email_verify_code'] = $email_verify_code;
                $validated['pdoa_id'] = $pdoa_id;
                $validated['password'] = Hash::make($request->input('password'));
                $wifi_user = WifiUserAccount::create($validated);
                // $validated['pdoa'] = $validated['pdoa_id'];
                // $wifiuser = Wi_fi_users::create($validated);
            }

            $token = auth()->guard('wifiapi')->attempt(['phone'=>$inputs['phone'], 'password'=>$inputs['password'] ]);

            if(! $token ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Getting Token Error!',
                ]);
            }
            $data =  $this->respondWithToken($token)->original;
            $data['user_id'] = $wifi_user->id;
            $data['phone'] = $wifi_user->phone;
            return response()->json([
                'success' => true,
                'message' => 'Wifi User login success!',
                'token' => $token
            ]);
        }else{

        }

    }
    
public function get_login_url(Request $request){
    $validator = Validator::make($request->all(), [
        'phone' => 'required',
        'challenge' => 'required',
        'type' => 'required',
        'pdoa' => 'required'
    ]);
    

    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => 'ValidationError',
            'data' => $validator->errors()
        ]);
    }
    $username = $request->phone;
    if($request->type == 'Free'){
        $username = $request->phone.'-Free';
    }

    
    $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->where('username', $username)->where('pdoa', $request->pdoa)->first();

if($request->type == 'Free'){
    if(! $radius_wifiuser){

        $network_settings = Network_setting::where('pdoa_id', $request->pdoa)->first();
        $logout_time = time()+$network_settings->freeDailySession*60;
        $password = 'abcd1234';
        $bandwidth = $network_settings->freeBandwidth;
        $dw_limit = $network_settings->freeDataLimit;
        $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->insert([
            'username' => $username,
            'value' => $password,
            'pdoa' => $request->pdoa, 
            'attribute' => 'Cleartext-Password',
            'op' => ':=',
            'bandwidth' => $bandwidth,
            'logout_time' => $logout_time,
            'download_limit' => $dw_limit
        ]);
                
    }else{
        $network_settings = Network_setting::where('pdoa_id', $request->pdoa)->first();
        $logout_time = time()+$network_settings->freeDailySession*60;
        $bandwidth = $network_settings->freeBandwidth;
        $dw_limit = $network_settings->freeDataLimit;
        $password = 'abcd1234';
        $radius_wifiuser = DB::connection('mysql2')->table('radcheck')
            ->where('username', $username)
            ->where('pdoa', $request->pdoa)
            ->update([
                'value' => $password,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'bandwidth' => $bandwidth,
                'logout_time' => $logout_time,
                'download_limit' => $dw_limit
            ]);
    }
 }
   $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->where('username', $username)->where('pdoa', $request->pdoa)->first();

    $challenge = $request->challenge;
    $uamsecret = '';
    $password = $radius_wifiuser->value;

    $hexchal = pack ("H32", $challenge);
    $newchal = $uamsecret ? pack("H*", md5($hexchal . $uamsecret)) : $hexchal;
    $response = md5("\0" .$password . $newchal);
    $newpwd = pack("a32", $password);
    $pappassword = implode ('', unpack("H32", ($newpwd ^ $newchal)));

    $data = array();
        
    $data['login_redirect_url'] = 'http://172.22.100.1:3990/logon?username='.$username.'&response='.$response.'&userurl=';
    $data['response'] = $response;
    
    return response()->json([
        'success' => true,
        'message' => "Login url generated",
        'data' => $data,
        'requet' => $request->all()
    ], 200);

}
    public function update_profile(Request $request, $wifi_user_id) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors()
            ]);
        }

        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }
        $user = WifiUserAccount::find($wifi_user_id);
        $user->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => "Updating WifiUser Profile success!",
            "data" => $user
        ], 200);
    }

    public function get_profile($wifi_user_id) {
        $user = WifiUserAccount::select("first_name", "last_name", "phone", "email", "state", "district", "pin_code")->where('id', '=', $wifi_user_id)->get()->first();
        return response()->json([
            'success' => true,
            'message' => "Getting WifiUser Profile success!",
            "data" => $user
        ], 200);
    }

    public function verify_url_code(Request $request){
        $validator = Validator::make($request->all(), [
            'pdoa' => 'required|string',// |exists:network_groups,id',
            'url_code' => 'required'
        ]);

        $inputs = $request->all();

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 400);
        }

        $wifidevice = WiFiDevice::where('pdoa',$request->pdoa)->where('url_code',$request->url_code)->first();
        if($wifidevice){
            $wifidevice->status = 'Verified';
            $wifidevice->save();
            $phone = $wifidevice->phone;
            // Check radius and insert/update
            $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->where('username',$wifidevice->usermac)->where('pdoa',$wifidevice->pdoa)->first();

            if(! $radius_wifiuser){

                $network_settings = Network_setting::where('pdoa_id',$request->pdoa)->first();
                $logout_time = time()+$network_settings->freeDailySession*60;
                $bandwidth = $network_settings->freeBandwidth;
                $dw_limit = $network_settings->freeDataLimit;
                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->insert([
                    'username' => $wifidevice->usermac,
                    'value' => $wifidevice->usermac,
                    'pdoa' => $wifidevice->pdoa, 
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'bandwidth' => $bandwidth,
                    'logout_time' => $logout_time,
                    'download_limit' => $dw_limit
                ]);
            }else{
                $network_settings = Network_setting::where('pdoa_id',$request->pdoa)->first();
                $logout_time = time()+$network_settings->freeDailySession*60;
                $bandwidth = $network_settings->freeBandwidth;
                $dw_limit = $network_settings->freeDataLimit;
                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')
                ->where('username', $wifidevice->usermac)
                ->where('pdoa', $wifidevice->pdoa)
                ->update([
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'bandwidth' => $bandwidth,
                    'logout_time' => $logout_time,
                    'download_limit' => $dw_limit
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "User Login OTP Verified",
                "data" => $wifidevice,
                'radius_wifiuser' => $radius_wifiuser
            ], 200);	
        }else{
            return response()->json([
                'success' => false,
                'message' => "User Login OTP Verification failed",
                "data" => null,
            ], 200);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\wi_fi_users  $wi_fi_users
     * @return \Illuminate\Http\Response
     */
    public function show(wi_fi_users $wi_fi_users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\wi_fi_users  $wi_fi_users
     * @return \Illuminate\Http\Response
     */
    public function edit(wi_fi_users $wi_fi_users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\wi_fi_users  $wi_fi_users
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usermac' => 'required|string|exists:wi_fi_devices,usermac',
            'pdoa' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 400);
        }

        $wifidevice = WiFiDevice::where('usermac',$request->usermac)->where('pdoa',$request->pdoa)->first();

        if($wifidevice){
            return response()->json([
                'success' => true,
                'message' => "User Login OTP Verified",
                "data" => $wifidevice
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => "User does not exist",
                'data' => null
            ], 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\wi_fi_users  $wi_fi_users
     * @return \Illuminate\Http\Response
     */
    public function destroy(wi_fi_users $wi_fi_users)
    {
        //
    }

    public function get_plan_list($pdoa_id)
    {
        try{
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Getting Internet Plans success!',
        //     'pdoa_id' => $pdoa_id
        // ]);
        //$user = auth()->user();
        
        $plans = Internet_plans::where('pdoa_id', $pdoa_id)->get();
    

        return response()->json([
            'success' => true,
            'message' => 'Getting Internet Plans success!',
            "data" => $plans,
            'pdoa' => $pdoa_id
        ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function generate_code($type, $length) {
        if($type == 'integer'){
            $characters = '0123456789';
        }else{
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }


    public function send_sms($pdoa, $otp, $url_code, $phone, $os)
    {        
        $phone = '91'.$phone;
        $sms_gateway = Sms_gateway::where('pdoa_id', $pdoa)->first();
        $key = $sms_gateway->key;
        // $os ='iOS'; 
        if($os == 'iOS'){
            $sms_template = Sms_template::where('pdoa_id',$pdoa)->skip(1)->first();
            $pdoa_info = PDOA::find($pdoa);
            $domain_name = $pdoa_info->domain_name;
            $url_code = $domain_name.'/a/'.$url_code;
            $curl = curl_init();
            Sms_logs::create([
                'receiver_phone' => $phone,
                'text' => "{\n  \"flow_id\": \"$sms_template->dlt_id\",\n  \"sender\": \"$sms_template->sender_id\",\n  \"mobiles\": \"$phone\",\n  \"otp\": \"$otp\",\n  \"url\": \"$url_code\"\n    \n}",
                'pdoa_id' => $pdoa,
            ]);
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"$sms_template->dlt_id\",\n  \"sender\": \"$sms_template->sender_id\",\n  \"mobiles\": \"$phone\",\n  \"otp\": \"$otp\",\n  \"url\": \"$url_code\"\n    \n}",
                CURLOPT_HTTPHEADER => [
                    "authkey: $key",
                    "content-type: application/JSON"
                ],
            ]);
        } else {
            $sms_template = Sms_template::where('pdoa_id', $pdoa)->first();
            $curl = curl_init();
            // return $sms_template;
            Sms_logs::create([
                'receiver_phone' => $phone,
                'text' => "{\n  \"flow_id\": \"$sms_template->dlt_id\",\n  \"sender\": \"$sms_template->sender_id\",\n  \"mobiles\": \"$phone\",\n  \"otp\": \"$otp\"\n    \n}",
                'pdoa_id' => $pdoa,
            ]);
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"$sms_template->dlt_id\",\n  \"sender\": \"$sms_template->sender_id\",\n  \"mobiles\": \"$phone\",\n  \"otp\": \"$otp\"\n    \n}",
                CURLOPT_HTTPHEADER => [
                    "authkey: $key",
                    "content-type: application/JSON"
                ],
            ]);
        }
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
    
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
        
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('wifiapi')->factory()->getTTL() * 60
        ]);
    }

    protected function verify_email(Request $request)
    {
        $user_email = $request->input("email");
        $code = $request->input("code");
        $user = WifiUserAccount::where(["email" => $user_email])->get()->first();
        if($user["email_verify_code"] == $code) {
            $user->update(["email_verified" => 1]);
            return response()->json([
                'success' => true,
                'message' => 'Your email is successfully verified!',
            ]);        
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Your are no correct user!',
            ]);
        }
    }
}
