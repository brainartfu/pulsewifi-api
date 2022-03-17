<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\Roles;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;


class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request)
    {
        $user = auth()->user();
        if ($user->role != 1) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100|unique:roles',
            'display_name' => 'required|string|min:2|max:100|unique:roles'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors()
            ]);
        }
        foreach ($request->all() as $key => $value) {
            $arr_create_keys[$key] = $value;
        }

        $role = Roles::create($arr_create_keys);

        $response_role = Roles::where(['name' => $role->name])->first();
            
        return response()->json([
            'success' => true,
            'message' => 'Role successfully registered',
            'data' => $response_role
        ]);
    }
    
    public function delete($id)
    {
        $user = auth()->user();
        if ($user->role != 1) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError'
            ]);
        }
        $del_role = Roles::find($id);
        if (!$del_role) {
            return response()->json([
                'success' => false,
                'message' => 'NoRole'
            ]);
        }
        if ($del_role['required']) {
            return response()->json([
                'success' => false,
                'message' => "This role is required! Don't delete this role!"
            ]);
        }

        $del_role -> delete();
        return response()->json([
            'success' => true,
            'message' => 'Role successfully deleted.'
        ]);
    }
    
    public function get_staff_role()
    {
        $roles = Roles::select("id", "name", "display_name")->where("id", ">", 2)->where("id", "<>", 4)->where("id", "<>", 5)->get();
        return response()->json([
            'success' => true,
            'message' => 'Getting staff roles success!',
            "data" => $roles
        ]);
    }

    public function get_role($id)
    {
        $role = Roles::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Getting role success!',
            "data" => $role
        ]);
    }

    public function get()
    {        
        $user = auth()->user();
        if ($user->role != 1) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError'
            ]);
        }
        $roles = Roles::get();
        return response()->json([
            'success' => true,
            'message' => 'Getting roles success!',
            "data" => $roles
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Roles::find($id);
        
        // if ($request->input("name")) {
            if ($role['name'] != $request->input("name")) { 
                $validator_name = 'min:2|string|max:100|unique:roles';
            } else {
                $validator_name = 'min:2|string|max:100';
            }
        // }
                
        // if ($request->input("display_name")) {
            if ($role['display_name'] != $request->input("display_name")) { 
                $validator_display = 'min:2|string|max:100|unique:roles';
            } else {
                $validator_display = 'min:2|string|max:100';
            }
        // }
        
        $validator = Validator::make($request->all(), [
            'name' => $validator_name,
            'display_name' => $validator_display
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
        $role->update($arr_update_keys);

        return response()->json([
            'success' => true,
            'message' => 'Role successfully updated.',
            'data' => $role
        ]);
    }
}