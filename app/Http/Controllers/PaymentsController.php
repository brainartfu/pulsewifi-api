<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Auth;
use DB;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function get($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $payments = Payments::leftJoin('wifi_user_accounts', 'payments.wifi_user_id', '=', 'wifi_user_accounts.id')
                ->leftJoin('location', 'payments.location_id', '=', 'location.id')
                ->select('payments.*', DB::raw("CONCAT(wifi_user_accounts.first_name, ' ', wifi_user_accounts.last_name) as wifi_user_name"), 'wifi_user_accounts.phone', 'location.name as location_name')
                ->get();
        } else {
            $payments = Payments::leftJoin('wifi_user_accounts', 'payments.wifi_user_id', '=', 'wifi_user_accounts.id')
                ->leftJoin('location', 'payments.location_id', '=', 'location.id')
                ->where('payments.pdoa_id', $pdoa_id)
                ->select('payments.*',  DB::raw("CONCAT(wifi_user_accounts.first_name, ' ', wifi_user_accounts.last_name) as wifi_user_name"), 'wifi_user_accounts.phone', 'location.name as location_name')
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Payments success!',
            "data" => $payments,
        ]);
    }

}