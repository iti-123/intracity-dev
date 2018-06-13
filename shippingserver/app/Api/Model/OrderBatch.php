<?php

namespace Api\Model;


use Illuminate\Database\Eloquent\Model;

class OrderBatch extends Model
{

    public $timestamps = true;
    protected $table = "shp_order_batch";

    public function getOrders($id, $userId)
    {

        return $this->where('id', $id)
            ->where('buyer_id', $userId)
            ->with('orders')
            ->get()
            ->first();

    }

    public function orders()
    {

        return $this->hasMany('Api\Model\Order', 'order_id', 'id');

    }


}