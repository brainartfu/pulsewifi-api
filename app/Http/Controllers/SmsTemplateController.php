<?php

namespace App\Http\Controllers;

use App\Models\Sms_template;
use Auth;
use Illuminate\Http\Request;
use Validator;

class SmsTemplateController extends Controller
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
            'name' => 'required|string|min:2|max:100|unique:sms_template',
            'sender_id' => 'required|string|min:2|max:100',
            'dlt_id' => 'string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        $template = Sms_template::create([
            'name' => $request->input('name'),
            'sender_id' => $request->input('sender_id'),
            'dlt_id' => $request->input('dlt_id'),
            'text' => $request->input('text'),
            'pdoa_id' => $pdoa_id,
            'status' => $request->input('status'),
        ]);

        $response_template = Sms_template::where(['name' => $template->name])->first();

        return response()->json([
            'success' => true,
            'message' => 'SMS Template successfully registered',
            'data' => $response_template,
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

        $del_template = Sms_template::find($id);
        if (!$del_template) {
            return response()->json([
                'success' => false,
                'message' => 'NoTemplate',
            ]);
        }

        $del_template->delete();
        return response()->json([
            'success' => true,
            'message' => 'SMS Template successfully deleted.',
        ]);
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
        if ($user->role == 1) {
            $templates = Sms_template::get();
        } else {
            $templates = Sms_template::where('pdoa_id', $pdoa_id)->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting SMS Templates success!',
            "data" => $templates,
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

        $template = Sms_template::find($id);
        $arr_update_keys = array([]);

        if ($template->name != $request->input("name")) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
                'sender_id' => 'required|string|min:2|max:100',
                'dlt_id' => 'string|min:2|max:100',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
                'sender_id' => 'required|string|min:2|max:100',
                'dlt_id' => 'string|min:2|max:100',
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

        $template->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'SMS Template successfully updated.',
            'data' => $template,
        ]);
    }
}