<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/21/17
 * Time: 7:34 PM
 */

namespace ApiV2\Model;


use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    public $table = "intra_hp_order_items";
    public $timestamps = false;
    
    public function seller() {
        return $this->hasOne('ApiV2\Model\SellerDetail', 'user_id', 'seller_id')->select('user_id','name');
    }
}