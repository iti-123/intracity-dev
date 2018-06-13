<?php

namespace ApiV2\Model;

use Illuminate\Database\Eloquent\Model;

class IntraHyperOrder extends Model
{
    protected $table = 'intra_hp_order_batch';

    public function order()
    {
        return $this->hasMany('ApiV2\Model\Order', 'order_id');
    }

    public function orderIteam()
    {
        return $this->hasMany('ApiV2\Model\OrderItem', 'order_id');
    }


}
