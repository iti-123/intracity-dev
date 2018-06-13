<?php

namespace Api\Services\LogistiksCommonServices;

use Api\Model\Message;
use Api\Services\BlueCollar\BaseServiceProvider;
use Api\Utils\CommonComponents;
use Log;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use DB;
use Api\Model\OrderItem;
use Api\Model\Order;

class OrderServices extends BaseServiceProvider
{
    
    public static function orderMasterFilter($inputData)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $order = '';
        if ($inputData->serviceType == _INTRACITY_ || $inputData->serviceType == _HYPERLOCAL_) {
            $order=DB::table('shp_order')
                    ->leftJoin('shp_order_items as item','item.order_id','=','shp_order.id')
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('intra_hp_sellerpost_ratecart as rc', 'rc.id','=','shp_order.seller_quote_id')
                    ->select(
                        'shp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.status as orderStatus',
                        'item.seller_name as seller',
                        'rc.rate_cart_type as postType'                        
                    )->groupBy("shp_order.order_no");
        } else if($inputData->serviceType == _BLUECOLLAR_) {
            $order=DB::table('shp_order')
                    ->leftJoin('shp_order_items as item','item.order_id','=','shp_order.id')
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('bluecollar_seller_registration as bsr', 'bsr.id','=','shp_order.seller_quote_id')
                    ->select(
                        'shp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'bsr.profile_type as bcProfileType',
                        'bsr.licence_valid_from as bcLicenceValidFrom',
                        'bsr.licence_valid_to as bcLicenceValidTo',
                        'bsr.first_name as bcFirstName',
                        'bsr.last_name as bcLasttName'
                    )->groupBy("shp_order.order_no");
        }        

        
            
            $filteredOrder = self::applyFilter($order,$inputData, $userID);
        
            return response()->json([
                'isSuccessfull'=>true,
                'payload'=> EncrptionTokenService::idEncrypt($filteredOrder)
            ]);
    }

    public static function applyFilter($order, $inputData, $userID) {
        return $order->where('shp_order.buyer_id',$userID)
                    ->where('shp_order.lkp_service_id',$inputData->serviceType)
                    ->where(function($query) use($inputData) {
                        self::generateSearchQuery($query,$inputData);
                    })                    
                    ->latest()->get();
    }

    public static function arrayToArray($arr,$dbKey){
        $retArr = array();
      if(!empty($arr)){
        $s = "";
        foreach($arr as $lKey=>$value){             
          
          $retArr[]= $value[$dbKey];
        }
        return $retArr;
      }
    }

    public static function generateSearchQuery($query, $inputData) {
        if(isset($inputData['orderNumber']) && !empty($inputData['orderNumber'])) {
            $query->whereIn('shp_order.order_no',self::arrayToArray($inputData['orderNumber'],'order_no'));
        }

        if(isset($inputData['sellerType']) && !empty($inputData['sellerType'])) {
            $query->whereIn('shp_order.seller_id',self::arrayToArray($inputData['sellerType'],'id'));
        }


        if(isset($inputData['postType']) && !empty($inputData['postType'])) {
            $query->whereIn('rc.rate_cart_type',self::arrayToArray($inputData['postType'],'id'));
        }

        if(isset($inputData['orderDate']) && !empty($inputData['orderDate'])) {
            $query->whereIn('item.dispatch_date',$inputData['orderDate']);
        }
       

        if(isset($inputData['fromLocation']) && !empty($inputData['fromLocation'])) {
            foreach(self::arrayToArray($inputData['fromLocation'],'locality_name') as $key => $value) {
                $query->orWhere('item.from_location','like',$value);
            }            
        }

        if(isset($inputData['vehicleType']) && !empty($inputData['vehicleType'])) {
            $query->whereIn('item.lkp_ict_vehicle_id',self::arrayToArray($inputData['vehicleType'],'id'));
            
        }

        if(isset($inputData['toLocation']) && !empty($inputData['toLocation'])) {
            foreach(self::arrayToArray($inputData['toLocation'],'locality_name') as $key => $value) {
                $query->orWhere('item.to_location','like',$value);
            }
            
        }

        return $query;

    }
    
    public static function orderDetails($inputData)
    {   
        $orderId = EncrptionTokenService::idDecrypt($inputData['orderId']);

        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        if ($inputData->serviceType == _INTRACITY_ || $inputData->serviceType == _HYPERLOCAL_) {
            $order=DB::table('shp_order')
                    ->where('shp_order.id','=',$orderId)
                    ->leftJoin('shp_order_items as item','item.order_id','=','shp_order.id')
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('intra_hp_sellerpost_ratecart as rc', 'rc.id','=','shp_order.seller_quote_id')
                    ->select(
                        'shp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'item.status as orderStatus',
                        'rc.rate_cart_type as postType'                        
                    )->groupBy("shp_order.order_no");
        } else if($inputData->serviceType == _BLUECOLLAR_) {
            $order=DB::table('shp_order')
                    ->where('shp_order.id','=',$orderId)
                    ->leftJoin('shp_order_items as item','item.order_id','=','shp_order.id')
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('bluecollar_seller_registration as bsr', 'bsr.id','=','shp_order.seller_quote_id')
                    ->select(
                        'shp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'bsr.profile_type as bcProfileType',
                        'bsr.licence_valid_from as bcLicenceValidFrom',
                        'bsr.licence_valid_to as bcLicenceValidTo',
                        'bsr.first_name as bcFirstName',
                        'bsr.last_name as bcLasttName'
                    )->groupBy("shp_order.order_no");
        }

        $filteredOrder = self::applyFilter($order,$inputData, $userID);
    
           
        return $filteredOrder;
    }


    private static function getUser() {
        return JWTAuth::parseToken()->getPayload();
    }

    private static function hasBuyerOrSeller($query, $inputData) {
        return $query->where(function($query) use ($inputData) {            
            $user = self::getUser();            
            $role = strtolower($user->get('role'));            
                if($role == 'buyer') {
                    $query->where('shp_order.buyer_id','=',$user->get('id'));
                } else {
                    $query->where('shp_order.seller_id','=',$user->get('id'));
                }
            });
    }

}
