<?php

namespace App\Http\Controllers;

use App\Models\PDOA;
use App\Models\Users;
use App\Models\Payment_setting;
use App\Models\Mail_server;
use App\Models\Sms_gateway;
use App\Models\Radacct;
use App\Models\Radcheck;
use App\Models\Network_setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Validator;

class PDOAController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['get_pdoa']]);

    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'franchise_fee' => 'required|integer|min:1',
            'distributor_fee' => 'required|integer|min:1',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'id_proof' => 'required',
            'id_proof_no' => 'required',
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
        $not_for_user_keys = [
            'brand_name',
            'brand_logo',
            'platform_bg',
            'pdoa_status',
            'pdoa_plan_id',
            'domain_name',
            'incorporation_cert',
        ];
        foreach ($request->all() as $key => $value) {
            $arr_create_keys[$key] = $value;
            if (!in_array($key, $not_for_user_keys)) {
                $req_array[$key] = $value;
            }
        }
        $arr_create_keys["id"] = "pulsewifi_" . date("Y-m-d_H-i-s");
        PDOA::create($arr_create_keys);
        $res_pdoa  = PDOA::find($arr_create_keys["id"]);
        $verify_code = mt_rand(1000, 9999);

        $client = new \GuzzleHttp\Client();
        $geocoder = new \Spatie\Geocoder\Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));

        // $req_array = $request->all();
        $geocoder->setCountry(config('geocoder.country', $req_array['country']));
        $address_to_geocode = Self::sanitize_address($req_array);
        $geoCode = $geocoder->getCoordinatesForAddress($address_to_geocode);
        $req_array['lead_process'] = 2;
        $req_array['pdoa_id'] = $arr_create_keys["id"];
        $req_array['enabled'] = 1;
        $req_array['latitude'] = $geoCode['lat'];
        $req_array['longitude'] = $geoCode['lng'];
        $req_array['password'] = Hash::make($request->input('password'));
        $req_array['email_verification_code'] = $verify_code;
        $req_array['role'] = 2;

        $user = Users::create($req_array);
        $response_user = Users::where(['email' => $user->email])->first();

        if ($request->file('brand_logo')) {
            if (!($request->file('brand_logo')->getClientOriginalExtension() == "jpg" || $request->file('brand_logo')->getClientOriginalExtension() == "jpeg" || $request->file('brand_logo')->getClientOriginalExtension() == "png" || $request->file('brand_logo')->getClientOriginalExtension() == "webp")) {
                $valid['brand_logo'] = "The brand logo image should be jpg or png format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $file_name =  $arr_create_keys["id"] . "_logo." . $request->file('brand_logo')->getClientOriginalExtension();
                $file_path = $request->file('brand_logo')->storeAs(
                    'public/PDOA', $file_name
                );
                $res_pdoa->update(['brand_logo' => $file_path]);
            }
        } else {
            $res_pdoa->update(['brand_logo' => ""]);

        }

        if ($request->file('favicon')) {
            if (!($request->file('favicon')->getClientOriginalExtension() == "jpg" || $request->file('favicon')->getClientOriginalExtension() == "jpeg" || $request->file('favicon')->getClientOriginalExtension() == "png" || $request->file('favicon')->getClientOriginalExtension() == "ico")) {
                $valid['favicon'] = "The Favicon should be jpg or png or ico file format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $file_name =  $arr_create_keys["id"] . "favicon." . $request->file('favicon')->getClientOriginalExtension();
                $file_path = $request->file('favicon')->storeAs(
                    'public/PDOA', $file_name
                );
                $res_pdoa->update(['favicon' => $file_path]);
            }
        } else {
            $res_pdoa->update(['favicon' => ""]);
        }
        if ($request->file('platform_bg')) {
            if (!($request->file('platform_bg')->getClientOriginalExtension() == "jpg" || $request->file('platform_bg')->getClientOriginalExtension() == "jpeg" || $request->file('platform_bg')->getClientOriginalExtension() == "png" || $request->file('brand_logo')->getClientOriginalExtension() == "webp")) {
                $valid['platform_bg'] = "The platform background image should be jpg or png format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $file_name = $arr_create_keys["id"] . "_bg." . $request->file('platform_bg')->getClientOriginalExtension();
                $file_path = $request->file('platform_bg')->storeAs(
                    'public/PDOA', $file_name
                );
                $res_pdoa->update(['platform_bg' => $file_path]);
            }
        } else {
            $res_pdoa->update(['platform_bg' => ""]);
        }
        if ($request->file('upload_id_proof')) {

            $file_name = "upload_id_proof_" . $response_user['id'] . "." . $request->file('upload_id_proof')->getClientOriginalExtension();
            $file_path = $request->file('upload_id_proof')->storeAs(
                'public/upload_id_proof', $file_name
            );
            $res_pdoa->update(['upload_id_proof' => $file_path]);
            $response_user->update(['upload_id_proof' => $file_path]);
        }
        if ($request->file('incorporation_cert')) {

            $file_name = "incorporation_cert_" . $response_user['id'] . "." . $request->file('incorporation_cert')->getClientOriginalExtension();
            $file_path = $request->file('incorporation_cert')->storeAs(
                'public/incorporation_cert', $file_name
            );
            $res_pdoa->update(['incorporation_cert' => $file_path]);
        }
        $res_pdoa["user_id"] = $response_user['id'];
        Payment_setting::create([
            'name' => "Razorpay",
            'key' => "tempKey",
            'secret' => "tempSecret",
            'callback_url' => "tempCallBackURL",
            'status' => 0,
            'type' => 0,
            'pdoa_id' =>$arr_create_keys["id"],
        ]);
        Mail_server::create([
            'name' => "SendGrid",
            'sender_name' => $request->input("firstname") . ' ' . $request->input("lastname"),
            'sender_email' => $request->input("email"),
            'api_key' => "tempKey",
            'status' => 0,
            'pdoa_id' =>$arr_create_keys["id"],
        ]);
        Sms_gateway::create([
            'name' => "Msg91",
            'key' => "tempKey",
            'secret' => "tempSecret",
            'status' => 0,
            'pdoa_id' =>$arr_create_keys["id"],
        ]);
        Network_setting::create([
            'guestEssid' => $request->input("brand_name"),
            'pdoa_id' => $arr_create_keys["id"],
            'freeWiFi' => 1,
            'freeBandwidth' => 150,
            'freeDailySession' => 60,
            'freeDataLimit' => 500,
            'serverWhitelist' => 'google.com,login.cnctdwifi.com,www.yahoo.com',
            'domainWhitelist' => '.google.com,.cnctdwifi.com,.yahoo.com'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PDOA successfully registered',
            "data" => $res_pdoa,
        ]);

    }

    public function get()
    {
        $PODAs = PDOA::leftJoin('pdoa_plan', "pdoa_plan.id", "=", "pdoas.pdoa_plan_id")
            ->select("pdoas.*", "pdoa_plan.plan_name", "pdoa_plan.price", "pdoa_plan.max_wifi_router_count")
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting PDOAs success!',
            'data' => $PODAs,
        ]);
    }

    public function get_pdoa($domain_name)
    {
        $pdoa = PDOA::where('domain_name', $domain_name)->get();
        if (count($pdoa)) {
            return response()->json([
                'success' => true,
                'message' => 'Getting PDOA success!',
                'data' => $pdoa->first(),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'NoPDOA',
            ], 404);
        }
    }

    public function update(Request $request, $pdoa_id)
    {
        $pdoa = PDOA::find($pdoa_id);
        $user = Users::where('pdoa_id', '=', $pdoa_id)->where("role", "<", 3)->get()->first();

        if ($user->email != $request->input("email")) {
            $email = 'string|min:2|max:100|unique:users';
        } else {
            $email = 'string|min:2|max:100';
        }
        if ($user->username != $request->input("username")) {
            $username = 'string|min:2|max:100|unique:users';
        } else {
            $username = 'string|min:2|max:100';
        }

        $validator = Validator::make($request->all(), [
            'brand_name' => 'string|min:2|max:100',
            'firstname' => 'string|min:2|max:100',
            'lastname' => 'string|min:2|max:100',
            'email' => $email,
            'username' => $username,
            'password' => 'string|confirmed|min:8',
            'company_name' => 'string|min:2|max:100',
            'designation' => 'string|min:2|max:100',
            'id_proof' => 'string',
            'id_proof_no' => 'string',
            'address' => 'string|min:2|max:100',
            'city' => 'string|min:2|max:100',
            'state' => 'string|min:2|max:100',
            'country' => 'string|min:2|max:100',
            'postal_code' => 'integer|min:100000|max:999999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $not_for_user_keys = [
            'brand_name',
            'brand_logo',
            'platform_bg',
            'pdoa_status',
            'pdoa_plan_id',
            'domain_name',
            'cin_no',
            'incorporation_cert',
        ];
        
        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
            if (!in_array($key, $not_for_user_keys)) {
                $req_array[$key] = $value;
            }
            if ($key == 'password') {
                $req_array[$key] = Hash::make($value);
            }
        }
        $user = Users::where(['pdoa_id' => $pdoa_id, 'role' => 2])->get()->first();
        $user->update($req_array);
        if ($request->file('brand_logo')) {
            if (!($request->file('brand_logo')->getClientOriginalExtension() == "jpg" || $request->file('brand_logo')->getClientOriginalExtension() == "jpeg" || $request->file('brand_logo')->getClientOriginalExtension() == "png" || $request->file('brand_logo')->getClientOriginalExtension() == "webp")) {
                $valid['brand_logo'] = "The brand logo image should be jpg or png format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $del_logo = $pdoa["brand_logo"];
                if ($del_logo && \Storage::exists($del_logo)) {
                    \Storage::delete($del_logo);
                }

                $file_name = $pdoa_id . "_logo." . $request->file('brand_logo')->getClientOriginalExtension();
                $file_path = $request->file('brand_logo')->storeAs(
                    'public/PDOA', $file_name
                );
                $arr_update_keys['brand_logo'] = $file_path;
            }
        }
        if ($request->file('favicon')) {
            if (!($request->file('favicon')->getClientOriginalExtension() == "jpg" || $request->file('favicon')->getClientOriginalExtension() == "jpeg" || $request->file('favicon')->getClientOriginalExtension() == "png" || $request->file('favicon')->getClientOriginalExtension() == "ico")) {
                $valid['favicon'] = "The Favicon should be jpg or png or ico file format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $file_name = $pdoa_id . "favicon." . $request->file('favicon')->getClientOriginalExtension();
                $file_path = $request->file('favicon')->storeAs(
                    'public/PDOA', $file_name
                );
                $arr_update_keys['favicon'] = $file_path;
            }
        }
        if ($request->file('platform_bg')) {
            if (!($request->file('platform_bg')->getClientOriginalExtension() == "jpg" || $request->file('platform_bg')->getClientOriginalExtension() == "jpeg" || $request->file('platform_bg')->getClientOriginalExtension() == "png" || $request->file('brand_logo')->getClientOriginalExtension() == "webp")) {
                $valid['platform_bg'] = "The platform background image should be jpg or png format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            } else {
                $del_bg = $pdoa["platform_bg"];
                if ($del_bg && \Storage::exists($del_bg)) {
                    \Storage::delete($del_bg);
                }

                $file_name = $pdoa_id . "_bg." . $request->file('platform_bg')->getClientOriginalExtension();
                $file_path = $request->file('platform_bg')->storeAs(
                    'public/PDOA', $file_name
                );
                $arr_update_keys['platform_bg'] = $file_path;
            }
        }
        if ($request->file('upload_id_proof')) {

            $file_name = "upload_id_proof_" . $user['id'] . "." . $request->file('upload_id_proof')->getClientOriginalExtension();
            $file_path = $request->file('upload_id_proof')->storeAs(
                'public/upload_id_proof', $file_name
            );
            $arr_update_keys['upload_id_proof'] = $file_path;
            $user->update(['upload_id_proof' => $file_path]);
        }

        if ($request->file('incorporation_cert')) {

            $file_name = "incorporation_cert_" . $user['id'] . "." . $request->file('incorporation_cert')->getClientOriginalExtension();
            $file_path = $request->file('incorporation_cert')->storeAs(
                'public/incorporation_cert', $file_name
            );
            $arr_update_keys['incorporation_cert'] = $file_path;
        }
        $pdoa->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'PDOA successfully updated.',
            'data' => $pdoa,
        ]);

    }

    public function delete($pdoa_id)
    {
        $pdoa = PDOA::find($pdoa_id);
        $del_logo = $pdoa["brand_logo"];
        if ($del_logo && \Storage::exists($del_logo)) {
            \Storage::delete($del_logo);
        }
        $del_bg = $pdoa["platform_bg"];
        if ($del_bg && \Storage::exists($del_bg)) {
            \Storage::delete($del_bg);
        }
        $del_id_proof = $pdoa["upload_id_proof"];
        if ($del_id_proof && \Storage::exists($del_id_proof)) {
            \Storage::delete($del_id_proof);
        }

        $pdoa->delete();

        return response()->json([
            'success' => true,
            'message' => 'PDOA is successfully deleted!',
        ]);

    }

    public function get_wifi_users_status($pdoa_id)
    {
        $now = date_create(date("Y-m-d H:i:s"));
        $today = date_create(date("Y-m-d"))->format('Y-m-d');
        date_add($now, date_interval_create_from_date_string('-1 days'));
        $yesterday = $now->format('Y-m-d');
        date_add($now, date_interval_create_from_date_string('-6 days'));
        $cur_week_from = $now->format('Y-m-d H:i:s');
        date_add($now, date_interval_create_from_date_string('-7 days'));
        $prev_week_from = $now->format('Y-m-d H:i:s');
        $now = time();
        $total_users = Radcheck::select('username')
                        ->where('pdoa', '=', $pdoa_id)
                        ->groupBy('username')
                        ->get()
                        ->count();
        $expired_users = Radcheck::select('username')
                        ->where('pdoa', '=', $pdoa_id)
                        ->where(DB::raw('LENGTH(username)'),'<',15)
                        ->where('logout_time', '<', $now)
                        ->groupBy('username')
                        ->get()
                        ->count();
        $online_today = Radacct::leftJoin('radcheck', 'radacct.username', '=', 'radcheck.username')
                        ->select('radacct.username')
                        ->where('radcheck.pdoa', '=', $pdoa_id)
                        ->where('radacct.acctstoptime', 'like', '%'.$today.'%')
                        ->groupBy('radacct.username')
                        ->get()
                        ->count();
        $online_yesterday = Radacct::leftJoin('radcheck', 'radacct.username', '=', 'radcheck.username')
                        ->select('radacct.username')
                        ->where('radcheck.pdoa', '=', $pdoa_id)
                        ->where('radacct.acctstoptime', 'like', '%'.$yesterday.'%')
                        ->groupBy('radacct.username')
                        ->get()
                        ->count();
        $cur_week_users = Radacct::leftJoin('radcheck', 'radacct.username', '=', 'radcheck.username')
                        ->select('radacct.username')
                        ->where('radcheck.pdoa', '=', $pdoa_id)
                        ->where('radacct.acctstoptime', '>=', $cur_week_from)
                        ->groupBy('radacct.username')
                        ->get()
                        ->count();
        $prev_week_users = Radacct::leftJoin('radcheck', 'radacct.username', '=', 'radcheck.username')
                        ->select('radacct.username')
                        ->where('radcheck.pdoa', '=', $pdoa_id)
                        ->where('radacct.acctstoptime', '<', $cur_week_from)
                        ->where('radacct.acctstoptime', '>=', $prev_week_from)
                        ->groupBy('radacct.username')
                        ->get()
                        ->count();
        
        return response()->json([
            'success' => true,
            'message' => 'Getting wifi_users status of pdoa is success!',
            'data' => [
                'total_users' => $total_users,
                'online_today' => $online_today,
                'online_yesterday' => $online_yesterday,
                'expired_users' => $expired_users,
                'cur_week_users' => $cur_week_users,
                'prev_week_users' => $prev_week_users,
                ]
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