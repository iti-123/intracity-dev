<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/21/17
 * Time: 7:34 PM
 */

namespace Api\Model;


use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public $timestamps = true;
    protected $table = "shp_order";

    public function quoteDetails()
    {
        return $this->hasOne('Api\Model\SellerQuotes', 'id', 'seller_quote_id');
    }

    public function postDetails()
    {
        return $this->hasOne('Api\Model\BuyerPost', 'id', 'buyer_post_id');
    }

    public function getOrdersByUserId($userType, $userId)
    {
        if ($userType == 'seller') {
            return $this
                ->where('seller_id', $userId)
                ->where('payment_status', 'SUCCESS')
                ->with('getSellerPostDetails')
                ->with('quoteDetails')
                ->with('postDetails')
                ->get();


        } else {
            return $this->where('buyer_id', $userId)
                ->where('payment_status', 'SUCCESS')
                ->with('getSellerPostDetails')
                ->with('quoteDetails')
                ->with('postDetails')
                ->get();
        }
    }

    public function getSellerPostDetails()
    {
        return $this->hasOne('Api\Model\SellerPost', 'id', 'seller_quote_id');
    }

    public function getOrderItemDetailsById($orderId, $userId)
    {

        return $this->where('id', $orderId)
            ->where(function ($q) use ($userId) {
                $q->where('seller_id', $userId)
                    ->orWhere('buyer_id', $userId);
            })
            /*
            ->with('postDetails')
            ->with('quoteDetails')
            */
            ->with('documentDetails')
            ->with('mileStoneDetails')
            ->get()
            ->first();

    }

    public function documentDetails()
    {
        return $this->hasMany('Api\Model\OrderDoc', 'order_id', 'id');
    }

    public function mileStoneDetails()
    {
        return $this->hasMany('Api\Model\OrderMilestone', 'order_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany('Api\Model\OrderItem', 'order_id', 'id');
    }

    public function rateCart()
    {
        return $this->hasOne('Api\Model\SellerpostRatecart', 'id', 'seller_quote_id')->select('id','rate_cart_type');
    }

}