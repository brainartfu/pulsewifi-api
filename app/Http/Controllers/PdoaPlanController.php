<?php

namespace App\Http\Controllers;

use App\Models\Pdoa_plan;
use Illuminate\Http\Request;
use Validator;

class PdoaPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required|string|min:2|max:100|unique:pdoa_plan',
            'price' => 'required|integer',
            'max_wifi_router_count' => 'required|integer|min:10|max:9999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        foreach ($request->all() as $key => $value) {
            $arr_create_keys[$key] = $value;
        }

        $pdoa_plan = Pdoa_plan::create($arr_create_keys);

        return response()->json([
            'success' => true,
            'message' => 'PDOA Plan successfully registered',
            'data' => $pdoa_plan,
        ]);
    }

    public function delete($plan_id)
    {
        $del_plan = Pdoa_plan::find($plan_id);
        if (!$del_plan) {
            return response()->json([
                'success' => false,
                'message' => 'No PDOA Plan',
            ]);
        }

        $del_plan->delete();
        return response()->json([
            'success' => true,
            'message' => 'PDOA Plan successfully deleted.',
        ]);
    }

    public function get_pdoa_plans()
    {
        $pdoa_plans = Pdoa_plan::get();
        return response()->json([
            'success' => true,
            'message' => 'Getting locations success!',
            "data" => $pdoa_plans,
        ]);
    }

    public function update(Request $request, $plan_id)
    {
        $pdoa_plan = Pdoa_plan::find($plan_id);

        if ($request->input("plan_name") && $pdoa_plan->plan_name != $request->input("plan_name")) {
            $valid_name = 'required|string|min:2|max:100|unique:pdoa_plan';
        } else {
            $valid_name = 'required|string|min:2|max:100';
        }

        $validator = Validator::make($request->all(), [
            'plan_name' => $valid_name,
            'price' => 'required|integer',
            'max_wifi_router_count' => 'required|integer|min:10|max:9999',
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

        $pdoa_plan->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'PDOA Plan successfully updated.',
            'data' => $pdoa_plan,
        ]);
    }

}