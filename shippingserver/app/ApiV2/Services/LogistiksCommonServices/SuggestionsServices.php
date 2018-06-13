<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Model\BlueCollar\MachineVehicleType;
use ApiV2\Services\BlueCollar\BaseServiceProvider;
use DB;

class SuggestionsServices extends BaseServiceProvider
{

    public static function citySuggestions($request)
    {
        $results = DB::table('lkp_ptl_pincodes')
            ->select('lkp_ptl_pincodes.id', 'districtname', 'statename', 'lkp_ptl_pincodes.lkp_district_id as district_id', 'state_id', 'lkp_cities.id as city_id', 'lkp_cities.city_name as city_name', 'lkp_ptl_pincodes.pincode')
            ->where('pincode', '=', $request->text)
            ->where('lkp_ptl_pincodes.is_active', '=', 1)
           // ->where('lkp_cities.id','=',1263)
            ->join('lkp_cities', function($join) use ($request){
                $join->on('lkp_cities.lkp_district_id', '=', 'lkp_ptl_pincodes.lkp_district_id');
            })
            ->groupBy('districtname', 'statename', 'city_name')
            ->get();
        self::$data['data'] = $results;
        self::$data['status'] = 200;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function vehicleTypes()
    {
        $results = MachineVehicleType::where('type', '=', 'VEHICLE')->get();
        self::$data['data'] = $results;
        self::$data['status'] = 200;
        self::$data['success'] = true;
        return self::$data;
    }

    public static function machineTypes()
    {
        $results = MachineVehicleType::where('type', '=', 'MACHINE')->get();
        self::$data['data'] = $results;
        self::$data['status'] = 200;
        self::$data['success'] = true;
        return self::$data;
    }

}
