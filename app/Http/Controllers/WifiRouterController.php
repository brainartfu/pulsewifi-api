<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Orders;
use App\Models\Location;
use App\Models\Users;
use App\Models\WifiRouterModel;
use App\Models\Network_setting;
use App\Models\PDOA;
use App\Models\Wifi_router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Validator;
use DateTime;


class WifiRouterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['get_inventory', 'heartbeat', 'settings','verify_router']]);
    }

    public function get_products($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $products = Wifi_router::leftJoin('wifi_router_model', 'wifi_router.model_id', '=', 'wifi_router_model.id')
                ->select("wifi_router.id", "wifi_router.owner_id", "wifi_router.model_id", "wifi_router_model.name as model_name", "wifi_router_model.images", "wifi_router.name as product_name", "wifi_router.mac_address", "wifi_router.created_at")
                ->where(["wifi_router.status" => 0, 'wifi_router.owner_id' => null])
                ->get();
        } else {
            $products = Wifi_router::leftJoin('wifi_router_model', 'wifi_router.model_id', '=', 'wifi_router_model.id')
                ->select("wifi_router.id", "wifi_router.owner_id", "wifi_router.model_id", "wifi_router_model.name as model_name", "wifi_router_model.images", "wifi_router.name as product_name", "wifi_router.mac_address", "wifi_router.created_at")
                ->where('wifi_router.pdoa_id', $pdoa_id)
                ->where(["wifi_router.status" => 0, 'wifi_router.owner_id' => null])
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Products success!',
            'data' => $products,
        ]);
    }

    public function get_inventory($pdoa_id)
    {
        $products = WifiRouterModel::leftJoin('wifi_router', 'wifi_router_model.id', '=', 'wifi_router.model_id')
            ->select('wifi_router_model.id', "wifi_router_model.name", "wifi_router_model.description", "wifi_router_model.price", "wifi_router_model.images", DB::raw('SUM(CASE WHEN wifi_router.status=0 THEN 1 ELSE 0 END) as total_count'))
            ->where('wifi_router.status', "=", 0)
            ->where('wifi_router.pdoa_id', $pdoa_id)
            ->where('wifi_router_model.status', "!=", 0)
            ->groupBy("wifi_router_model.id", "wifi_router_model.name", "wifi_router_model.description", "wifi_router_model.images", "wifi_router_model.price")
            ->get();
        $orders = Orders::where("status", "<", 3)->get();
        $mis_inv = [];
        foreach ($orders as $key => $order) {
            $models = explode(",", $order["model_ids"]);
            $non_processed = explode(",", $order["non_processed"]);
            foreach ($models as $idx => $model) {
                if (array_key_exists($model, $mis_inv)) $mis_inv[$model] = (int)$mis_inv[$model] + (int)$non_processed[$idx];
                else $mis_inv[$model] = (int)$non_processed[$idx];
            }
        }
        $products = $products->map(function ($product, $key) {
            $cart_amount = Cart::select(DB::raw('SUM(request_amount) as request_amount'))
                ->where("status", "=", 0)
                ->where(["model_id" => $product["id"]])
                ->get()->first();
            if ($cart_amount) {
                $product["left_inventory"] = $product['total_count'] - $cart_amount['request_amount'];
            } else $product["left_inventory"] = $product['total_count'];
            return $product;
        });
        if (count($products)) {
            for ($i = 0; $i < count($products); $i++) {
                if (array_key_exists($products[$i]["id"], $mis_inv)) $products[$i]["left_inventory"] = (int)$products[$i]["left_inventory"] - (int)$mis_inv[$products[$i]["id"]];
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Getting Products inventory success!',
            'data' => $products,
        ]);
    }

    public function add(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            //'name' => 'required|string|min:2|max:100|unique:wifi_router',
            'name' => 'required|string|min:2|max:100',
            'mac_address' => 'required|string|min:17|max:18|unique:wifi_router',
            'model_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $secret = Str::random(30);
        $key = Str::random(20);


        $router = Wifi_router::create([
            'name' => $request->input('name'),
            'mac_address' => $request->input('mac_address'),
            'model_id' => $request->input('model_id'),
            'pdoa_id' => $pdoa_id
        ]);

        $router->secret = $secret;
        $router->key = $key;

        $router->config_version = 0;
        $router->save();
        return response()->json([
            'success' => true,
            'message' => 'Product successfully registered',
        ]);
    }

    public function update_product(Request $request, $router_id)
    {
        $router = Wifi_router::find($router_id);
        if (!$router) {
            return response()->json([
                'success' => false,
                'message' => 'NoProduct',
            ]);
        }
        if ($request->input("name") && $router->name != $request->input("name")) {
            $name_str = 'string|min:2|max:100|unique:wifi_router';
        } else {
            $name_str = 'string|min:2|max:100';
        }
        if ($request->input("mac") && $router->mac != $request->input("mac")) {
            $mac_str = 'string|min:2|max:100|unique:wifi_router';
        } else {
            $mac_str = 'string|min:2|max:100';
        }
        $validator = Validator::make($request->all(), [
            'name' => $name_str,
            'mac_address' => $mac_str,
            'model_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }
        $router->update($arr_update_keys);

        return response()->json([
            'success' => true,
            'message' => 'Product successfully updated!',
        ]);
    }

    public function delete($router_id)
    {
        $del_router = Wifi_router::find($router_id);
        if (!$del_router) {
            return response()->json([
                'success' => false,
                'message' => 'NoWifiRouter',
            ]);
        }
        $del_router->delete();
        return response()->json([
            'success' => true,
            'message' => 'Wifi Router successfully deleted.',
        ]);
    }

    public function update_router_with_location($location_id, $router_id)
    {
        $router = Wifi_router::find($router_id);
        if ($location_id == 0) {
            $router->update(["location_id" => null]);
        } else {
            $router->update(["location_id" => $location_id]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Wifi Router is successfully updated!',
        ]);
    }

    public function get_location_router($location_id)
    {
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-180 seconds"));
        $onlinetime = $now->format('Y-m-d H:i:s');

        $routers = Wifi_router::select('*', DB::raw("CASE WHEN last_online > '$onlinetime' THEN 1 ELSE 0 END as online_status"))
            ->where(['location_id' => $location_id])->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting Wifi routers success!',
            "data" => $routers,
        ]);
    }

    public function get_no_location_router($user_id)
    {
        $now = date_create(date("Y-m-d H:i:s"));

        $routers = Wifi_router::where(['owner_id' => $user_id, 'location_id' => null, 'status' => 0])->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting Wifi routers success!',
            "data" => $routers,
        ]);
    }

    public function get_router($user_id, $pdoa_id)
    {
        $user = auth()->user();
        if ($user->role > 3 && $user_id == 0) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        if($user_id != 0) $user = Users::find($user_id);
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-180 seconds"));
        $onlinetime = $now->format('Y-m-d H:i:s');
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-7 days"));
        $inactivetime = $now->format('Y-m-d H:i:s');
        if ($user_id == 0) {
            $f_arr = Users::select("id")->where('pdoa_id','=',$pdoa_id)->get();
        } else {
            if($user['role'] == 4) { 
                $f_arr = Users::select('id')->where('belongs_to', '=', $user_id)->get();
            } else $f_arr = [['id'=> $user_id]];
        }

        $users = array();
        foreach ($f_arr as $key => $value) {
            $users[] = $value['id'];
        }
        $routers = Wifi_router::leftJoin('location', 'wifi_router.location_id', '=', 'location.id')->select('wifi_router.*', 'location.name as location_name', DB::raw("CASE WHEN wifi_router.last_online > '$onlinetime' THEN 1 ELSE 0 END as online_status"), DB::raw("CASE WHEN wifi_router.last_online < '$inactivetime' THEN 1 ELSE 0 END as inactive"))
        ->whereIn('wifi_router.owner_id', $users)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Getting Wifi routers success!',
            "data" => $routers
        ]);
    }

    public function get_router_status($user_id, $pdoa_id)
    {
        $user = auth()->user();
        if ($user->role > 3 && $user_id == 0) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        $user = Users::find($user_id);
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-180 seconds"));
        $onlinetime = $now->format('Y-m-d H:i:s');
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-7 days"));
        $inactivetime = $now->format('Y-m-d H:i:s');
        if ($user_id == 0) {
            $f_arr = Users::select("id")->where('pdoa_id','=',$pdoa_id)->get();
        } else if($user['role'] == 4) { 
            $f_arr = Users::select('id')->where('belongs_to', '=', $user_id)->get();
        } else $f_arr = [['id'=> $user_id]];

        $users =array();
        foreach ($f_arr as $key => $value) {
            $users[] = $value['id'];
        }
        $routers = Wifi_router::select(DB::raw("count(id) as router_count"), DB::raw("SUM(CASE WHEN last_online > '$onlinetime' THEN 1 ELSE 0 END) as online_status"), DB::raw("SUM(CASE WHEN last_online < '$inactivetime' THEN 1 ELSE 0 END) as inactive"))
        ->where('location_id', "<>", null)
        ->whereIn('owner_id', $users)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Getting Wifi routers success!',
            "data" => $routers
        ]);
    }

    public function get_year_top($pdoa_id) {
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-1 year"));
        $basetime = $now->format('Y-m-d H:i:s');
        $year_top_product = Wifi_router::leftJoin('wifi_router_model', 'wifi_router.model_id', '=', 'wifi_router_model.id')
                        ->select("wifi_router_model.id", "wifi_router_model.name", "wifi_router_model.images", DB::raw("SUM(CASE WHEN wifi_router.updated_at > '$basetime' THEN 1 ELSE 0 END) as current_sell"))
                        ->where('wifi_router.owner_id', "<>", null)
                        ->where('wifi_router.pdoa_id', "=", $pdoa_id)
                        ->orderBy('current_sell', 'desc')
                        ->groupBy("wifi_router_model.id")
                        ->groupBy("wifi_router_model.name")
                        ->groupBy("wifi_router_model.images")
                        ->limit(1)
                        ->get()
                        ->first();
        $total_count = 0;
        if($year_top_product) {
            $mis_inv = 0;
            $orders = Orders::where("status", "<", 3)->where('pdoa_id', '=', $pdoa_id)->get();
            foreach ($orders as $key => $order) {
                $models = explode(",", $order["model_ids"]);
                $non_processed = explode(",", $order["non_processed"]);
                foreach ($models as $idx => $model) {
                    if ($model == $year_top_product['id']) $mis_inv = (int)$mis_inv + (int)$non_processed[$idx];
                }
            }
            $total = Wifi_router::select(DB::raw('COUNT(id) as total_count'))
                            ->where('owner_id', "=", null)
                            ->where('pdoa_id', "=", $pdoa_id)
                            ->where('model_id', "=", $year_top_product['id'])
                            ->get()
                            ->first();
            if($total) $total_count = $total['total_count'];
            $cart_amount = Cart::leftJoin('users', 'cart.owner_id', '=', 'users.id')->select(DB::raw('SUM(cart.request_amount) as request_amount'))
                ->where("cart.status", "<", 3)
                ->where("users.pdoa_id", "=", $pdoa_id)
                ->where(["model_id" => $year_top_product['id']])
                ->get()->first();
            if ($cart_amount) {
                $year_top_product["left_inventory"] = $total_count - $cart_amount['request_amount'];
            } else $year_top_product["left_inventory"] = $total_count;                
            $red = $year_top_product["left_inventory"];
            $year_top_product["left_inventory"] = (int)$year_top_product["left_inventory"] - (int)$mis_inv;            
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting best sell product of this year success!',
            'data' => $year_top_product
        ]);
    }

    public function get_best_sell($pdoa_id, $period) {
        $now = date_create(date("Y-m-d H:i:s"));
        if($period == "week") $from_date_string = "-7 days";
        else $from_date_string = "-1 ".$period;
        date_add($now, date_interval_create_from_date_string($from_date_string));
        $basetime = $now->format('Y-m-d H:i:s');
        date_add($now, date_interval_create_from_date_string($from_date_string));
        $fromtime = $now->format('Y-m-d H:i:s');
        $routers = Wifi_router::leftJoin('wifi_router_model', 'wifi_router.model_id', '=', 'wifi_router_model.id')
                    ->select("wifi_router_model.id", "wifi_router_model.name", "wifi_router_model.images", DB::raw("SUM(CASE WHEN wifi_router.updated_at > '$basetime' THEN 1 ELSE 0 END) as current_sell"), DB::raw("SUM(CASE WHEN wifi_router.updated_at > '$fromtime' AND wifi_router.updated_at < '$basetime' THEN 1 ELSE 0 END) as before_sell"))
                    ->where('wifi_router.owner_id', "<>", null)
                    ->orderBy('current_sell', 'desc')
                    ->groupBy("wifi_router_model.id")
                    ->groupBy("wifi_router_model.name")
                    ->groupBy("wifi_router_model.images")
                    ->limit(5)
                    ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Getting best sell products success!',
            'data' => $routers,
        ]);
    }

    public function update(Request $request, $router_id)
    {
        $router = Wifi_router::find($router_id);
        if ($router->name != $request->input("name")) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100|unique:wifi_router',
                'mac_address' => 'string|min:2|max:100',
                'location_id' => 'integer',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
                'mac_address' => 'string|min:2|max:100',
                'location_id' => 'integer',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $location = Location::find($request->input("location_id"));

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'NoLoaction',
            ]);
        }

        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }
        $arr_update_keys["owner_id"] = $location->owner_id;
        $router->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Wifi router successfully updated.',
            'data' => $router,
        ]);
    }

    public function heartbeat($key, $secret)
    {
        $wiFiRouter = Wifi_router::select('secret', 'config_version', 'id')->where('key', $key)->where('secret', $secret)->first();
        if ($wiFiRouter) {
            $now = new DateTime(date("Y-m-d H:i:s"));
            $wiFiRouter->last_online = $now;
            $wiFiRouter->save();
            $wiFiRouter_info = array();
            $wiFiRouter_info['secret'] = $wiFiRouter->secret;
            $wiFiRouter_info['config_version'] = $wiFiRouter->config_version;

            return response()->json([
                'success' => true,
                'message' => 'Heartbeat registered Successfully',
                'data' => $wiFiRouter_info
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed'
            ], 400);
        }
    }

    public function settings($key, $secret)
    {
        //$wiFiRouter = Wifi_router::select('secret', 'location_id', 'config_version', 'id','pdoa_id')->where('key', $key)->where('secret', $secret)->first();
        $wiFiRouter = Wifi_router::select('secret', 'location_id', 'config_version', 'id','pdoa_id')->where('key', $key)->where('secret', $secret)->first();

        if ($wiFiRouter) {
            $pdoa_id = $wiFiRouter->pdoa_id;
            $pdoa  = PDOA::where('id', $pdoa_id)->first();
            $Network_setting = Network_setting::select('guestEssid', 'serverWhitelist', 'domainWhitelist')->where('pdoa_id', $pdoa_id)->first();
            $Network_setting->domain = $pdoa->domain_name;
            $Network_setting->timezone = '1';
            $Network_setting->radius_server = $_ENV['radius_server'];
            $Network_setting->radius_secret =  $_ENV['radius_secret'];
            $Network_setting->login_url = $_ENV['login_url'];
            $Network_setting->dns1 = $_ENV['dns1'];
            $Network_setting->dns2 = $_ENV['dns2'];
            /*$Network_setting->timezone = $_ENV[''];
            $Network_setting->timezone = $_ENV[''];
            $Network_setting->timezone = $_ENV[''];
            $Network_setting->timezone = $_ENV[''];
            */
            $Network_setting->update_file = '';
            $Network_setting->update_file_hash = '';

            return response()->json([
                'success' => true,
                'message' => 'Settings retrived Successfully',
                'wifirouter' => $wiFiRouter,
                'network_settings' => $Network_setting,
                'network_settings' => $Network_setting,
                'pdoa_id' => $pdoa_id
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Authorization failed',
            ], 400);
        }
    }

    public function verify_router($verification_code, $mac)
    {
        if ($verification_code == $_ENV['VERIFICATION_CODE']) {

            //$wiFiRouter = Wifi_router::select('id', 'location_id', 'key', 'secret', 'pdoa_id')->where('mac_address', $mac)->first();
            $wiFiRouter = Wifi_router::select('id', 'location_id', 'model_id', 'key', 'secret', 'pdoa_id')->where('mac_address', $mac)->first();

            if ($wiFiRouter) {
                $model = WifiRouterModel::where('id',$wiFiRouter->model_id)->first();
                $wiFiRouter->model = $model->name;
                //if($wiFiRouter->model == 'TP-Link A6')

                switch ($model->name){
                    case 'TP-Link Archer A6': $wiFiRouter->installer = 'GAzYbCGZrwkDLvMt9thy9Taf.sh';
                        break;
                    case 'TP Link EAP 225-Indoor': $wiFiRouter->installer = 'bWKM2T2Z3kpcRcJvk9KJczBk.sh';
                        break;
                    case 'TP Link EAP 225-Outdoor': $wiFiRouter->installer = 'HMASbrXk4jnvrcHdcU7HGnMn.sh';
                        break;
                    default: break;
                            
                }
                return response()->json([
                    'success' => true,
                    'message' => 'WiFi Router verified successfully',
                    'data' => $wiFiRouter
                ], 200);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization failed',
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Authorization failed',
            ], 400);
        }
    }

    public function generate_random($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
