<?php

namespace Api\Model;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\Model;
use DB;
class CartItem extends Model
{
    protected $table= 'shp_cart_items';
    public $timestamps = false;

    public static function getCartdetailsById($cartId, $reqType) {


        $statuses = ["DRAFT", "UPDATED", "ADDED_TO_ORDERS"];
        if($reqType == 'GET') {
            $status = "DRAFT";
        }
        if($reqType == 'UPDATE') {
            $status = "DRAFT";
        }
        return
            self::where('id', $cartId)
            ->where('status', $status)
            ->get()->first();

    }

    public function getByBuyerIdAndServiceId($buyerId, $serviceId) {
        return $this
            ->where('status', "UPDATED")
            ->where('buyer_id', $buyerId)
            ->where('lkp_service_id', $serviceId)
            ->with('quoteDetails')
            ->with('postDetails')
            ->get();

    }

    public function quoteDetails() {
        return $this->hasOne('Api\Model\SellerQuotes', 'id', 'seller_quote_id');
    }

    public function postDetails() {
        return $this->hasOne('Api\Model\BuyerPost', 'id', 'buyer_post_id');
    }

    public function deleteByCartId($cartId, $buyerId, $serviceId){

        return $this
              ->where('id',$cartId)
              ->where('buyer_id',$buyerId)
              //->where('lkp_service_id',$serviceId)
              ->delete();
    }

    public function emptyCart($buyerId, $serviceId){
        $deleteStatus = $this
                ->where('buyer_id',$buyerId)
                //->where('lkp_service_id',$serviceId)
                ->delete();
        return $deleteStatus;
    }


    public function getCheckoutDetails($buyerId, $serviceId)
    {

        /*  $cart_items = $this
            ->where('buyer_id', $buyerId)
            ->where('lkp_service_id', $serviceId)
            ->get();*/

        $cart_items = $this->getByBuyerIdAndServiceId($buyerId, $serviceId);

        $paymentMethods =  DB::table('lkp_payment_methods')->get();

        $cart_items ->paymentMethods = $paymentMethods ;
        return $cart_items;
    }

    public function updateCartItemStatus($cartId) {

        $cartItem = self::find($cartId);
        $cartItem->status = "ADDED_TO_ORDERS";
        $cartItem->save();
        return true;

    }


    public static function getCartCount() {
        
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $cartQty = DB::table('shp_cart_items')
                    ->where('buyer_id', $userID)
                    ->where('status', 'ADDED_TO_ORDERS')
                    ->count();
       
          //return isJson($cartQty) ? $cartQty : 0; 
        return $cartQty; 
    }

    public function bcSellerData(){
        return $this->hasOne('Api\Model\BlueCollar\SellerRegistration', 'id', 'seller_id');
    }
}
