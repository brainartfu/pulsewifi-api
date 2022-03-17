<?php

namespace App\Http\Controllers;

use App\Models\WiFiOrder;
use App\Models\Internet_plans;
use App\Models\Location;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log;
// use DB;

class WiFiOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function process_payment(Request $request)
    {
        $data = [ 
            'order_id' => $request->order_id,
            'payment_id' => $request->razorpay_payment_id,
            'amount' => $request->totalAmount,
            'product_id' => $request->product_id,
        ];
        // you can write your database insertation code here
        // after successfully insert transaction in database, pass the response accordingly
        $order = WiFiOrder::find($request->order_id);
        
        if($order){
            $order->payment_reference = $request->razorpay_payment_id;
            
            $order->status = 1;
            $order->save();
            $phone = $order->phone;
            $pdoa = $order->pdoa;
            $plan_id = $order->internet_plan_id;
            Log::info('planid:: '.$plan_id);

            // Update RADIUS DB as per payment status  
            $plan_info = Internet_plans::find($plan_id);
            $logout_time = time()+$plan_info->validity*60;
            $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->where('username',$phone)->where('pdoa',$order->pdoa)->first();
            $now = date("Y-m-d H:i:s");
            $bandwidth = $plan_info->bandwidth;
            Log::info('bandwidth:: '.$bandwidth);

            if(! $radius_wifiuser){
                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')->insert([
                    'username' => $phone,
                    'value' => $phone,
                    'pdoa' => $pdoa, 
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'bandwidth' => $bandwidth,
                    'logout_time' => $logout_time,
                    'download_limit' => $plan_info->data_limit,
                    'plan_start_date' => $now
                ]);
            } else {
                $radius_wifiuser = DB::connection('mysql2')->table('radcheck')
                ->where('username', $order->phone)
                ->where('pdoa', $order->pdoa)
                ->update([
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'bandwidth' => $bandwidth,
                    'logout_time' => $logout_time,
                    'download_limit' => $plan_info->data_limit,
                    'plan_start_date' => $now
                ]);
            }

            Payments::create([
                'wifi_user_id' => $order['wifi_user_account_id'],
                'wifi_user_phone' => $order['phone'],
                'amount' => $request->totalAmount,
                'payment_method' => "razorpay",
                'location_id' => $order['location_id'],
                'order_id' => $order['id'],
                'payment_status' => 1,
                'payment_details' => "paid_wifi",
                'pdoa_id' => $pdoa,
            ]);

            $arr = array('msg' => 'Payment successfully credited', 'status' => true);  
            
            return response()->json([
                'success' => true,
                'message' => "WiFi Order Processed",
                'data' => $order,
                'request' => $request->all(),
                'bandwidth' => $bandwidth

            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'WiFi Order creation failed',
                'validated' => $request->all()
            ], 400);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wifi_user_id' => 'required',
            'phone' => 'required|string|min:10|max:10',
            'internet_plan_id' => 'required',
            //'amount' => 'required|integer',
            'location_id' => 'integer|nullable',
            'pdoa' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $validated = $validator->validated();
        $validated['location_id'] = 10;
        //$validated['plan']
        $plan = Internet_plans::find($request->internet_plan_id);
        $validated['amount'] = $plan->price;
        $validated['pdoa_id'] = $request->pdoa;
        $validated['wifi_user_account_id'] = $request->wifi_user_id;
         
        if(!empty($request->location_id)){
            $location = Location::find($request->location_id);
            if($location){
                $validated['franchise_id'] = $location['owner_id'];
            }
        }
        $wifi_order = WiFiOrder::create($validated);

        if($wifi_order){
            return response()->json([
                'success' => true,
                'message' => "WiFi Order generated",
                'data' => $wifi_order,
                //'request' => $request->all()
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'WiFi Order creation failed',
                'validated' => $validated
            ], 400);
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
     * @param  \App\Models\WiFiOrder  $wiFiOrder
     * @return \Illuminate\Http\Response
     */
    public function show(WiFiOrder $wiFiOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WiFiOrder  $wiFiOrder
     * @return \Illuminate\Http\Response
     */
    public function info($id)
    {
        $order = WiFiOrder::where('payment_reference',$id)->first();

        if($order){
            return response()->json([
                'success' => true,
                'message' => "WiFi Order details Retrived",
                'data' => $order,
                //'request' => $request->all()
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'WiFi Order details retrival failed',
                
            ], 400);
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
        $now = date_create(date("Y-m-d H:i:s"));
        if($period == "week") $from_date_string = "-7 days";
        else $from_date_string = "-1 ".$period;
        date_add($now, date_interval_create_from_date_string($from_date_string));
        $basetime = $now->format('Y-m-d H:i:s');
        $users = WiFiOrder::leftJoin('location', 'wi_fi_orders.location_id', '=', 'location.id')
                    ->select("wi_fi_orders.phone as user_phone", "location.name as location_name", DB::raw("SUM(wi_fi_orders.amount) as total_payment"))
                    ->where('wi_fi_orders.pdoa_id', "=", $pdoa_id)
                    ->where('wi_fi_orders.updated_at', ">=", $basetime)
                    ->orderBy('total_payment', 'desc')
                    ->groupBy('user_phone')
                    ->groupBy('location_name')
                    ->limit(5)
                    ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting best wifi_users success!',
            'data' => $users
        ]);
    }
        
    public function get_best_locations($pdoa_id, $period) {
        $now = date_create(date("Y-m-d H:i:s"));
        if($period == "week") $from_date_string = "-7 days";
        else $from_date_string = "-1 ".$period;
        date_add($now, date_interval_create_from_date_string($from_date_string));
        $basetime = $now->format('Y-m-d H:i:s');
        $locations = WiFiOrder::leftJoin('location', 'wi_fi_orders.location_id', '=', 'location.id')
                    ->select("location.name as location_name", DB::raw("SUM(wi_fi_orders.amount) as total_payment"))
                    ->where('wi_fi_orders.pdoa_id', "=", $pdoa_id)
                    ->where('wi_fi_orders.updated_at', ">=", $basetime)
                    ->orderBy('total_payment', 'desc')
                    ->groupBy('location_name')
                    ->limit(10)
                    ->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting best locations success!',
            'data' => $locations
        ]);
    }

}
