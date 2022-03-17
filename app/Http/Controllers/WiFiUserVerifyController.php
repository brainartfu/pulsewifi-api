<?php

namespace App\Http\Controllers;

use App\Models\WiFiUserVerify;
use Illuminate\Http\Request;
use App\Rules\PhoneNumber;
use Validator;

class WiFiUserVerifyController extends Controller
{

    public function send_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_phone' => ['required', new PhoneNumber],
            'challenge' => 'required|string',
            'usermac' => 'required|string',
            'os' => 'nullable|string',
            'location_id' => 'required|integer',
            'pdoa' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $create_otp = $validator->validated();
        $create_otp['otp'] = Self::generate_code('integer',4);
        $create_otp['url_code'] = Self::generate_code('string',6);
        $create_otp['status'] = 'Not Verfied';

        $otp = WiFiUserVerify::create($create_otp);

        if($otp){
            if($request->os == 'iOS'){
                $otp_msg = 'Your OTP for Pulse WiFi login is '.$create_otp['otp'].'.';
            }else{
                $otp_msg = 'Your OTP is '.$create_otp['otp'].'. To get uninterrupted internet download Pulse WiFi App - http://console.pulsewifi.net/login/a/'.$create_otp['url_code'];
            }
            $sms_send_resonse = Self::send_sms($otp_msg, $request->user_phone, $request->pdoa);
            return response()->json([
                'success' => true,
                'message' => 'Otp sent'
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while seding otp',
                'data' => null
            ], 201);
        }
    }

    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_phone' => ['required', new PhoneNumber],
            'otp' => 'required|integer',
            'challenge' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $verify_otp = WiFiUserVerify::where('user_phone',$request->user_phone)->where('otp',$request->otp)->first();
        if($verify_otp){

            $verify_otp->status = 'Verified';
            $verify_otp->save();

            $login_url = '';
            $challenge = $request->challenge;
            $uamsecret = '';

            $username = $password = 'freewifi';
            $hexchal = pack ("H32", $challenge);
            $newchal = $uamsecret ? pack("H*", md5($hexchal . $uamsecret)) : $hexchal;
            $response = md5("\0" .$password . $newchal);
            $newpwd = pack("a32", $password);
            $pappassword = implode ('', unpack("H32", ($newpwd ^ $newchal)));
                
            $login_url = 'http://172.22.100.1:3990/logon?username='.$username.'&response='.$response.'&userurl=https://play.google.com/store/apps/details?id=com.wifi.zayfi';
            // $verify_url_code->login_url = $login_url;

            return response()->json([
                'success' => true,
                'message' => 'OTP Verified Successfully',
                'login_url' => $login_url
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
                'request' => $request->all()
            ], 201);
        }
    }


    public function verify_url($url_code)
    {
        $verify_url_code = WiFiUserVerify::where('url_code',$url_code)->first();
        if($verify_url_code){

            $verify_url_code->status = 'Verified';
            $verify_url_code->save();

            $login_url = '';
            $challenge = $verify_url_code->challenge;
            $uamsecret = '';

            $username = $password = 'freewifi';
            $hexchal = pack ("H32", $challenge);
            $newchal = $uamsecret ? pack("H*", md5($hexchal . $uamsecret)) : $hexchal;
            $response = md5("\0" .$password . $newchal);
            $newpwd = pack("a32", $password);
            $pappassword = implode ('', unpack("H32", ($newpwd ^ $newchal)));
                
            $login_url = 'http://172.22.100.1:3990/logon?username='.$username.'&response='.$response.'&userurl=https://play.google.com/store/apps/details?id=com.wifi.zayfi';
            $verify_url_code->login_url = $login_url;

            return response()->json([
                'success' => true,
                'message' => 'User verified Successfully',
                'data' => $verify_url_code
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'User not verified',
            ], 201);
        }

        if($verify_url_code){
            $verify_url_code->status = 'Verified';
            $verify_url_code->save();
            return response()->json([
                'message' => 'OTP Verified Successfully',
                'otp' => $verify_url_code
            ], 201);
        }else{
            return response()->json([
                'message' => 'Verification failed',
                'request' => $url_code
            ], 201);
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


    public function send_sms($otp_msg, $phone, $pdoa){
        Sms_logs::create([
            'receiver_phone' => $phone,
            'text' => $otp_msg,
            'pdoa_id' => $pdoa,
        ]);
    }

    public function verify_otp_login_mac(Request $request){
        $validator = Validator::make($request->all(), [
            'challenge' => 'required|string',
            'usermac' => 'required|string',
            'os' => 'nullable|string',
            'location_id' => 'required|integer',
            'group_id' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $verify_user = WiFiUserVerify::where('usermac',$request->usermac)->where('location_id',$request->location_id)->where('status','verified')->first();
        $login_url = '';
        $challenge = $request->challenge;
        $uamsecret = '';

        $username = $password = 'freewifi';
        $hexchal = pack ("H32", $challenge);
        $newchal = $uamsecret ? pack("H*", md5($hexchal . $uamsecret)) : $hexchal;
        $response = md5("\0" .$password . $newchal);
        $newpwd = pack("a32", $password);
        $pappassword = implode ('', unpack("H32", ($newpwd ^ $newchal)));
            
        $login_url = 'http://172.22.100.1:3990/logon?username='.$username.'&response='.$response.'&userurl=https://play.google.com/store/apps/details?id=com.wifi.zayfi';
            
        if($verify_user){
            return response()->json([
                'success' => true,
                'message' => 'User already verified',
                'login_url' => $login_url
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not verified',
            ], 201);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\WiFiUserVerify  $wiFiUserVerify
     * @return \Illuminate\Http\Response
     */
    public function show(WiFiUserVerify $wiFiUserVerify)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WiFiUserVerify  $wiFiUserVerify
     * @return \Illuminate\Http\Response
     */
    public function edit(WiFiUserVerify $wiFiUserVerify)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WiFiUserVerify  $wiFiUserVerify
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WiFiUserVerify $wiFiUserVerify)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WiFiUserVerify  $wiFiUserVerify
     * @return \Illuminate\Http\Response
     */
    public function destroy(WiFiUserVerify $wiFiUserVerify)
    {
        //
    }
}
