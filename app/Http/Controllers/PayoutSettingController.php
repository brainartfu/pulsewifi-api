<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Auth;
use Illuminate\Http\Request;
use Validator;

class PayoutSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function get($pdoa_id)
    {
        $franchises = Users::select("id as franchise_id", "firstname as franchise_firstname", "lastname as franchise_lastname", "revenue_model as franchise_rate", "belongs_to as distributor_id")
                        ->where(["pdoa_id" => $pdoa_id, "role" => 5])
                        ->get();
        foreach ($franchises as $key => $fran) {
            if ($fran["distributor_id"]) {
                $distributor = Users::find($fran["distributor_id"]);
                $fran["distributor_firstname"] = $distributor["firstname"];
                $fran["distributor_lastname"] = $distributor["lastname"];
                $fran["distributor_rate"] = $distributor["revenue_model"];
            } 
            $payout_settings[] = $fran; 
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Payout settings success!',
            "data" => $payout_settings,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'franchise_rate' => 'required|integer|min:1|max:99',
            'distributor_rate' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        Users::where(['id'=>$request->input("franchise_id")])->update(['revenue_model'=>$request->input("franchise_rate")]);
        Users::where(['id'=>$request->input("distributor_id")])->update(['revenue_model'=>$request->input("distributor_rate")]);

        return response()->json([
            'success' => true,
            'message' => 'Payout setting successfully updated.'
        ]);
    }
}