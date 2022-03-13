<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use App\Models\WifiRouterModel;
use App\Models\DeviceModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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

            'name' => 'required|string|min:2|max:100|unique:wifi_router_model',
            'description' => 'string|min:2|max:100',
            'price' => 'numeric|min:1',
            // 'photo'=> 'required',
            // 'description'=> 'required',
            // 'brand'=> 'required',
            // 'model'=> 'required',
            // 'hardware_version'=> 'required',
            // 'ean'=> 'required',
            // 'package_info'=> 'required',
            // 'category'=> 'required',
            // 'shipping'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        foreach ($request->all() as $key => $value) {
            $arr_insert_keys[$key] = $value;
        }

        $wifiRouterModel = WifiRouterModel::create($arr_insert_keys);
        $response_wifiRouterModel = WifiRouterModel::where(['name' => $wifiRouterModel->name])->first();
        $id = $response_wifiRouterModel['id'];
        if ($image = $request->model_images) {
            $file_name = "WiFiRouterModel_" . $id . "_0." . $request->model_images->getClientOriginalExtension();
            $image->move(public_path('/WiFiRouter_img/'), $file_name);
            $response_wifiRouterModel->update(['images' => public_path('/WiFiRouter_img/').$file_name]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Wifi Router Model successfully registered',
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
