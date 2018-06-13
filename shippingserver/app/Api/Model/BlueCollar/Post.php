<?php

namespace Api\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'bluecollar_posts';

    public function bcData()
    {
        return $this->hasOne('Api\Model\BlueCollar\SellerRegistration', 'id', 'bc_reg_id');
    }

    public function vehMach()
    {
        return $this->hasMany('Api\Model\BlueCollar\PostVehMach', 'post_id', 'id');
    }

    public function postedBy()
    {
        return $this->hasOne('Api\Model\UserDetails', 'id', 'posted_by')->select('id', 'username');
    }

    public function city()
    {
        return $this->hasOne('Api\Model\BlueCollar\CityModel', 'id', 'city_id')->select('id', 'city_name');
    }

    public function state()
    {
        return $this->hasOne('Api\Model\BlueCollar\StateModel', 'id', 'state_id')->select('id', 'state_name');
    }

    public function district()
    {
        return $this->hasOne('Api\Model\BlueCollar\DistrictModel', 'id', 'district_id')->select('id', 'district_name');
    }

    public function quote()
    {
        //bluecollar Service id is 23
        return $this->hasMany('Api\Model\BlueCollar\Quote', 'post_id', 'id')->where('lkp_service_id', '=', 23);
    }

  public function accessList(){
    return $this->hasMany('Api\Model\BlueCollar\PostAccessList', 'post_id', 'id');
  }

}
