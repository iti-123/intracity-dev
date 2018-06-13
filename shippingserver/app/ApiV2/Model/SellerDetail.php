<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class SellerDetail extends Model
{
    
    public function city()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\CityModel','id','lkp_city_id')->select('id','city_name as location');
    }


    public function industry() {
        return $this->hasOne('ApiV2\Model\Industry','id','lkp_industry_id')->select('id','industry_name as type');
    }

    public function empStrength() {
        return $this->hasOne('ApiV2\Model\EmpStrength','id','lkp_employee_strength_id')->select('id','employee_strength as total');
    }

    public function business() {
        return $this->hasOne('ApiV2\Model\BusinessType','id','lkp_business_type_id')->select('id','business_type_name as type');
    }
}



