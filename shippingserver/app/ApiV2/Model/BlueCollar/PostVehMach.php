<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace ApiV2\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class PostVehMach extends Model
{
    public $timestamps = false;
    protected $table = 'bluecollar_posts_vehicle_machine';

    public function post()
    {
        return $this->belongsTo('ApiV2\Model\BlueCollar\Post', 'vm_id', 'post_id');
    }

    public function detail()
    {
        return $this->hasOne('ApiV2\Model\BlueCollar\MachineVehicleType', 'id', 'vm_id');
    }
}
