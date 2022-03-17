<?php

namespace App\Http\Controllers;

use App\Models\Sms_logs;
use Illuminate\Http\Request;

class SmsLogsController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }
    
    public function get($pdoa_id)
    {        
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        $logs = Sms_logs::where('pdoa_id', '=', $pdoa_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting SMS Logs success!',
            "data" => $logs
        ]);
    }

}
