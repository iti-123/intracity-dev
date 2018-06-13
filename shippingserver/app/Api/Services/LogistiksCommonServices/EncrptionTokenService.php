<?php

namespace Api\Services\LogistiksCommonServices;

use Api\Services\BlueCollar\BaseServiceProvider;
use Illuminate\Support\Facades\Crypt;

class EncrptionTokenService extends BaseServiceProvider
{

    public static function idEncrypt($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $value->id = Crypt::encrypt($value->id);
            }
        } else {
            $data->id = Crypt::encrypt($data->id);
        }
        return $data;
    }

    


    public static function idDecrypt($value)
    {
        return Crypt::decrypt($value);
    }

    public static function valueEncrypt($value)
    {
        return Crypt::encrypt($value);
    }

    public static function eloqIdEncrypt($data)
    {
        foreach ($data as $key => $value) {
            $value->enc_id = Crypt::encrypt($value->id);
        }
        return $data;
    }

    public static function encryptRouteId($payload)
    {
        
        foreach ($payload as $pkey => $value) {
            
            foreach ($value->routes as $key => $route) {
                
                $value->routes[$key]->encId = Crypt::encrypt($route->id);
              
            }
        }
         

        return $payload;
    }

}
