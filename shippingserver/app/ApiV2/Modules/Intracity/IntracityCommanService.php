<?php

namespace ApiV2\Modules\Intracity;

use Tymon\JWTAuth\Facades\JWTAuth;
use Log;
use DB;
use ApiV2\Model\BlueCollar\CityModel;
use ApiV2\Model\BlueCollar\MachineVehicleType;
class IntracityCommanService
{
    public function getBaseTime($slab) {
        $timeSlab = $this->get_numerics($slab);
        $data = DB::table('intracity_hour_distance_slabs')
        ->where("hour","=",$timeSlab[0])
        ->first();
        return $data ? $data->id:'';
    } 

    public function get_numerics ($str) {
        preg_match_all('/\d+/', $str, $matches);
        return $matches[0];
    }

    public function getCityIdByName($cityName) {
        if(!empty($cityName)):
            $city = CityModel::where('city_name','LIKE',"%{$cityName}%")->first();
        else:
            $city = null;
        endif;
        return $city ? $city->id : '';
    }
    
    public function getVehicleIdByName($vehicleName) {
        if(!empty($vehicleName)):
            $vehicleType = DB::table('lkp_vehicle_types')->where('vehicle_type','LIKE',"%{$vehicleName}%")->first();
        else:
            $vehicleType = null;
        endif;
        return $vehicleType ? $vehicleType->id : '';
    }

    // public function getLocation($location) {
    //     $locality = LocalityModel::where('locality_name','LIKE',"%{$location}%")->first();
    //     Log::info('$locality->id'. json_encode($locality);
    //     return $locality ? $locality->id : ''; 
    // }

    public function getMileStone($tracking) {
        switch($tracking) {
            case 'Mile Stone':
                return 1;
                break;
            case 'Real Time':
                return 2;
                break;
            default:
                return "";
        }
    }
}
