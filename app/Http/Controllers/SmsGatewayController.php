<?php

namespace App\Http\Controllers;

use App\Models\Sms_gateway;
use Auth;
use Illuminate\Http\Request;
use Validator;

class SmsGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100|unique:sms_gateway',
            'key' => 'required|string|min:2|max:100',
            'secret' => 'string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $gateway = Sms_gateway::create([
            'name' => $request->input('name'),
            'secret' => $request->input('secret'),
            'pdoa_id' => $pdoa_id,
            'key' => $request->input('key'),
            'status' => $request->input('status'),
        ]);

        $response_gateway = Sms_gateway::where(['name' => $gateway->name])->first();

        return response()->json([
            'success' => true,
            'message' => 'SMS Gateway successfully registered',
            'data' => $response_gateway,
        ]);
    }

    public function delete($id)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $del_gateway = Sms_gateway::find($id);
        if (!$del_gateway) {
            return response()->json([
                'success' => false,
                'message' => 'NoGateway',
            ]);
        }

        $del_gateway->delete();
        return response()->json([
            'success' => true,
            'message' => 'SMS Gateway successfully deleted.',
        ]);
    }

    public function get($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $gateways = Sms_gateway::leftJoin('pdoas', 'sms_gateway.pdoa_id', '=', 'pdoas.id')
            ->select('sms_gateway.*', 'pdoas.domain_name')
            ->get();
        } else {
            $gateways = Sms_gateway::leftJoin('pdoas', 'sms_gateway.pdoa_id', '=', 'pdoas.id')
            ->select('sms_gateway.*', 'pdoas.domain_name')
            ->where('sms_gateway.pdoa_id', $pdoa_id)
            ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting SMS Gateways success!',
            "data" => $gateways,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $gateway = Sms_gateway::find($id);
        $arr_update_keys = array([]);

        if ($gateway->name != $request->input("name")) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100|unique:sms_gateway',
                'key' => 'required|string|min:2|max:100',
                'secret' => 'string|min:2|max:100',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
                'key' => 'required|string|min:2|max:100',
                'secret' => 'string|min:2|max:100',
            ]);
        }

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

        $gateway->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'SMS Gateway successfully updated.',
            'data' => $gateway,
        ]);
    }

}