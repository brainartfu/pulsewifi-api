<?php

namespace App\Http\Controllers;

use App\Models\Mail_server;
use Auth;
use Illuminate\Http\Request;
use Validator;

class MailServerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request, $pdoa_id)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100|unique:mail_server',
            'sender_name' => 'required|string|min:2|max:100',
            'sender_email' => 'required|string|min:2|max:100',
            'api_key' => 'required|string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $mailserver = Mail_server::create([
            'name' => $request->input('name'),
            'sender_name' => $request->input('sender_name'),
            'sender_email' => $request->input('sender_email'),
            'api_key' => $request->input('api_key'),
            'pdoa_id' => $pdoa_id,
            'status' => $request->input('status'),
        ]);

        $response_mailserver = Mail_server::where(['name' => $mailserver->name])->first();

        return response()->json([
            'success' => true,
            'message' => 'Mail server successfully registered',
            'data' => $response_mailserver,
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

        $del_mailserver = Mail_server::find($id);
        if (!$del_mailserver) {
            return response()->json([
                'success' => false,
                'message' => 'Nomailserver',
            ]);
        }

        $del_mailserver->delete();
        return response()->json([
            'success' => true,
            'message' => 'Mail server successfully deleted.',
        ]);
    }

    public function get($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $mail_server = Mail_server::leftJoin('pdoas', 'mail_server.pdoa_id', '=', 'pdoas.id')
            ->select('mail_server.*', 'pdoas.domain_name')
            ->get();
        } else {
            $mail_server = Mail_server::leftJoin('pdoas', 'mail_server.pdoa_id', '=', 'pdoas.id')
            ->select('mail_server.*', 'pdoas.domain_name')
            ->where('mail_server.pdoa_id', $pdoa_id)
            ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Mail server success!',
            "data" => $mail_server,
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

        $mailserver = Mail_server::find($id);
        $arr_update_keys = array([]);

        if ($mailserver->name != $request->input("name")) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100|unique:mail_server',
                'sender_name' => 'required|string|min:2|max:100',
                'sender_email' => 'required|string|min:2|max:100',
                'api_key' => 'required|string|min:2|max:100',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
                'sender_name' => 'required|string|min:2|max:100',
                'sender_email' => 'required|string|min:2|max:100',
                'api_key' => 'required|string|min:2|max:100',
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

        $mailserver->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Mail server successfully updated.',
            'data' => $mailserver,
        ]);
    }
}