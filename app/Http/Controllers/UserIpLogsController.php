<?php

namespace App\Http\Controllers;

use App\Models\User_ip_logs;
use Illuminate\Http\Request;
use Auth;
use Validator;

class UserIpLogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['add']]);
    }
    
    public function add(Request $request, $pdoa_id)
    {        
        $validator = Validator::make($request->all(), [
            'src_ip' => 'required|string',
            'dest_ip' => 'required|string',
            'protocol' => 'required|string',
            'port' => 'required|string',
            'username' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }
        $req_array = $request->all();
        $req_array['pdoa_id'] = $pdoa_id;
        $log = User_ip_logs::create($req_array);
        return response()->json([
            'success' => true,
            'message' => 'User IP Log is added successfully!',
            "data" => $log
        ]);
    }

    public function get($pdoa_id, $filter)
    {        
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        if($filter == "all") $logs = User_ip_logs::where('pdoa_id', '=', $pdoa_id)->paginate(10);
        else $logs = User_ip_logs::where('pdoa_id', '=', $pdoa_id)
                        ->where(function($query) use ($filter)
                        {
                            $query->where('username', 'LIKE', '%'.$filter.'%')
                            ->orWhere('src_ip', 'LIKE', '%'.$filter.'%')
                            ->orWhere('dest_ip', 'LIKE', '%'.$filter.'%')
                            ->orWhere('protocol', 'LIKE', '%'.$filter.'%')
                            ->orWhere('port', 'LIKE', '%'.$filter.'%')
                            ->orWhere('src_port', 'LIKE', '%'.$filter.'%')
                            ->orWhere('dest_port', 'LIKE', '%'.$filter.'%')
                            ->orWhere('client_device_ip', 'LIKE', '%'.$filter.'%')
                            ->orWhere('client_device_ip_type', 'LIKE', '%'.$filter.'%')
                            ->orWhere('client_device_translated_ip', 'LIKE', '%'.$filter.'%');
                        })
                        ->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Getting User IP Logs success!',
            "data" => $logs
        ]);
    }

}
