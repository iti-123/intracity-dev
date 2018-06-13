<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace ApiV2\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class SellerRegVehMach extends Model
{
    protected $table = 'bluecollar_seller_reg_vehicle_machine';

    public function vehicleType()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\MachineVehicleType', 'id', 'vm_id')->where('status', '=', 'ACTIVE');
    }
}
