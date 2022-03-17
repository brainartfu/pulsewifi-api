<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\nas_list;
use App\Models\Wifi_router;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request, $pdoa_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'owner_id' => 'required|integer',
            'address' => 'required|string|min:2|max:100',
            'city' => 'required|string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
            'country' => 'required|string|min:2|max:100',
            'postal_code' => 'required|integer|min:100000|max:999999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        if (!isset($req_array["state"])) {
            $req_array["state"] = '';
        }

        $client = new \GuzzleHttp\Client();
        $geocoder = new \Spatie\Geocoder\Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));

        $req_array = $request->all();
        $geocoder->setCountry(config('geocoder.country', $req_array['country']));
        $address_to_geocode = Self::sanitize_address($req_array);
        $geoCode = $geocoder->getCoordinatesForAddress($address_to_geocode);

        $location = Location::create([
            'name' => $request->input('name'),
            'pdoa_id' => $pdoa_id,
            'owner_id' => $request->input('owner_id'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'country' => $request->input('country'),
            'postal_code' => $request->input('postal_code'),
            'latitude' => $geoCode['lat'],
            'longitude' => $geoCode['lng'],
        ]);
        $location_id = $location->id;
              $nas = nas_list::create([
                  'id' => $location_id,
                  'pdoa' => $pdoa_id
              ]);
        return response()->json([
            'success' => true,
            'message' => 'Location successfully registered',
            'data' => $location        
        ]);
    }

    public function delete($location_id)
    {
        $del_location = Location::find($location_id);
        if (!$del_location) {
            return response()->json([
                'success' => false,
                'message' => 'NoLocation',
            ]);
        }

        $router_count = Wifi_router::where("location_id", $location_id)->get()->count();

        // if ($router_count != 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'This location has own Wifi routers.',
        //     ]);
        // }
        $del_location->delete();
        return response()->json([
            'success' => true,
            'message' => 'Location successfully deleted.',
        ]);
    }

    public function get_all_locations()
    {
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-180 seconds"));
        $onlinetime = $now->format('Y-m-d H:i:s');
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-7 days"));
        $inactivetime = $now->format('Y-m-d H:i:s');
        $jointbl = Location::leftJoin('wifi_router', 'location.id', '=', 'wifi_router.location_id')
        ->select('location.*', DB::raw("count(wifi_router.location_id) as router_count"), DB::raw("SUM(CASE WHEN wifi_router.last_online > '$onlinetime' THEN 1 ELSE 0 END) as online_status"), DB::raw("SUM(CASE WHEN wifi_router.last_online < '$inactivetime' THEN 1 ELSE 0 END) as inactive_count"))
        ->groupBy('location.id')
        ->groupBy('location.name')
        ->groupBy('location.owner_id')
        ->groupBy('location.address')
        ->groupBy('location.city')
        ->groupBy('location.state')
        ->groupBy('location.country')
        ->groupBy('location.postal_code')
        ->groupBy('location.latitude')
        ->groupBy('location.longitude')
        ->groupBy('location.pdoa_id')
        ->groupBy('location.created_at')
        ->groupBy('location.updated_at')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting locations success!',
            "data" => $jointbl,
        ]);
    }
    
    public function get_locations($franchise_id, $pdoa_id)
    {
        $user = auth()->user();
        if ($user->role > 3 && $franchise_id == 0) {
            return response()->json([
                'success' => false,
                'message' => 'PermissionError',
            ]);
        }
        if ($franchise_id != 0) {
            $user = Users::find($franchise_id);
        }
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-180 seconds"));
        $onlinetime = $now->format('Y-m-d H:i:s');
        $now = date_create(date("Y-m-d H:i:s"));
        date_add($now, date_interval_create_from_date_string("-7 days"));
        $inactivetime = $now->format('Y-m-d H:i:s');
        if ($franchise_id == 0) {
            $f_arr = Users::select("id")->where('pdoa_id','=',$pdoa_id)->get();
        } else {
            if($user['role'] == 4) { 
                $f_arr = Users::select('id')->where('belongs_to', '=', $franchise_id)->get();
            } else $f_arr = [['id'=> $franchise_id]];
        }
        $franchises =array();
        foreach ($f_arr as $key => $value) {
            $franchises[] = $value['id'];
        }
        $jointbl = Location::leftJoin('wifi_router', 'location.id', '=', 'wifi_router.location_id')
        ->select('location.*', DB::raw("count(wifi_router.location_id) as router_count"), DB::raw("SUM(CASE WHEN wifi_router.last_online > '$onlinetime' THEN 1 ELSE 0 END) as online_status"), DB::raw("SUM(CASE WHEN wifi_router.last_online < '$inactivetime' THEN 1 ELSE 0 END) as inactive_count"))
        ->where('location.pdoa_id', '=', $pdoa_id)
        ->whereIn('location.owner_id', $franchises)
        ->groupBy('location.id')
        ->groupBy('location.name')
        ->groupBy('location.owner_id')
        ->groupBy('location.address')
        ->groupBy('location.city')
        ->groupBy('location.state')
        ->groupBy('location.country')
        ->groupBy('location.postal_code')
        ->groupBy('location.latitude')
        ->groupBy('location.longitude')
        ->groupBy('location.pdoa_id')
        ->groupBy('location.created_at')
        ->groupBy('location.updated_at')
        ->get();
       
        return response()->json([
            'success' => true,
            'message' => 'Getting locations success!',
            "data" => $jointbl,
        ]);
    }

    public function update(Request $request, $location_id)
    {
        $location = Location::find($location_id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'owner_id' => 'required|integer|min:1',
            'address' => 'string|min:2|max:100',
            'city' => 'string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
            'country' => 'required|string|min:2|max:100',
            'postal_code' => 'required|integer|min:100000|max:999999',
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
        $client = new \GuzzleHttp\Client();
        $geocoder = new \Spatie\Geocoder\Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));

        $req_array = $request->all();
        $geocoder->setCountry(config('geocoder.country', $req_array['country']));
        $address_to_geocode = Self::sanitize_address($req_array);
        $geoCode = $geocoder->getCoordinatesForAddress($address_to_geocode);

        $arr_update_keys['latitude'] = $geoCode['lat'];
        $arr_update_keys['longitude'] = $geoCode['lng'];
        $location->update($arr_update_keys);
        return response()->json([
            'success' => true,
            'message' => 'Location successfully updated.',
            'data' => $location,
        ]);
    }

    public function sanitize_address($req_array)
    {
        $address = '';
        if (isset($req_array['address'])) {
            $address = $address . $req_array['address'];
        }
        if (isset($req_array['city'])) {
            if (strlen($address) > 0) {
                $address = $address . ', ';
            }
            $address = $address . $req_array['city'];
        }
        if (isset($req_array['state'])) {
            if (strlen($address) > 0) {
                $address = $address . ', ';
            }
            $address = $address . $req_array['state'];
        }

        if (isset($req_array['postal_code'])) {
            if (strlen($address) > 0) {
                $address = $address . ', ';
            }
            $address = $address . $req_array['postal_code'];
        }
        return $address;

    }
}
