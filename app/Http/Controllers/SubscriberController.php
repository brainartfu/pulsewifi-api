<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function get_all_subscribers()
    {
        $subscribers = Subscriber::get();

        return response()->json([
            'success' => true,
            'message' => 'Getting subscribers success!',
            "data" => $subscribers,
        ]);
    }

    public function update(Request $request, $subscriber_id)
    {
        $subscriber = Subscriber::find($subscriber_id);
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|min:2|max:100',
        //     'owner_id' => 'required|integer|min:1',
        //     'address' => 'string|min:2|max:100',
        //     'city' => 'string|min:2|max:100',
        //     'state' => 'required|string|min:2|max:100',
        //     'country' => 'required|string|min:2|max:100',
        //     'postal_code' => 'required|integer|min:100000|max:999999',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'ValidationError',
        //         'data' => $validator->errors(),
        //     ]);
        // }
        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }
        $subscriber->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Subscriber successfully updated.',
            'data' => $subscriber,
        ]);
    }

    public function add(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|min:2|max:100',
        //     'owner_id' => 'required|integer',
        //     'address' => 'required|string|min:2|max:100',
        //     'city' => 'required|string|min:2|max:100',
        //     'state' => 'required|string|min:2|max:100',
        //     'country' => 'required|string|min:2|max:100',
        //     'postal_code' => 'required|integer|min:100000|max:999999',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'ValidationError',
        //         'data' => $validator->errors(),
        //     ]);
        // }

        $subscriber = Subscriber::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'active_package' => $request->input('active_package'),
            'package_status' => $request->input('package_status'),
            'data_consume' => $request->input('data_consume'),
            'duration' => $request->input('duration'),
            'connected_devices' => $request->input('connected_devices'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'country' => $request->input('country'),
            'postal_code' => $request->input('postal_code'),
            'expired_at' => $request->input('expired_at'),
            'last_recharged' => $request->input('last_recharged'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscriber successfully registered',
            'data' => $subscriber
        ]);
    }
}
