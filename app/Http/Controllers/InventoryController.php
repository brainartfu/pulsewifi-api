<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'version' => 'required',
            'category' => 'required',
            'price' => 'required',
            'shipping' => 'required',
            'photo' => 'required',
            'description' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $inven = Inventory::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'version' => $request->version,
            'category' => $request->category,
            'price' => $request->price,
            'shipping' => $request->shipping,
            'photo' => $request->photo,
            'description' => $request->description
        ]);

        if($inven){
            return response()->json([
                'success' => true,
                'message' => "device generated",
                'data' => $inven,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'device creation failed',
                'validated' => $validated
            ], 400);
        }
    }

    /**
     * get device list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $device = Inventory::leftJoin('category', 'inventory.category', '=', 'category.id')
                    ->select("inventory.*", "category.name as cName")
                    ->limit(10)
                    ->latest('inventory.created_at')
                    ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Device list Success!',
            'data' => $device
        ]);     
    }

    /**
     * get category and model list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_category()
    {
        $category = Category::latest('category.created_at')->get();
        $model = DeviceModel::latest('device_model.created_at')->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Device list Success!',
            'data' => [
                'category'=>$category,
                'model'=>$model
            ]
        ]);     
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
     * Create the new category.
     *
     * @param  \App\Models\Category
     * @return \Illuminate\Http\Response
     */
    public function new_category(Request $request)
    {
        if ($request->name) {
            $category = Category::create(['name'=>$request->name]);
            if ($category) {
                return response()->json([
                    'success'=> true,
                    'message'=> 'New Category Create Success!',
                    'data'=> $category
                ]);
            } else {
                return response()->json([
                    'success'=> false,
                    'message'=> 'New Category Create Failure!'
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
