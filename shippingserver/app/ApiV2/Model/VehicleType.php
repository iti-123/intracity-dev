<?php
/**
 * Created by PhpStorm.
 * User: Karunya
 * Date: 04/16/17
 * Time: 7:34 PM
 */

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{

    public $timestamps = false;
    protected $table = "lkp_vehicle_types";
}
