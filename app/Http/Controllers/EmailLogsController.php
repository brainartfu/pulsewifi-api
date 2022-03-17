<?php

namespace App\Http\Controllers;

use App\Models\Email_logs;
use Illuminate\Http\Request;

class EmailLogsController extends Controller
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

        $logs = Email_logs::where('pdoa_id', '=', $pdoa_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting Email Logs success!',
            "data" => $logs
        ]);
    }

}
