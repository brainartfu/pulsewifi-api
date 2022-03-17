<?php

namespace App\Http\Controllers;

use App\Models\Email_template;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class EmailTemplateController extends Controller
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
            'name' => 'required|string|min:2|max:100|unique:email_template',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        if ($request->file('email_template')) {
            if (!($request->file('email_template')->getClientOriginalExtension() == "html" || $request->file('email_template')->getClientOriginalExtension() == "htm")) {
                $valid['file'] = "Invalid file format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            }
        } else {
            $valid['file'] = "Email template file is requlred!";
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $valid,
            ]);
        }
        $template = Email_template::create([
            'name' => $request->input('name'),
            'status' => $request->input('status'),
            'pdoa_id' => $pdoa_id,
        ]);

        $response_template = Email_template::where(['name' => $template->name])->first();

        $file_name = "template_" . $response_template['id'] . "." . $request->file('email_template')->getClientOriginalExtension();
        $file_path = $request->file('email_template')->storeAs(
            'email_template', $file_name
        );

        $text = Storage::get($file_path);

        $response_template->update(["file_path" => $file_path, "text" => $text]);
        return response()->json([
            'success' => true,
            'message' => 'Email Template successfully registered',
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

        $del_template = Email_template::find($id);
        if (!$del_template) {
            return response()->json([
                'success' => false,
                'message' => 'NoTemplate',
            ]);
        }

        $del_file = $del_template["file_path"];
        Storage::delete($del_file);
        $del_template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email Template successfully deleted.',
        ]);
    }

    public function get($pdoa_id)
    {
        $user = auth()->user();
        if ($user->role == 1) {
            $templates = Email_template::get();
        } else {
            $templates = Email_template::where('pdoa_id', $pdoa_id)->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Email Templates success!',
            "data" => $templates,
        ]);
    }

    public function get_template($id)
    {
        $user = auth()->user();
        if ($user->role > 2) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        $template = Email_template::find($id);
        $file = $template['file_path'];

        return Storage::download($file);
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
        $template = Email_template::find($id);

        $arr_update_keys = array([]);

        if ($template->name != $request->input("name")) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100|unique:email_template',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        if ($request->file('email_template')) {
            if (!($request->file('email_template')->getClientOriginalExtension() == "html" || $request->file('email_template')->getClientOriginalExtension() == "htm")) {
                $valid['file'] = "Invalid file format!";
                return response()->json([
                    'success' => false,
                    'message' => 'ValidationError',
                    'data' => $valid,
                ]);
            }
        } else {
            $valid['file'] = "Email template file is requlred!";
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $valid,
            ]);
        }

        $file_name = "template_" . $id . "." . $request->file('email_template')->getClientOriginalExtension();
        $file_path = $request->file('email_template')->storeAs(
            'email_template', $file_name
        );
        $text = Storage::get($file_path);

        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }

        $arr_update_keys['file_path'] = $file_path;
        $arr_update_keys['text'] = $text;
        $template->update($arr_update_keys);

        return response()->json([
            'success' => true,
            'message' => 'Email Template successfully updated.',
            'data' => $template,
        ]);
    }
}