<?php

namespace Api\Model;

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

    public function post()
    {
        return $this->hasOne('Api\Model\IntraHyperBuyerPost', 'id', 'fk_buyer_seller_post_id');
    }

    /** Seller Search Result Hyperlocal */
    public function postResult()
    {
        return $this->hasOne('Api\Model\IntraHyperBuyerPost', 'id', 'fk_buyer_seller_post_id');
    }

    public function quoteHyper() 
    {
       return $this->hasOne('Api\Model\IntraHyperQuotaion', 'route_id', 'id')->where('lkp_service_id', '=', _HYPERLOCAL_);
    }

    public function fromLocationRes() 
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name'); 
    }

    public function toLocationRes() 
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }

    public function cityRes()
    {
        return $this->hasOne('Api\Model\BlueCollar\CityModel', 'id', 'city_id')->select('id', 'city_name');
    }

    public function fromLocalitiesRes()
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name');
    }

    public function toLocalitiesRes()
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }
    /** Seller Search Result Hyperlocal */    

    public function quote()
    {
        return $this->hasOne('Api\Model\IntraHyperQuotaion', 'route_id', 'id');
    }

    public function fromLocation()
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name');
    }

    public function toLocation()
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }

    public function city()
    {
        return $this->hasOne('Api\Model\BlueCollar\CityModel', 'id', 'city_id')->select('id', 'city_name');
    }

    public function fromLocalities()
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'from_location')->select('id', 'locality_name');
    }

    public function toLocalities()
    {
        return $this->hasOne('Api\Model\BlueCollar\LocalityModel', 'id', 'to_location')->select('id', 'locality_name');
    }

    public function vehicleType()
    {
        return $this->hasOne('Api\Model\BlueCollar\VehicleTypeModel', 'id', 'vehicle_type_id')->select('id', 'vehicle_type');
    }


}
