<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:03 AM
 */

namespace Api\Model\BlueCollar;

use Illuminate\Database\Eloquent\Model;

class PostVehMach extends Model
{
    public $timestamps = false;
    protected $table = 'bluecollar_posts_vehicle_machine';

    public function post()4
    {
        return $this->belongsTo('Api\Model\BlueCollar\Post', 'vm_id', 'post_id');
    }

    public function detail()
    {
        return $this->hasOne('Api\Model\BlueCollar\MachineVehicleType', 'id', 'vm_id');
    }
}
