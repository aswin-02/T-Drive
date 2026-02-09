<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class GeneralController extends Controller
{


    public function getRoles(){
        $roles = Role::all();
        return response()->json($roles);
    }

    public function getStates(){
        $states = State::where('is_active','1')->get();
        return response()->json($states);
    }

    public function getDistricts(Request $req)
    {
        $districts = District::where('is_active', '1');

        if ($req->filled('state_id')) {
            $districts->where('state_id', $req->state_id);
        }

        return response()->json($districts->get());
    }
    
    public function getPinCodes(Request $request): JsonResponse
    {
        $pincode = Pincode::where('is_active','1');

        if($request->filled('district_id')){
            $pincode->where('district_id', $request->district_id);
        }

        return response()->json($pincode->get());
    }

    public function getCities(Request $req)
    {
        $cities = City::where('is_active','1');

        if($req->filled('district_id')){
            $cities->where('district_id', $req->district_id);
        }

        return response()->json($cities->get());
    }
}