<?php

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;

class IntraHyperOrder extends Model
{
    protected $table = 'shp_order_batch';

    public function order()
    {
        return $this->hasMany('Api\Model\Order', 'order_id');
    }

    public function orderIteam()
    {
        return $this->hasMany('Api\Model\OrderItem', 'order_id');
    }


}
