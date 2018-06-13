<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class BuyerDetail extends Model
{
    public function city()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\CityModel','id','lkp_city_id')->select('id','city_name as location');
    }   
    
    
}