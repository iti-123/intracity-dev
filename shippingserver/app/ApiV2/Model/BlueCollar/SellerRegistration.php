<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace ApiV2\Model\BlueCollar;

use ApiV2\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class SellerRegistration extends Model
{
    protected $table = 'bluecollar_seller_registration';
    protected $appends = array('enc_id');

    use Encryptable;

    //  var_dump(self::getAttribute('id'));

    public function vehicleTypes()
    {
        return $this->hasMany('ApiV2\Model\BlueCollar\SellerRegVehMach', 'bc_reg_id')->where('status', '=', 'ACTIVE');
    }

    public function experience()
    {
        return $this->hasMany('ApiV2\Model\BlueCollar\SellerRegExperience', 'bc_reg_id')->where('status', '=', 'ACTIVE');
    }

    public function qualification()
    {
        return $this->hasMany('ApiV2\Model\BlueCollar\SellerRegQualif', 'bc_reg_id')->where('status', '=', 'ACTIVE');
    }

    public function curCity()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\CityModel', 'id', 'cur_city_id')->select('id', 'city_name');
    }

    public function curState()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\StateModel', 'id', 'cur_state_id')->select('id', 'state_name');
    }

    public function curDistrict()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\DistrictModel', 'id', 'cur_district_id')->select('id', 'district_name');
    }

    public function perCity()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\CityModel', 'id', 'per_city_id')->select('id', 'city_name');
    }

    public function perState()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\StateModel', 'id', 'per_state_id')->select('id', 'state_name');
    }

    public function perDistrict()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\DistrictModel', 'id', 'per_district_id')->select('id', 'district_name');
    }

    public function createdBy()
    {
        return $this->hasOne('ApiV2\Model\UserDetails', 'id', 'created_by');
    }

}
