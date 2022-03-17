<?php

namespace App\Http\Controllers;

use App\Models\WifiRouterModel;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class WifiRouterModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request)
    {
        $user = auth()->user();
        if ($user->role > 3) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100|unique:wifi_router_model',
            'description' => 'string|min:2|max:100',
            'price' => 'numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        foreach ($request->all() as $key => $value) {
            $arr_insert_keys[$key] = $value;
        }

        $wifiRouterModel = WifiRouterModel::create($arr_insert_keys);
        $response_wifiRouterModel = WifiRouterModel::where(['name' => $wifiRouterModel->name])->first();
        $id = $response_wifiRouterModel['id'];

        if ($request->file('model_images') && count($request->file('model_images'))) {
            $str_file_path = "";
            foreach ($request->file('model_images') as $index => $file) {
                $file_name = "WiFiRouterModel_" . $id . "_" . $index . "." . $file->getClientOriginalExtension();
                $file_path = $file->storeAs(
                    'public/WiFiRouter_img', $file_name
                );
                if ($index == 0) {
                    $str_file_path = $file_path;
                } else {
                    $str_file_path = $str_file_path . "," . $file_path;
                }

            }

            $response_wifiRouterModel->update(['images' => $str_file_path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Wifi Router Model successfully registered',
            'data' => $response_wifiRouterModel,
        ]);
    }

    public function delete($id)
    {

        $del_wifiRouterModel = WifiRouterModel::find($id);
        if (!$del_wifiRouterModel) {
            return response()->json([
                'success' => false,
                'message' => 'NoWifiRouterModel',
            ]);
        }

        if ($del_wifiRouterModel['images'] != "") {
            $file_path = explode(",", $del_wifiRouterModel['images']);
            foreach ($file_path as $path) {
                if (\Storage::exists($path)) {
                    \Storage::delete($path);
                }
            }
        }

        $del_wifiRouterModel->delete();
        return response()->json([
            'success' => true,
            'message' => 'Wifi Router Model successfully deleted.',
        ]);
    }

    public function get()
    {
        $wifiRouterModels = WifiRouterModel::get();

        return response()->json([
            'success' => true,
            'message' => 'Getting Wifi Router Models success!',
            'data' => $wifiRouterModels,
        ]);
    }

    public function get_routerModel($id)
    {
        $wifiRouterModel = WifiRouterModel::find($id);

        return response()->json([
            'success' => true,
            'message' => 'Getting Wifi Router Model success!',
            "data" => $wifiRouterModel,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role > 3) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }

        $wifiRouterModel = WifiRouterModel::find($id);
        $arr_update_keys = array([]);

        if ($wifiRouterModel->name != $request->input("name")) {
            $valid_name = 'required|string|min:2|max:100|unique:wifi_router_model';
        } else {
            $valid_name = 'required|string|min:2|max:100';
        }
        $validator = Validator::make($request->all(), [
            'name' => $valid_name,
            'description' => 'string|min:2|max:100',
            'price' => 'numeric|min:1',
        ]);
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
        if ($request->input('deleted_files') && $request->input('deleted_files') != "") {
            $del_files = explode(",", $request->input('deleted_files'));
            foreach ($del_files as $path) {
                if (\Storage::exists("public/WiFiRouter_img/" . $path)) {
                    \Storage::delete("public/WiFiRouter_img/" . $path);
                }
            }
            $arr_update_keys["images"] = $request->input('updated_filenames');
        }
        if ($request->file('model_images') && count($request->file('model_images'))) {
            if ($arr_update_keys && !array_key_exists("images", $arr_update_keys)) {
                if ($wifiRouterModel["images"] && $wifiRouterModel["images"] != "") {
                    $arr_update_keys["images"] = $wifiRouterModel["models"];
                } else {
                    $arr_update_keys["images"] = "";
                }

            }
            $last_file_index = -1;
            if ($arr_update_keys["images"] != "") {
                $file_names = explode(",", $arr_update_keys["images"]);
                $last_file_index = explode("_", $file_names[count($file_names) - 1])[2];
            }
            foreach ($request->file('model_images') as $index => $file) {
                $update_index = $last_file_index + $index + 1;
                $file_name = "WiFiRouterModel_" . $id . "_" . $update_index . "." . $file->getClientOriginalExtension();
                $file_path = $file->storeAs(
                    'public/WiFiRouter_img', $file_name
                );
                if ($arr_update_keys["images"] == "") {
                    $arr_update_keys["images"] = $file_path;
                } else {
                    $arr_update_keys["images"] = $arr_update_keys["images"] . "," . $file_path;
                }
            }

        }

        $wifiRouterModel->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Wifi Router Model successfully updated.',
            'data' => $wifiRouterModel,
        ]);
    }
}