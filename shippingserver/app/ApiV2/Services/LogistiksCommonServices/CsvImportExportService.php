<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Model\BlueCollar\CityModel;
use ApiV2\Model\LogistiksUser;
use DB;

class CsvImportExportService 
{

    protected $data = [];
    //protected $response = ['status' => 0, 'success' => 'success', 'message' => 'no record found'];

    public static function getCityIdByCityName($param)
    {
        $response = [];
        $results = CityModel::where('city_name', '=', $param)->first();
        if(count($results)){
                $response['status'] = 1;
                $response['id'] = $results->id;
                $response['state_id'] = $results->lkp_state_id;
                $response['district_id'] = $results->lkp_district_id;
        }else{
                $response['status'] = 0;
                $response['success'] = 'success';
                $response['message'] = 'no record found';
        }
        return $response;
    }
    
    
    public static function getBuyerIdByEmail($param){
        
        $response = [];        
        $getBuyerId = LogistiksUser::where('email', '=', $param)->where('is_active', '=', 1)->first();

        if(count($getBuyerId)){
                $response['status'] = 1;
                $response['id'] = $getBuyerId->id;
        }else{
                $response['status'] = 0;
                $response['success'] = 'success';
                $response['message'] = 'no record found';
        }
        return $response;        
        
    }    

}
