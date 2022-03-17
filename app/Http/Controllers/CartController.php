<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Users;
use App\Models\Orders;
use App\Models\Wifi_router;
use App\Models\WifiRouterModel;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['add_cart', 'update_cart', 'cancel_cart']]);
    }

    public function add_cart($user_id, $model_id, $amount)
    {
        if (!in_array($model_id, ["distributor_fee", "franchise_fee", "pdoa_license_price", "pdoa_setup_fee"])) {
            $product = Wifi_router::select(DB::raw('SUM(CASE WHEN wifi_router.status=0 THEN 1 ELSE 0 END) as count'))
                ->where('status', "=", 0)
                ->where('model_id', $model_id)
                ->get();

            $count = 0;
            if(count($product) > 0) $count = $product[0]['count'];
            
            $mis_inv = 0;
            $orders = Orders::where("status", "<", 3)->get();
            foreach ($orders as $key => $order) {
                $models = explode(",", $order["model_ids"]);
                $non_processed = explode(",", $order["non_processed"]);
                foreach ($models as $idx => $model) {
                    if ($model == $model_id) $mis_inv = (int)$mis_inv + (int)$non_processed[$idx];
                }
            }
            
            $cart_amount = Cart::select(DB::raw('SUM(request_amount) as request_amount'))
                ->where("status", "=", 0)
                ->where(["model_id" => $model_id])
                ->get()->first();
            if ($cart_amount) {
                $count = $count - $cart_amount['request_amount'];
            }               
            
            $count = (int)$count - (int)$mis_inv;

            if($count < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock is not enough to buy!',
                    'data' => $count
                ]);
            }
        }

        $cart = Cart::find("cart_".$user_id."_".$model_id);
        if ($cart) {
            $amount = $cart["request_amount"] + $amount;
            $cart->update(["request_amount" => $amount]);
        } else {
            if (in_array($model_id, ["distributor_fee", "franchise_fee", "pdoa_license_price", "pdoa_setup_fee"])) {
                Cart::create(["id" => "cart_".$user_id."_".$model_id,
                            "owner_id" => $user_id,
                            "description" => $model_id,
                            "request_amount" => $amount,
                            "status" => 0
                ]);
            } else  {
                Cart::create(["id" => "cart_".$user_id."_".$model_id,
                            "owner_id" => $user_id,
                            "model_id" => $model_id,
                            "request_amount" => $amount,
                            "status" => 0
                ]);
            }
        }

        $user = Users::find($user_id);
        if (!$user["email_verified"] || !$user["enabled"] || !$user["lead_process"]) return Self::get_cart($user_id);
        else  return response()->json([
                    'success' => true,
                    'message' => 'Adding Product to cart success!'
                ]);
    }

    public function get_cart_amount($user_id) {
        $counts = Cart::select(DB::raw('count(id) as model_count'), DB::raw('SUM(request_amount) as request_amount'))
                    ->where(["owner_id" => $user_id, "status" => 0])->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting cart amount success!',
            'data' => $counts[0]
        ]);
    }

    public function get_cart($user_id)
    {
        $carts = Cart::leftJoin('wifi_router_model', 'cart.model_id', '=', 'wifi_router_model.id')
                        ->select( "cart.id", "cart.model_id", "cart.request_amount", "wifi_router_model.name", "wifi_router_model.description", "wifi_router_model.images", "wifi_router_model.price")
                        ->where("cart.owner_id", "=", $user_id)
                        ->where("cart.status", "=", 0)
                        ->groupBy("cart.model_id", "cart.id", "cart.request_amount", "wifi_router_model.name", "wifi_router_model.description", "wifi_router_model.images", "wifi_router_model.price")
                        ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting cart success!',
            'data' => $carts
        ]);
    }

    public function update_cart($cart_id, $amount)
    {
        $cart = Cart::find($cart_id);
        $model_id = $cart['model_id'];
        if ($model_id && $amount && !in_array($model_id, ["distributor_fee", "franchise_fee", "pdoa_license_price"])) {
            $product = Wifi_router::select(DB::raw('SUM(CASE WHEN wifi_router.status=0 THEN 1 ELSE 0 END) as count'))
                ->where('status', "=", 0)
                ->where('model_id', $model_id)
                ->get();

            $count = 0;
            if(count($product) > 0) $count = $product[0]['count'];
            
            $mis_inv = 0;
            $orders = Orders::where("status", "<", 3)->get();
            foreach ($orders as $key => $order) {
                $models = explode(",", $order["model_ids"]);
                $non_processed = explode(",", $order["non_processed"]);
                foreach ($models as $idx => $model) {
                    if ($model == $model_id) $mis_inv = (int)$mis_inv + (int)$non_processed[$idx];
                }
            }
            
            $cart_amount = Cart::select(DB::raw('SUM(request_amount) as request_amount'))
                ->where("status", "=", 0)
                ->where(["model_id" => $model_id])
                ->get()->first();
            if ($cart_amount) {
                $count = $count - $cart_amount['request_amount'];
            }               
            
            $count = (int)$count - (int)$mis_inv;

            if($count < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock is not enough to buy!',
                    'data' => $count
                ]);
            }
        }

        $user_id = $cart["owner_id"];

        if ($amount) {
            $cart->update(["request_amount" => $amount]);
            return Self::get_cart($user_id);
        } else  {
            return Self::cancel_cart($cart_id);
        }
    }

    public function cancel_cart($cart_id)
    {
        $cart = Cart::find($cart_id);
        $cart->delete();
        
        $user_id = $cart["owner_id"];
        return Self::get_cart($user_id);
    }

}