<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace Api\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class MachineVehicleType extends Model
{
    public $timestamps = false;
    protected $table = 'bluecollar_vehicle_machine_types';
}
