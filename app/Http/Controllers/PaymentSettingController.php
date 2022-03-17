<?php

namespace App\Http\Controllers;

use App\Models\Payment_setting;
use Auth;
use Illuminate\Http\Request;
use Validator;

class PaymentSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request, $pdoa_id)
    {
        $user = auth()->user();
        if ($user->role != 1) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100|unique:payment_setting',
            'key' => 'required|string|min:2|max:100',
            'secret' => 'required|string|min:2|max:100',
            'type' => 'integer',
            'callback_url' => 'required|string|min:2|max:100',
            'status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $payment_setting = Payment_setting::create([
            'name' => $request->input('name'),
            'key' => $request->input('key'),
            'secret' => $request->input('secret'),
            'type' => $request->input('type'),
            'pdoa_id' => $pdoa_id,
            'callback_url' => $request->input('callback_url'),
            'status' => $request->input('status'),
        ]);
        $response_payment_setting = Payment_setting::where(['name' => $payment_setting->name])->first();
        return response()->json([
            'success' => true,
            'message' => 'Payment Setting successfully added',
            'data' => $response_payment_setting,
        ]);
    }

    public function delete($id)
    {
        $user = auth()->user();
        if ($user->role != 1) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $del_setting = Payment_setting::find($id);
        if (!$del_setting) {
            return response()->json([
                'success' => false,
                'message' => 'NoPaymentSettomg',
            ]);
        }

        $del_setting->delete();
        return response()->json([
            'success' => true,
            'message' => 'Payment Setting successfully deleted.',
        ]);
    }

    public function get($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $payment_settings = Payment_setting::leftJoin('pdoas', 'payment_setting.pdoa_id', '=', 'pdoas.id')
            ->select('payment_setting.*', 'pdoas.domain_name')
            ->get();
        } else {
            $payment_settings = Payment_setting::leftJoin('pdoas', 'payment_setting.pdoa_id', '=', 'pdoas.id')
            ->select('payment_setting.*', 'pdoas.domain_name')
            ->where('payment_setting.pdoa_id', $pdoa_id)
            ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Payment settings success!',
            "data" => $payment_settings,
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

        $update_setting = Payment_setting::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'key' => 'required|string|min:2|max:100',
            'secret' => 'required|string|min:2|max:100',
            'type' => 'integer',
            'callback_url' => 'required|string|min:2|max:100',
            'status' => 'required|integer',
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

        $update_setting->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Payment setting successfully updated.',
            'data' => $update_setting,
        ]);
    }
}