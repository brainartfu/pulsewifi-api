<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use App\Models\WifiRouterModel;
use App\Models\DeviceModel;
use App\Models\WifiBrand;
use App\Models\Wifi_router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;
use Validator;
use Illuminate\Support\Facades\Log;
// use DB;

class InventoryController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->role > 3) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100'.$request->id?'':'|unique:wifi_router_model',
            'description' => 'string|min:2|max:100',
            'price' => 'numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        foreach ($request->all() as $key => $value) {
            if ($key !== 'model_images') {
                $arr_insert_keys[$key] = $value;
            }
        }
        if ($request->id) {
            $wifiRouterModel = WifiRouterModel::where('id', '=', $request->id)->update($arr_insert_keys);
            $response_wifiRouterModel = WifiRouterModel::where('id', '=', $request->id)->first();
        } else {
            $wifiRouterModel = WifiRouterModel::create($arr_insert_keys);
            $response_wifiRouterModel = WifiRouterModel::where(['name' => $wifiRouterModel->name])->first();
        }
        $id = $response_wifiRouterModel['id'];
        if ($image = $request->model_images) {
            $file_name = "WiFiRouterModel_" . $id . "_0." . $request->model_images->getClientOriginalExtension();
            $image->move(public_path('/WiFiRouter_img/'), $file_name);
            $response_wifiRouterModel->update(['images' => 'WiFiRouter_img/'.$file_name]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Wifi Router Model successfully '.($request->id?'updated.':'registered.'),
            'data' => $response_wifiRouterModel,
        ]);
    }

    /**
     * get device list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getItems(Request $request)
    {
        $device = WifiRouterModel::leftJoin('category', 'wifi_router_model.category', '=', 'category.id')
            ->leftJoin('wi_fi_brand', 'wifi_router_model.brand', '=', 'wi_fi_brand.id')
            ->select("wifi_router_model.*", "category.name as cName", "wi_fi_brand.logo as brand_logo")
            ->latest('wifi_router_model.created_at')
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Device list Success!',
            'data' => $device
        ]);     
    }
    /**
     * get device list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_item_by_id(Request $request)
    {
        $device = WifiRouterModel::where('wifi_router_model.id', '=', $request->id)
            ->leftJoin('category', 'wifi_router_model.category', '=', 'category.id')
            ->select("wifi_router_model.*", "category.name as cName")
            ->latest('wifi_router_model.created_at')
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Device list Success!',
            'data' => $device
        ]);     
    }


    /**
     * delete item .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_item(Request $request)
    {
        $item = WifiRouterModel::whereIn('id', $request->ids)->delete();
        if ($item) {
            return response()->json([
                'success' => true,
                'message' => 'Item is deleted!',
                'data' => $item
            ]);     
        } else {
            return response()->json([
                'success' => false,
                'message' => 'item Deleting is failure!'
            ]);
        }
    }

    /**
     * get category  list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_category()
    {
        $category = Category::latest('category.created_at')->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Category list Success!',
            'data' => $category
        ]);     
    }
    /**
     * get category  list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_category_model()
    {
        $category = Category::latest('category.created_at')->get();
        $model = WifiRouterModel::select('id', 'name')->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Category list Success!',
            'data' => array('category'=>$category, 'model'=>$model)
        ]);     
    }

    /**
     * get category  list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_category_brand_model()
    {
        $category = Category::select('id', 'name')->get();
        $model = WifiRouterModel::select('id', 'name')->get();
        $brand = WifiBrand::select('id', 'name')->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Category list Success!',
            'data' => array('category'=>$category, 'model'=>$model, 'brand'=> $brand)
        ]);     
    }

    /**
     * delete category .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_category(Request $request)
    {
        $category = Category::whereIn('id', $request->ids)->delete();
        if ($category) {
            return response()->json([
                'success' => true,
                'message' => 'Category is deleted!',
                'data' => $category
            ]);     
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Category Deleting is failure!'
            ]);
        }
    }


    /**
     * Create the new category.
     *
     * @param  \App\Models\Category
     * @return \Illuminate\Http\Response
     */
    public function new_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'unit' => 'required',
            'tax_preference' => 'required',
            'hsn_code' => 'required',
            'tax_rate' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $arr = array(
            'name' => $request->name,
            'unit' => $request->unit,
            'tax_preference' => $request->tax_preference,
            'hsn_code' => $request->hsn_code,
            'tax_rate' => $request->tax_rate,
            'status' => $request->status
        );
        if ($request->id == 'new') {
            $category = Category::create($arr);
            $message = "New Category Create Success!";
            $failmessage = "New Category Create Failure!";
        } else {
            $category = Category::find($request->id)->update($arr);
            $message = "Category Update Success!";
            $failmessage = "Category Update Failure!";
        }
        if ($category) {
            return response()->json([
                'success'=> true,
                'message'=> $message,
                'data'=> $category
            ]);
        } else {
            return response()->json([
                'success'=> false,
                'message'=> $failmessage
            ]);
        }
        
    }
    /**
     * Create the new brand.
     *
     * @param  \App\Models\Brand
     * @return \Illuminate\Http\Response
     */
    public function add_brand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required', //more parameter
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $arr = array(
            'name' => $request->name
        );

        if ($request->id == 'new') {
            $brand_id = WifiBrand::insertGetId($arr);
            $message = "New Brand Create Success!";
            $failmessage = "New Brand Create Failure!";
        } else {
            $updatebrand = WifiBrand::find($request->id)->update($arr);
            $brand_id = $request->id;
            $message = "Brand Update Success!";
            $failmessage = "Brand Update Failure!";
        }
        $brand = WifiBrand::find($brand_id);
        if ($image = $request->brand_logo) {
            $file_name = "BrandLogo_" . $brand_id . "." . $request->brand_logo->getClientOriginalExtension();
            $image->move(public_path('/Brand_logo/'), $file_name);
            $brand->update(['logo' => 'Brand_logo/'.$file_name]);
        }

        if ($brand) {
            return response()->json([
                'success'=> true,
                'message'=> $message,
                'data'=> $brand
            ]);
        } else {
            return response()->json([
                'success'=> false,
                'message'=> $failmessage
            ]);
        }
        
    }
    public function delete_brand(Request $request)
    {
        $brand = WifiBrand::where('id', '=', $request->id)->first();
        var_dump($brand->logo);
        if (\Storage::exists('public/'.$brand->logo)) {
            \Storage::delete($brand->logo);
        }
        $state = $brand->delete();
        if ($state) {
            return response()->json([
                'success'=> true,
                'message'=> "Brand deleted successfully."
            ]);
        } else {
            return response()->json([
                'success'=> false,
                'message'=> "Brand deleted failure."
            ]);
        }
        
    }

    /**
     * Create the new stock.
     *
     * @param  \App\Models\WifiRouterModel
     * @return \Illuminate\Http\Response
     */
    public function new_stock(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100'.($request->id?'':'|unique:wifi_router'),
            // 'mac_address' => 'required|string|min:17|max:18|unique:wifi_router',
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

        $data = array(
            'name' => $request->input('name'),
            'mac_address' => $request->input('mac_address'),
            'model_id' => $request->input('model_id'),
            'pdoa_id' => 'pulse_1_2022-05-24_08-41-34',
            'category' => $request->input('category'),
            'brand' => $request->input('brand'),
            'serial_num' => $request->input('serial'),
            'wlan0' => $request->input('wlan0'),
            'wlan1' => $request->input('wlan1'),
            'configure' => $request->input('configure'),
            'status' => $request->input('status'),
            'pdoa_id'=>$user->pdoa_id,
            'owner_id'=>$user->id
        );
        if ($request->input('id')) {
            $router = Wifi_router::where('id', '=', $request->input('id'))->update($data);
            if ($router) {
                 return response()->json([
                    'success' => true,
                    'message' => 'Product successfully updated',
                ]);
            }
        } else {
            $router = Wifi_router::create($data);

            $router->secret = $secret;
            $router->key = $key;

            $router->config_version = 0;
            $router->save();
            return response()->json([
                'success' => true,
                'message' => 'Product successfully registered',
            ]);

        }
        return response()->json([
            'success' => false,
            'message' => 'Failure, try again.'
        ]);
    }

    /**
     * Get Stocks.
     *
     * @param  \App\Models\Wifi_router
     * @return \Illuminate\Http\Response
     */
    public function get_stock(Request $request) {
        $stocks = Wifi_router::leftJoin('category', 'wifi_router.category', '=', 'category.id')
        ->leftJoin('wifi_router_model', 'wifi_router.model_id', '=', 'wifi_router_model.id')
        ->leftJoin('wi_fi_brand', 'wifi_router.brand', '=', 'wi_fi_brand.id')
        ->leftJoin('users', 'wifi_router.owner_id', '=', 'users.id')
        ->select('wifi_router.*', 'wifi_router_model.name as model_name', 'wi_fi_brand.name as brand_name', 'users.username as user_name', 'category.name as category_name')
        ->get();
        return response()->json([
            'success' => true,
            'message'=> 'Getting Stocks success.',
            'data'=> $stocks
        ]);
    }
    public function delete_stock(Request $request) {
        $brand = Wifi_router::whereIn('id', $request->ids)->delete();
        if ($brand) {
            return response()->json([
                'success'=> true,
                'message'=> "Stock deleted successfully."
            ]);
        } else {
            return response()->json([
                'success'=> false,
                'message'=> "Stock deleted failure."
            ]);
        }
    }
    /**
     * Display the specified device.
     *
     * @param  \App\Models\Inventory 
     * @return \Illuminate\Http\Response
     */
    public function get_by_id(Request $request)
    {
        $device = Inventory::where('id', '=', $request->id)->get();
        return response()->json([
            'success'=> true,
            'message'=> 'Getting Device by id',
            'data'=>$device
        ]);
    }
    /**
     * Create the new model.
     *
     * @param  \App\Models\Model
     * @return \Illuminate\Http\Response
     */
    public function new_model(Request $request)
    {
        if ($request->name) {
            $model = DeviceModel::create(['name'=>$request->name]);
            if ($model) {
                return response()->json([
                    'success'=> true,
                    'message'=> 'New Model Create Success!',
                    'data'=> $model
                ]);
            } else {
                return response()->json([
                    'success'=> false,
                    'message'=> 'New Model Create Failure!'
                ]);
            }
        } else {
            return response()->json([
                'success'=> false,
                'message'=> 'Name required!'
            ]);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WiFiOrder  $wiFiOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WiFiOrder $wiFiOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WiFiOrder  $wiFiOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(WiFiOrder $wiFiOrder)
    {
        //
    }

    public function get_best_users($pdoa_id, $period) {
        // $now = date_create(date("Y-m-d H:i:s"));
        // if($period == "week") $from_date_string = "-7 days";
        // else $from_date_string = "-1 ".$period;
        // date_add($now, date_interval_create_from_date_string($from_date_string));
        // $basetime = $now->format('Y-m-d H:i:s');
        // $users = WiFiOrder::leftJoin('location', 'wi_fi_orders.location_id', '=', 'location.id')
        //             ->select("wi_fi_orders.phone as user_phone", "location.name as location_name", DB::raw("SUM(wi_fi_orders.amount) as total_payment"))
        //             ->where('wi_fi_orders.pdoa_id', "=", $pdoa_id)
        //             ->where('wi_fi_orders.updated_at', ">=", $basetime)
        //             ->orderBy('total_payment', 'desc')
        //             ->groupBy('user_phone')
        //             ->groupBy('location_name')
        //             ->limit(5)
        //             ->get();
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Getting best wifi_users success!',
        //     'data' => $users
        // ]);
    }
        
    public function get_best_locations($pdoa_id, $period) {
        // $now = date_create(date("Y-m-d H:i:s"));
        // if($period == "week") $from_date_string = "-7 days";
        // else $from_date_string = "-1 ".$period;
        // date_add($now, date_interval_create_from_date_string($from_date_string));
        // $basetime = $now->format('Y-m-d H:i:s');
        // $locations = WiFiOrder::leftJoin('location', 'wi_fi_orders.location_id', '=', 'location.id')
        //             ->select("location.name as location_name", DB::raw("SUM(wi_fi_orders.amount) as total_payment"))
        //             ->where('wi_fi_orders.pdoa_id', "=", $pdoa_id)
        //             ->where('wi_fi_orders.updated_at', ">=", $basetime)
        //             ->orderBy('total_payment', 'desc')
        //             ->groupBy('location_name')
        //             ->limit(10)
        //             ->get();
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Getting best locations success!',
        //     'data' => $locations
        // ]);
    }

}
