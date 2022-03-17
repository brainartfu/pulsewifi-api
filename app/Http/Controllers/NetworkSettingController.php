<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\Network_setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NetworkSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }
    
    public function get($pdoa_id)
    {        
        $user = auth()->user();
        if ($user->role == 1) {
            $network = Network_setting::leftJoin('pdoas', 'network_setting.pdoa_id', '=', 'pdoas.id')
            ->select('network_setting.*', 'pdoas.domain_name')
            ->get();
        } else {
            $network = Network_setting::leftJoin('pdoas', 'network_setting.pdoa_id', '=', 'pdoas.id')
            ->select('network_setting.*', 'pdoas.domain_name')
            ->where('network_setting.pdoa_id', $pdoa_id)
            ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Network setting success!',
            "data" => $network
        ]);
    }

    public function update(Request $request, $id)
    {     
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError'
            ]);
        }
        
        $network = Network_setting::find($id);
        $arr_update_keys = array([]);
       
        $validator = Validator::make($request->all(), [
            'guestEssid' => 'required|string|min:2|max:100',
            'freeWiFi' => 'required',
            'freeBandwidth' => 'required|integer|min:1',
            'freeDailySession' => 'required|integer',
            'freeDataLimit' => 'required|integer',
            'serverWhitelist' => 'string',
            'domainWhitelist' => 'string'
        ]);
            
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors()
            ]);
        }
        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }
        
        $network->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Network setting successfully updated.',
            'data' => $network
        ]);
    }
    
}