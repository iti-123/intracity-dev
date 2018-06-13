<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace Api\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    protected $table = 'lkp_cities';

    public function state()
    {
        return $this->hasOne('Api\Model\BlueCollar\StateModel', 'id', 'lkp_state_id')->select('id', 'state_name');
    }
}

class LocalityModel extends Model
{
    protected $table = 'lkp_localities';

}

class VehicleTypeModel extends Model
{
    protected $table = 'lkp_vehicle_types';

}
