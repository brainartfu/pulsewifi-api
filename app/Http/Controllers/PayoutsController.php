<?php

namespace App\Http\Controllers;

use App\Models\Payouts;
use App\Models\Internet_plans;
use App\Models\Users;
use App\Models\Location;
use Auth;
use DB;

class PayoutsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['log_payout']]);
    }

    public function get($pdoa_id)
    {
        $user = auth()->user();

        switch ($user->role) {
            case 1:
                $payouts = Payouts::leftJoin('wifi_user_accounts', 'payouts.wifi_user_id', '=', 'wifi_user_accounts.id')
                    ->leftJoin("users as franchise", "payouts.franchise_id", "=", "franchise.id")
                    ->leftJoin("users as distributor", "payouts.distributor_id", "=", "franchise.id")
                    ->select("payouts.id", DB::raw("CONCAT(wifi_user_accounts.first_name, ' ', wifi_user_accounts.last_name) as wifi_username"), "wifi_user_accounts.phone as wifi_user_phone", "payouts.amount", "payouts.tax_amount", "franchise.username as franchise_name", "payouts.franchise_amount", "distributor.username as distributor_name", "payouts.distributor_amount", "payouts.payout_status", "payouts.payment_method", "payouts.payout_details", "payouts.created_at")
                    ->orderBy("created_at")->orderBy("franchise_id")->orderBy("distributor_id")->get();
                break;
            case 2:
                $payouts = Payouts::leftJoin("wifi_user_accounts", "payouts.wifi_user_id", "=", "wifi_user_accounts.id")
                    ->leftJoin("users as franchise", "payouts.franchise_id", "=", "franchise.id")
                    ->leftJoin("users as distributor", "payouts.distributor_id", "=", "franchise.id")
                    ->select("payouts.id", DB::raw("CONCAT(wifi_user_accounts.first_name, ' ', wifi_user_accounts.last_name) as wifi_username"), "wifi_user_accounts.phone as wifi_user_phone", "payouts.amount", "payouts.tax_amount", "franchise.username as franchise_name", "payouts.franchise_amount", "distributor.username as distributor_name", "payouts.distributor_amount", "payouts.payout_status", "payouts.payment_method", "payouts.payout_details", "payouts.created_at")
                    ->where('payouts.pdoa_id', $pdoa_id)
                    ->orderBy("created_at")->orderBy("franchise_id")->orderBy("distributor_id")->get();
                break;
            case 3:
                $payouts = Payouts::leftJoin('wifi_user_accounts', 'payouts.wifi_user_id', '=', 'wifi_user_accounts.id')
                    ->leftJoin("users as franchise", "payouts.franchise_id", "=", "franchise.id")
                    ->leftJoin("users as distributor", "payouts.distributor_id", "=", "franchise.id")
                    ->select("payouts.id", DB::raw("CONCAT(wifi_user_accounts.first_name, ' ', wifi_user_accounts.last_name) as wifi_username"), "wifi_user_accounts.phone as wifi_user_phone", "payouts.amount", "payouts.tax_amount", "franchise.username as franchise_name", "payouts.franchise_amount", "distributor.username as distributor_name", "payouts.distributor_amount", "payouts.payout_status", "payouts.payment_method", "payouts.payout_details", "payouts.created_at")
                    ->where("payouts.distributor_id", $user->id)
                    ->where('payouts.pdoa_id', $pdoa_id)
                    ->orderBy("created_at")->orderBy("franchise_id")->orderBy("distributor_id")->get();
                break;
            default:
                $payouts = Payouts::leftJoin('wifi_user_accounts', 'payouts.wifi_user_id', '=', 'wifi_user_accounts.id')
                    ->leftJoin("users as franchise", "payouts.franchise_id", "=", "franchise.id")
                    ->leftJoin("users as distributor", "payouts.distributor_id", "=", "franchise.id")
                    ->select("payouts.id", DB::raw("CONCAT(wifi_user_accounts.first_name, ' ', wifi_user_accounts.last_name) as wifi_username"), "wifi_user_accounts.phone as wifi_user_phone", "payouts.amount", "payouts.tax_amount", "franchise.username as franchise_name", "payouts.franchise_amount", "distributor.username as distributor_name", "payouts.distributor_amount", "payouts.payout_status", "payouts.payment_method", "payouts.payout_details", "payouts.created_at")
                    ->where("payouts.franchise_id", $user->id)
                    ->where('payouts.pdoa_id', $pdoa_id)
                    ->orderBy("created_at")->orderBy("franchise_id")->orderBy("distributor_id")->get();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Payouts success!',
            "data" => $payouts,
        ]);
    }

    public function log_payout($wifi_user_id, $internet_plan_id, $location_id, $payment_method)
    {
        $plan = Internet_plans::find($internet_plan_id);
        $amount = $plan['price'];
        $tax_amount = $amount * $_ENV['GST'] / 100;
        $location = Location::find($location_id);
        $franchise = Users::find($location['owner_id']);
        $distributor = Users::find($franchise['belongs_to']);
        $franchise_amount = ($amount - $tax_amount) * $franchise['revenue_model'] / 100;
        $distributor_amount = ($amount - $tax_amount) * $distributor['revenue_model'] / 100;
        $payout = Payouts::create([
            'wifi_user_id' => $wifi_user_id,
            'amount' => $amount,
            'tax_amount' => $tax_amount,
            'payment_method' => $payment_method,
            'franchise_id' => $franchise['id'],
            'franchise_amount' => $franchise_amount,
            'distributor_id' => $distributor['id'],
            'distributor_amount' => $distributor_amount,
            'pdoa_id' => $location['pdoa_id'],
            'payout_status' => 0
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Logging Payout is success.',
            'data' => $payout
        ]);
    }

    public function update_process($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            Payouts::where(["payout_status" => 0])->update(["payout_status" => 1]);
        } else {
            Payouts::where(["payout_status" => 0, 'pdoa_id' => $pdoa_id])->update(["payout_status" => 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'All of Payout Logs are successfully updated as processed.',
        ]);
    }
}