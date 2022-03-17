<?php

namespace App\Http\Controllers;
use App\Models\Users;
use App\Models\Internet_plans;
use App\Models\Payment_setting;

use Auth;
use Illuminate\Http\Request;
use Validator;

class InternetPlansController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['get_plans','get_plan_list','user_active_session']]);
    }

    public function add(Request $request, $pdoa_id)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'description' => 'string|min:2|max:100',
            'price' => 'required|integer|min:0',
            'validity' => 'required|integer|min:0',
            'bandwidth' => 'required|integer|min:0',
            'data_limit' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $plan = Internet_plans::create([
            'name' => $request->input('name'),
            'pdoa_id' => $pdoa_id,
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'validity' => $request->input('validity'),
            'bandwidth' => $request->input('bandwidth'),
            'data_limit' => $request->input('data_limit'),
        ]);
        $response_plan = Internet_plans::where(['name' => $plan->id])->get();
        return response()->json([
            'success' => true,
            'message' => 'Internet Plan successfully registered',
            'data' => $response_plan,
            'pdoa' => $pdoa_id
        ]);
    }

    public function user_active_session(Request $request, $pdoa_id){
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'message' => 'InternetPlan successfully deleted.',
            'data' => $user
        ]);
    }

    public function delete($plan_id)
    {
        $user = auth()->user();
        $del_plan = Internet_plans::find($plan_id);
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        if (!$del_plan) {
            return response()->json([
                'success' => false,
                'message' => 'NoPlan',
            ]);
        }
        $del_plan->delete();
        return response()->json([
            'success' => true,
            'message' => 'InternetPlan successfully deleted.',
        ]);
    }

    public function get_plans($pdoa_id)
    {
        $user = auth()->user();
        if(! $user){
            $plans = Internet_plans::leftJoin('pdoas', 'internet_plans.pdoa_id', '=', 'pdoas.id')
            ->select('internet_plans.*', 'pdoas.domain_name')
            ->where('pdoa_id', $pdoa_id)->get();
        }else{
            if ($user->role == 1) {
                $plans = Internet_plans::leftJoin('pdoas', 'internet_plans.pdoa_id', '=', 'pdoas.id')
                ->select('internet_plans.*', 'pdoas.domain_name')->get();
            } else {
                $plans = Internet_plans::leftJoin('pdoas', 'internet_plans.pdoa_id', '=', 'pdoas.id')
                ->select('internet_plans.*', 'pdoas.domain_name')->where('pdoa_id', $pdoa_id)->get();
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Getting Internet Plans success!',
            "data" => $plans,
            'pdoa' => $pdoa_id
        ]);
    }

    public function get_plan_list($pdoa_id)
    {
        $plans = Internet_plans::where('pdoa_id', $pdoa_id)->get();
        $payment_info = Payment_setting::where('pdoa_id', $pdoa_id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Getting Internet Plans success!',
            "data" => $plans,
            'pdoa' => $pdoa_id,
            'payment_info' => $payment_info
        ]);
       
    }

    public function updatePlan(Request $request, $plan_id)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $plan = Internet_plans::find($plan_id);
        $arr_update_keys = array([]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'description' => 'string|min:2|max:100',
            'price' => 'required|integer|min:0',
            'validity' => 'required|integer|min:0',
            'bandwidth' => 'required|integer|min:0',
            'data_limit' => 'required|integer|min:0',
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

        $plan->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Internet Plan successfully updated.',
            'data' => $plan,
        ]);
    }
}