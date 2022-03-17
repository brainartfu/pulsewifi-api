<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Mail_server;
use App\Models\Users;
use App\Models\PDOA;
use App\Models\Wifi_router;
use App\Models\Email_logs;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['request_lead', 'get_user_fees', 'verifyEmail', 'forgotPassword', 'resetPassword']]);
    }

    public function get_user_fees($pdoa_id)
    {
        $fees = PDOA::find($pdoa_id);
        return response()->json([
            'success' => true,
            'message' => 'Getting User Fees success!',
            'data' => [
                'distributor_fee' => $fees["distributor_fee"],
                'franchise_fee' => $fees["franchise_fee"]
            ],
        ]);
    }

    public function get_leads($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $leads = Users::where("lead_process", "<>", 2)->orderBy("lead_process", "desc")->orderBy("role", "asc")->get();
        } else {
            $leads = Users::where("lead_process", "<>", 2)->where('pdoa_id', $pdoa_id)->orderBy("lead_process", "desc")->orderBy("role", "asc")->get();
        }
        return response()->json([
            'success' => true,
            'message' => 'Getting Leads success!',
            'data' => $leads,
        ]);
    }

    public function add_franchise(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'company_name' => 'string|min:2|max:100',
            'designation' => 'string|min:2|max:100',
            'id_proof' => 'required|string',
            'id_proof_no' => 'required|string',
            'identity_verification' => 'required|string',
            'address' => 'required|string|min:2|max:100',
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
        $req_array['role'] = 5;

        $user = Users::create($req_array);
        $response_user = Users::where(['email' => $user->email])->first();

        if ($request->file('upload_id_proof')) {

            $file_name = "upload_id_proof_" . $response_user->id . "." . $request->file('upload_id_proof')->getClientOriginalExtension();
            $file_path = $request->file('upload_id_proof')->storeAs(
                'public/upload_id_proof',
                $file_name
            );
            $response_user->update(['upload_id_proof' => $file_path]);
        }

        if ($request->file('passbook_cheque')) {

            $file_name = "passbook_cheque_" . $response_user->id . "." . $request->file('passbook_cheque')->getClientOriginalExtension();
            $file_path = $request->file('passbook_cheque')->storeAs(
                'public/passbook_cheque',
                $file_name
            );
            $response_user->update(['passbook_cheque' => $file_path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Franchise is successfully registered!',
            'data' => $response_user,
        ]);
    }

    public function add_distributor(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'company_name' => 'required|string|min:2|max:100',
            'designation' => 'required|string|min:2|max:100',
            'id_proof' => 'required|string',
            'id_proof_no' => 'required|string',
            'address' => 'required|string|min:2|max:100',
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
        $req_array['role'] = 4;

        $user = Users::create($req_array);
        $response_user = Users::where(['email' => $user->email])->first();

        if ($request->file('upload_id_proof')) {

            $file_name = "upload_id_proof_" . $user_id . "." . $request->file('upload_id_proof')->getClientOriginalExtension();
            $file_path = $request->file('upload_id_proof')->storeAs(
                'public/upload_id_proof',
                $file_name
            );
            $response_user->update(['upload_id_proof' => $file_path]);
        }
        if ($request->file('passbook_cheque')) {

            $file_name = "passbook_cheque_" . $response_user->id . "." . $request->file('passbook_cheque')->getClientOriginalExtension();
            $file_path = $request->file('passbook_cheque')->storeAs(
                'public/passbook_cheque',
                $file_name
            );
            $response_user->update(['passbook_cheque' => $file_path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request is successfully submitted!',
            'data' => $response_user,
        ]);
    }

    public function add_lead(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:2|max:100|unique:users',
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'role' => 'required|integer',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'address' => 'required|string|min:2|max:100',
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

        return response()->json([
            'success' => true,
            'message' => 'Request is successfully submitted!',
            'data' => $response_user,
        ]);
    }

    public function process_lead($user_id)
    {
        $result = Users::where(['id' => $user_id])->update(['enabled' => 1, 'lead_process' => 1]);
        return response()->json([
            'success' => true,
            'message' => 'Lead successfully processed!',
            'data' => $result,
        ]);
    }

    public function request_lead(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:2|max:100|unique:users',
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'role' => 'required|integer',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'address' => 'required|string|min:2|max:100',
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
        $req_array['pdoa_id'] = $pdoa_id;
        $req_array['lead_process'] = 0;
        $req_array['enabled'] = 0;
        $req_array['latitude'] = $geoCode['lat'];
        $req_array['longitude'] = $geoCode['lng'];
        $req_array['password'] = Hash::make($request->input('password'));
        $req_array['email_verification_code'] = $verify_code;

        $user = Users::create($req_array);
        $response_user = Users::where(['email' => $user->email])->first();

        // App::setLocale('fr');
        // Mail::to($user->email)->send(new EmailVerificationCode($user->email_verification_code));

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered',
            'data' => $response_user,
        ]);
    }

    public function delete($user_id)
    {
        $user = auth()->user();
        $del_user = Users::find($user_id);
        if (!$del_user) {
            return response()->json([
                'success' => false,
                'message' => 'NoUser',
            ]);
        }
        if ($user->role >= $del_user->role) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        $del_file = $del_user["profile_img_path"];
        Storage::delete($del_file);
        $del_user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User successfully deleted.',
        ]);
    }

    public function getStaffs($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $staffs = Users::where("role", "<>", 4)->where("role", "<>", 5)->orderBy("role")->get();
        } else {
            $staffs = Users::where("role", "<>", 4) - where("role", "<>", 5)->where("pdoa_id", $pdoa_id)->orderBy("role")->get();
        }

        if (count($staffs)) {
            $msg = "Getting staff list success!";
        } else {
            $msg = "Nostaff";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            "data" => $staffs,
        ]);
    }

    public function getDistributors($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $distributors = Users::where(["role" => 4])->where("lead_process", ">", 0)->get();
        } else {
            $distributors = Users::where(["role" => 4])->where('pdoa_id', $pdoa_id)->where("lead_process", ">", 0)->get();
        }

        if (count($distributors)) {
            for ($i = 0; $i < count($distributors); $i++) {
                $pdoa = PDOA::where(["id" => $distributors[$i]->pdoa_id])->get()->first();
                $belongs_franchise = Users::where(["role" => 5, "belongs_to" => $distributors[$i]->id])->get();
                $count_franchise_online = 0;
                if (count($belongs_franchise)) {
                    for ($j = 0; $j < count($belongs_franchise); $j++) {
                        $now = date_create(date("Y-m-d H:i:s"));
                        date_add($now, date_interval_create_from_date_string("-180 seconds"));
                        $onlinetime = $now->format('Y-m-d H:i:s');
                        $count_online = Wifi_router::where("owner_id", $belongs_franchise[$j]->id)->where("last_online", ">", $onlinetime)->count();
                        if ($count_online) {
                            $count_franchise_online++;
                        }
                    }
                }
                $distributors[$i]->total_franchise = count($belongs_franchise);
                $distributors[$i]->total_online = $count_franchise_online;
                $distributors[$i]->pdoa = ['id' => $pdoa->id, 'firstname' => $pdoa->firstname, 'lastname' => $pdoa->lastname];
            }

            $msg = "Getting distributor list success!";
        } else {
            $msg = "No distributors";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            "data" => $distributors,
        ]);
    }

    public function getPdoaDistributors($pdoa_id)
    {
        $distributors = Users::where(["role" => 4])->where('pdoa_id', $pdoa_id)->where("lead_process", ">", 0)->get();

        if (count($distributors)) {
            for ($i = 0; $i < count($distributors); $i++) {
                $pdoa = PDOA::where(["id" => $distributors[$i]->pdoa_id])->get()->first();
                $belongs_franchise = Users::where(["role" => 5, "belongs_to" => $distributors[$i]->id])->get();
                $count_franchise_online = 0;
                if (count($belongs_franchise)) {
                    for ($j = 0; $j < count($belongs_franchise); $j++) {
                        $now = date_create(date("Y-m-d H:i:s"));
                        date_add($now, date_interval_create_from_date_string("-180 seconds"));
                        $onlinetime = $now->format('Y-m-d H:i:s');
                        $count_online = Wifi_router::where("owner_id", $belongs_franchise[$j]->id)->where("last_online", ">", $onlinetime)->count();
                        if ($count_online) {
                            $count_franchise_online++;
                        }
                    }
                }
                $distributors[$i]->total_franchise = count($belongs_franchise);
                $distributors[$i]->total_online = $count_franchise_online;
                $distributors[$i]->pdoa = ['id' => $pdoa->id, 'firstname' => $pdoa->firstname, 'lastname' => $pdoa->lastname];
            }

            $msg = "Getting distributor list success!";
        } else {
            $msg = "No distributors";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            "data" => $distributors,
        ]);
    }

    public function getFranchises($distributor_id, $pdoa_id)
    {
        if ($distributor_id == 0) {
            $franchises = Users::where("role", 5)->where("lead_process", ">", 0)->where('pdoa_id', '=', $pdoa_id)->get();
        } else {
            $franchises = Users::where(["role" => 5, "belongs_to" => $distributor_id])->where("lead_process", ">", 0)->get();
        }

        if (count($franchises)) {
            for ($i = 0; $i < count($franchises); $i++) {
                $locations = Location::where(["owner_id" => $franchises[$i]->id])->get();
                $count_locations_online = 0;
                if (count($locations)) {
                    for ($j = 0; $j < count($locations); $j++) {
                        $now = date_create(date("Y-m-d H:i:s"));
                        date_add($now, date_interval_create_from_date_string("-180 seconds"));
                        $onlinetime = $now->format('Y-m-d H:i:s');
                        $count_online = Wifi_router::where("location_id", $locations[$j]->id)->where("last_online", ">", $onlinetime)->count();
                        if ($count_online) {
                            $count_locations_online++;
                        }
                    }
                }
                $franchises[$i]->total_locations = count($locations);
                $franchises[$i]->total_online = $count_locations_online;
            }

            $msg = "Getting Franchises list success!";
        } else {
            $msg = "NoFranchises";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            "data" => $franchises,
        ]);
    }

    public function getAllFranchises($distributor_id, $pdoa_id)
    {
        $franchises = Users::where("role", 5)->where("lead_process", ">", 0);
        if ($pdoa_id) {
            $franchises = $franchises->where('pdoa_id', '=', $pdoa_id);
        }
        if ($distributor_id) {
            $franchises = $franchises->where('belongs_to', '=', $distributor_id);
        }
        $franchises = $franchises->get();

        if (count($franchises)) {
            for ($i = 0; $i < count($franchises); $i++) {
                $pdoa = PDOA::where(["id" => $franchises[$i]->pdoa_id])->get()->first();
                $distributor = Users::where(["id" => $franchises[$i]->belongs_to])->get()->first();
                $distributors = Users::where(["role" => 4])->where('pdoa_id', $franchises[$i]->pdoa_id)->where("lead_process", ">", 0)->get();
                $locations = Location::where(["owner_id" => $franchises[$i]->id])->get();
                $count_locations_online = 0;
                if (count($locations)) {
                    for ($j = 0; $j < count($locations); $j++) {
                        $now = date_create(date("Y-m-d H:i:s"));
                        date_add($now, date_interval_create_from_date_string("-180 seconds"));
                        $onlinetime = $now->format('Y-m-d H:i:s');
                        $count_online = Wifi_router::where("location_id", $locations[$j]->id)->where("last_online", ">", $onlinetime)->count();
                        if ($count_online) {
                            $count_locations_online++;
                        }
                    }
                }
                $franchises[$i]->total_locations = count($locations);
                $franchises[$i]->total_online = $count_locations_online;
                $franchises[$i]->pdoa = ['id' => $pdoa->id, 'firstname' => $pdoa->firstname, 'lastname' => $pdoa->lastname];
                $franchises[$i]->distributor = ['id' => $distributor->id, 'firstname' => $distributor->firstname, 'lastname' => $distributor->lastname];
                $franchises[$i]->distributors = $distributors;
            }

            $msg = "Getting Franchises list success!";
        } else {
            $msg = "NoFranchises";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            "data" => $franchises,
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

    protected function verifyEmail(Request $request)
    {
        $user_email = $request->input("email");
        $code = $request->input("code");
        $user = Users::where(["email" => $user_email])->get()->first();
        if ($user["email_verification_code"] == $code) {
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

    protected function forgotPassword(Request $request)
    {
        $user_email = $request->input("email");
        $pdoa_id = $request->input("pdoa_id");
        $server = PDOA::where(["id" => $pdoa_id])->get()->first();
        $user = Users::where(['email' => $user_email])->get()->first();
        if (!$user)
            return response()->json([
                'success' => false,
                'message' => 'You are not correct user!',
            ]);
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
                    <a href="https://' . $server["domain_name"] . '" title="logo" target="_blank">
                        <img src="' . $logo . '" title="logo" alt="logo" width="auto" height="100" />
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
                            You have
                            requested to reset your password</h1>
                            <span
                            style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                            <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
                            We cannot simply send you your old password. A unique link to reset your
                            password has been generated for you. To reset your password, click the
                            following link and follow the instructions.
                            </p>
                            <a href="https://' . $server["domain_name"] . '/resetPassword?email=' . $user_email . '&hashedpassword=' . $user["password"] . '" target="_blank"
                            style="background:#20e277;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">Reset
                            Password</a>
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
                        &copy; <strong>' . $server["domain_name"] . '</strong></p>
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
        $email->setSubject("Forget Password request from " . $server["domain_name"]);
        $email->addTo($user_email, $user["firstname"] . " " . $user["lastname"]);
        $email->addContent(
            "text/html",
            $content
        );
        $sendgrid = new \SendGrid($mail_server["api_key"]);
        try {
            $response = $sendgrid->send($email);
            Email_logs::create([
                'receiver_email' => $user["email"],
                'subject' => "Forget Password request from " . $server["domain_name"],
                'pdoa_id' => $pdoa_id,
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }

        return response()->json([
            'success' => true,
            'message' => 'Forget password requested successfully!',
        ]);
    }

    protected function resetPassword(Request $request)
    {
        $user_email = $request->input("email");
        $originpw = $request->input("hashedpassword");
        $password = $request->input("password");
        $user = Users::where(['email' => $user_email])->get()->first();
        $new_password = Hash::make($password);
        if ($user["password"] == $originpw) {
            $user->update(['password' => $new_password]);

            return response()->json([
                'success' => true,
                'message' => 'Password has reset successfully!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You are not correct user!',
            ]);
        }
    }
}
