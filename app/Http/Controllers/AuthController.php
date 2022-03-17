<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Users;
use App\Models\PDOA;
use App\Models\Mail_server;
use App\Models\Email_logs;
use App\Rules\ChkCurrentPassword;
use App\Rules\IsValidPassword;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'updateuser', 'wifiuser_login', 'wifiuser_register']]);
    }

    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:2|max:100|unique:users',
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'role' => 'required|integer',
            'belongs_to' => 'integer',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'address' => 'string|min:2|max:100',
            'city' => 'required|string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
            'country' => 'required|string|min:2|max:100',
            'postal_code' => 'required|integer|min:100000|max:999999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $verify_code = mt_rand(1000, 9999);

        $client = new \GuzzleHttp\Client();
        $geocoder = new \Spatie\Geocoder\Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));

        $req_array = $request->all();
        $geocoder->setCountry(config('geocoder.country', $req_array['country']));
        $address_to_geocode = Self::sanitize_address($req_array);
        $geoCode = $geocoder->getCoordinatesForAddress($address_to_geocode);
        $req_array['lead_process'] = 2;
        $req_array['pdoa_id'] = $pdoa_id;
        $req_array['enabled'] = 1;
        $req_array['latitude'] = $geoCode['lat'];
        $req_array['longitude'] = $geoCode['lng'];
        $req_array['password'] = Hash::make($request->input('password'));
        $req_array['email_verification_code'] = $verify_code;

        $user = Users::create($req_array);
        $response_user = Users::where(['email' => $user->email])->first();

        $server = PDOA::where(["id" => $pdoa_id])->get()->first();
        $mail_server = Mail_server::where(["pdoa_id" => $pdoa_id])->get()->first();
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
                        <img src="'.$logo.'" title="logo" alt="logo" width="auto" height="100" />
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
                            You have to
                            Verify your Email</h1>
                            <span
                            style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                            <p style="color:black; font-size:20px;line-height:24px; margin:0;">
                            Your email verify code is '.$verify_code.'.
                            </p>
                            <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
                            You registered as new user of '.$server["domain_name"].'.<br>
                            Please click below button to verify your email.
                            </p>
                            <a href="https://'.$server["domain_name"].'/verifyEmail?email='.$user["email"].'" target="_blank"
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
                        &copy; <strong>'.$server["domain_name"].'</strong></p>
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
        $email->setSubject("Verify Email request from " . $server["domain_name"]);
        $email->addTo($user["email"], $user["firstname"] . " " . $user["lastname"]);
        $email->addContent(
            "text/html", $content
        );
        $sendgrid = new \SendGrid($mail_server["api_key"]);
        try {
            $response = $sendgrid->send($email);
            Email_logs::create([
                'receiver_email' => $user["email"],
                'subject' => "Verify Email request from " . $server["domain_name"],
                'pdoa_id' => $pdoa_id,
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }


        return response()->json([
            'success' => true,
            'message' => 'Request is successfully submitted!',
            'data' => $response_user,
        ]);
    }

    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $password = $request->password;
        $hash = Hash::make($password);

        $insertData = array(
            'email' => $request->email,
            'password' => $hash,
        );
        if (!$token = auth()->guard('api')->attempt($validator->validated())) {
            return response()->json([
                'success' => false,
                'msg' => auth()->attempt($validator->validated()),
                'message' => 'This user is unauthorized.',
                'request' => $request->all()
            ]);
        }
        $user = Users::where([
            'email' => $request->input('email'),
        ])->first();
        if (!$user->email_verified) {
            return response()->json([
                'success' => false,
                'message' => 'This user is not email_verified. Please verify your email.',
            ], 201);
        }

        if (!$user->enabled) {
            return response()->json([
                'success' => false,
                'message' => 'This user is not enabled.',
            ], 201);
        }
        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => $this->respondWithToken($token)->original,
        ], 200);
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'User successfully logged out.',
        ]);
    }

    /**
     * Refresh token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'success' => true,
            'message' => 'User token successfully updated.',
            'data' => $this->respondWithToken(auth()->refresh())->original,
        ]);
        // return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json([
            'success' => true,
            'message' => 'Getting user data success',
            'data' => auth()->user(),
        ]);
        // return response()->json(auth()->user());
    }

    /**
     * Update user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request, $user_id)
    {
        $user = Users::find($user_id);
        if ($user->username != $request->input("username")) {
            $user_validation = "required|string|min:2|max:100|unique:users";
        } else {
            $user_validation = "required|string|min:2|max:100";
        }
        if ($request->input("password") != "") {
            $validator = Validator::make($request->all(), [
                'username' => $user_validation,
                'belongs_to' => 'integer',
                'current_password' => ['required', new ChkCurrentPassword()],
                'password' => ['string', 'confirmed', 'min:8'],
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'username' => $user_validation,
                'belongs_to' => 'integer',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
            if ($key == 'password') {
                $arr_update_keys['password'] = Hash::make($value);
            }

        }

        if ($request->file('profile_img')) {
            if (!($request->file('profile_img')->getClientOriginalExtension() == "jpg" || $request->file('profile_img')->getClientOriginalExtension() == "jpeg" || $request->file('profile_img')->getClientOriginalExtension() == "png")) {
                $valid['file'] = "The profile image should be jpg or png format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $file_name = "avatar_" . $user_id . "." . $request->file('profile_img')->getClientOriginalExtension();
                $file_path = $request->file('profile_img')->storeAs(
                    'public/profile_img', $file_name
                );
                $user->update(['profile_img_path' => $file_path]);
            }
        }

        $user->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Your profile successfully updated.',
            'data' => $user,
        ]);
    }

    public function updateuser(Request $request, $user_id)
    {
        $user = Users::find($user_id);
        if ($user->username != $request->input("username")) {
            $user_validation = "required|string|min:2|max:100|unique:users";
        } else {
            $user_validation = "required|string|min:2|max:100";
        }
        if ($request->input("password") != "") {
            $validator = Validator::make($request->all(), [
                'username' => $user_validation,
                'role' => 'integer',
                'belongs_to' => 'integer',
                // 'current_password' => ['required', new ChkCurrentPassword()],
                'password' => ['string', 'confirmed', 'min:8'],
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'username' => $user_validation,
                'role' => 'integer',
                'belongs_to' => 'integer',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        if ($user->role != $request->input("role")) {
            if ($user->role == 5) {
                if (Location::where(['owner_id' => $user->id])->get()->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This Franchise has own Location!',
                    ]);
                } else {
                    $user->belongs_to = null;
                }
            } else if ($user->role == 4) {
                if (Users::where(['belongs_to' => $user->id])->get()->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This Distributor has own franchises!',
                    ]);
                }
            }            
        }

        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
            if ($key == 'password') {
                $arr_update_keys['password'] = Hash::make($value);
            }

        }

        if ($request->file('profile_img')) {
            if (!($request->file('profile_img')->getClientOriginalExtension() == "jpg" || $request->file('profile_img')->getClientOriginalExtension() == "jpeg" || $request->file('profile_img')->getClientOriginalExtension() == "png")) {
                $valid['file'] = "The profile image should be jpg or png format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $file_name = "avatar_" . $user_id . "." . $request->file('profile_img')->getClientOriginalExtension();
                $file_path = $request->file('profile_img')->storeAs(
                    'public/profile_img', $file_name
                );
                $arr_update_keys['profile_img_path'] = $file_path;
            }
        }

        if ($request->file('upload_id_proof')) {

            $file_name = "upload_id_proof_" . $user_id . "." . $request->file('upload_id_proof')->getClientOriginalExtension();
            $file_path = $request->file('upload_id_proof')->storeAs(
                'public/upload_id_proof', $file_name
            );
            $arr_update_keys['upload_id_proof'] = $file_path;
        }

        if ($request->file('passbook_cheque')) {

            $file_name = "passbook_cheque_" . $user_id . "." . $request->file('passbook_cheque')->getClientOriginalExtension();
            $file_path = $request->file('passbook_cheque')->storeAs(
                'public/passbook_cheque', $file_name
            );
            $arr_update_keys['passbook_cheque'] = $file_path;
        }

        $user->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'User successfully updated.',
            'data' => $user,
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function sanitize_address($req_array)
    {
        $address = '';
        if (isset($req_array['address'])) {
            $address = $address . $req_array['address'];
        }
        if (isset($req_array['city'])) {
            if (strlen($address) > 0) {
                $address = $address . ', ';
            }
            $address = $address . $req_array['city'];
        }
        if (isset($req_array['state'])) {
            if (strlen($address) > 0) {
                $address = $address . ', ';
            }
            $address = $address . $req_array['state'];
        }

        if (isset($req_array['postal_code'])) {
            if (strlen($address) > 0) {
                $address = $address . ', ';
            }
            $address = $address . $req_array['postal_code'];
        }
        return $address;
    }
}
