<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class IntraHyperRoute extends Model
{

    protected $fillable = [
        'fk_buyer_seller_post_id',
        'type_basis',
        'city_id',
        'hour_dis_slab',
        'vehicle_type_id',
        'valid_from',
        'valid_to',
        'number_of_veh_need',
        'vehicle_rep_location',
        'vehicle_rep_time',
        'weight',
        'material_type',
        'from_location',
        'to_location'
    ];

    protected $guard = ['city_name'];

    protected $table = 'intra_hp_buyer_seller_routes';

    public function views()
    {
        return $this->hasMany('ApiV2\Model\UserView', 'model_id', 'id')->select('model_id','user_id','view_count');
    }

    public function post()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperBuyerPost', 'id', 'fk_buyer_seller_post_id');
    }

    public function notification() {
        return $this->hasOne('ApiV2\Model\Notification','post_id','fk_buyer_seller_post_id');
    }

    public function checkOrder() {
        return $this->hasMany('ApiV2\Model\OrderItem','routeId','id')->where('status','>=',1)->select('routeId','id');
    }

    /** Seller Search Result Hyperlocal */
    public function postResult()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperBuyerPost', 'id', 'fk_buyer_seller_post_id');
    }

    public function quoteHyper() 
    {
       return $this->hasOne('ApiV2\Model\IntraHyperQuotaion', 'route_id', 'id')->where('lkp_service_id', '=', _HYPERLOCAL_);
    }

    public function fromLocationRes() 
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name'); 
    }

    public function toLocationRes() 
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }

    public function cityRes()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\CityModel', 'id', 'city_id')->select('id', 'city_name');
    }

    public function fromLocalitiesRes()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name');
    }

    public function toLocalitiesRes()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }
    /** Seller Search Result Hyperlocal */    

    public function quote()
    {
        return $this->hasOne('ApiV2\Model\IntraHyperQuotaion', 'route_id', 'id');
    }

    public function quotes()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperQuotaion', 'route_id', 'id');
    }

    public function fromLocation()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name');
    }

    public function toLocation()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }

    public function city()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\CityModel', 'id', 'city_id')->select('id', 'city_name');
    }

    public function fromLocalities()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name');
    }

    public function toLocalities()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }

    public function vehicleType()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\VehicleTypeModel', 'id', 'vehicle_type_id')->select('id', 'vehicle_type');
    }

  

}
