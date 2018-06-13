<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Model\Message;
use ApiV2\Services\BlueCollar\BaseServiceProvider;
use ApiV2\Utils\CommonComponents;
use Log;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use DB;
use ApiV2\Model\OrderItem;
use ApiV2\Model\Order;
use ApiV2\Model\BuyerDetail;

use ApiV2\Services\LogistiksCommonServices\DocumentServices;
class OrderServices extends BaseServiceProvider
{
    
    public static function orderMasterFilter($inputData)
    {

  //return $inputData;

        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $orderId = '';
        if(isset($inputData['orderId']) && !empty($inputData['orderId'])){
            $orderId = EncrptionTokenService::idDecrypt($inputData['orderId']);
        }

        
        $order = '';
        if ($inputData->serviceType == _INTRACITY_ || $inputData->serviceType == _HYPERLOCAL_) {
            $order=DB::table('intra_hp_order')
                    ->leftJoin('intra_hp_order_items as item','item.order_id','=','intra_hp_order.id')
                    ->leftjoin('intra_hp_post_quotations as quote', function($join) {
                        $join->on('quote.route_id','=','item.routeId');
                    })
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->where(function($query) use($orderId) {
                        if(isset($orderId) && !empty($orderId)){
                            $query->where('item.order_id','=',$orderId);
                        }
                    })
                    
                    ->leftjoin('intra_hp_buyer_seller_routes as rk', 'rk.id','=','item.routeId')
                    ->where(function($query) use ($inputData){
                        if(isset($inputData['city']['id']) && !empty($inputData['city']['id'])){
                            $query->where('rk.city_id','=',$inputData['city']['id']);
                        }                       
                    })
                    ->leftjoin('seller_details as seller', 'seller.user_id', '=', 'item.seller_id')
                    ->select(
                        'intra_hp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.id as itemId',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'item.status as orderItemStatus',
                        'item.truck_attribute as truckAttribute',
                        'item.is_gsa as isGSA',
                        'item.consignment_details as consignmentPickupDetails',
                        'item.transit_detail as transitDetail',
                        'item.delivery_detail as deliveryDetail',
                        'item.routeId',
                        'item.pickup_date as pickUpDate',
                        'item.title as rateCardTitle',
                        'rk.type_basis as postType',
                        'seller.established_in as sellerEstablished',
                        'seller.gta as sellerGta',
                        'seller.tin as sellerTin',
                        'seller.service_tax_number as sellerTaxNumber',
                        'seller.principal_place as placeOfBusiness',
                        'seller.contact_landline as landline',
                        'seller.contact_mobile as sellerMobile',
                        'seller.contact_email as sellerEmail',
                        DB::raw("(select name FROM seller_details WHERE user_id=item.seller_id ) as seller_name"),
                        DB::raw("(select concat(firstname,' ',lastname) FROM buyer_details WHERE user_id=item.buyer_id ) as buyer_name"),
                        DB::raw("(select load_type FROM lkp_load_types WHERE id=rk.material_type ) as loadType"),
                        DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=item.lkp_ict_vehicle_id ) as vehicleType")
                    );
        } else if($inputData->serviceType == _BLUECOLLAR_) {
            $order=DB::table('intra_hp_order')
                    ->leftJoin('intra_hp_order_items as item','item.order_id','=','intra_hp_order.id')
                    ->where(function($query) use ($inputData){
                        // static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('bluecollar_seller_registration as bsr', 'bsr.id','=','intra_hp_order.seller_quote_id')
                    ->select(
                        'intra_hp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.id as itemId',
                        'item.status as orderItemStatus',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'item.is_gsa as isGSA',
                        'bsr.profile_type as bcProfileType',
                        'bsr.licence_valid_from as bcLicenceValidFrom',
                        'bsr.licence_valid_to as bcLicenceValidTo',
                        'bsr.first_name as bcFirstName',
                        'bsr.last_name as bcLasttName'
                    )->groupBy("intra_hp_order.order_no");
        }        

      
            
            $filteredOrder = self::applyFilter($order,$inputData, $userID);
            $query = DB::getQueryLog();
           //print_r($query);
            return response()->json([
                'isSuccessfull'=>true,
                'orderId'=> $orderId,
                'payload'=> EncrptionTokenService::idEncrypt($filteredOrder)
            ]);
    }

    public static function applyFilter($order, $inputData, $userID) {
        //DB::enableQueryLog();
        return $order->where('intra_hp_order.lkp_service_id',$inputData->serviceType)
                    ->where(function($query) use($inputData) {
                         if (isset($inputData['orderNumber']) && !empty($inputData['orderNumber'])) {
                            foreach (self::arrayToArray($inputData['orderNumber'],'order_no') as $key => $value) {
                                if ($key == 0) {
                                    $query->where('intra_hp_order.order_no', '=', $value);
                                } else {
                                    $query->orWhere('intra_hp_order.order_no', '=', $value);
                                }
                            }    
                        }
                    })  
                    ->where(function($query) use($inputData) {
                         if (isset($inputData['postType']) && !empty($inputData['postType'])) {
                            foreach (self::arrayToArray($inputData['postType'],'id') as $key => $value) {
                                if ($key == 0) {
                                    $query->where('intra_hp_order.post_type', '=', $value);
                                } else {
                                    $query->orWhere('intra_hp_order.post_type', '=', $value);
                                }
                            }    
                        }
                    })
                    ->where(function($query) use($inputData) {
                         if (isset($inputData['sellerType']) && !empty($inputData['sellerType'])) {
                            foreach (self::arrayToArray($inputData['sellerType'],'id') as $key => $value) {
                                if ($key == 0) {
                                    $query->where('intra_hp_order.seller_id', '=', $value);
                                } else {
                                    $query->orWhere('intra_hp_order.seller_id', '=', $value);
                                }
                            }    
                        }
                    }) 
                    ->where(function($query) use($inputData) {
                         if (isset($inputData['orderDate']) && !empty($inputData['orderDate'])) {
                            foreach ($inputData['orderDate'] as $key => $value) {
                                if ($key == 0) {
                                    $query->where('item.dispatch_date', '=', $value);
                                } else {
                                    $query->orWhere('item.dispatch_date', '=', $value);
                                }
                            }    
                        }
                    }) 
                    ->where(function($query) use($inputData) {
                        if (isset($inputData['vehicleType']) && !empty($inputData['vehicleType'])) {
                            foreach(self::arrayToArray($inputData['vehicleType'],'id')  as $key => $value) {
                                if ($key == 0) {
                                    $query->where('item.lkp_ict_vehicle_id', '=', $value);
                                } else {
                                    $query->orWhere('item.lkp_ict_vehicle_id', '=', $value);
                                }
                            }    
                        }
                    })       
                    ->where(function($query) use($inputData) {
                        if (isset($inputData['fromLocation']) && !empty($inputData['fromLocation'])) {
                            foreach(self::arrayToArray($inputData['fromLocation'],'locality_name')  as $key => $value) {
                                if ($key == 0) {
                                    $query->where('item.from_location', '=', $value);
                                } else {
                                    $query->orWhere('item.from_location', '=', $value);
                                }
                            }    
                        }
                    })  
                    ->where(function($query) use($inputData) {
                        if (isset($inputData['toLocation']) && !empty($inputData['toLocation'])) {
                            foreach(self::arrayToArray($inputData['toLocation'],'locality_name')  as $key => $value) {
                                if ($key == 0) {
                                    $query->where('item.to_location', '=', $value);
                                } else {
                                    $query->orWhere('item.to_location', '=', $value);
                                }
                            }    
                        }
                    })    
                    ->where(function($query) use($inputData) {
                      if(isset($inputData['fromLocation']) && !empty($inputData['fromLocation']) && !empty($inputData['toLocation'])) {
                        foreach($inputData['fromLocation'] as $key => $value) {
                           $fromLocations[] = $value['locality_name'];
                        }

                        foreach($inputData['toLocation'] as $key => $value) {
                           $toLocations[] = $value['locality_name'];
                        } 
                        
                        foreach($fromLocations as $key => $val) {
                            for($i=0;$i<count($toLocations);$i++){
                                 if($key == 0 && $i == 0){
                                     $query->where('item.from_location','like',$val)
                                           ->where('item.to_location','like',$toLocations[$i]);
                                  }else{
                                     $query->orWhere('item.from_location','like',$val)
                                           ->where('item.to_location','like',$toLocations[$i]);
                                  }
                            } 
                        }      
                      }
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
            $query->whereIn('intra_hp_order.order_no',self::arrayToArray($inputData['orderNumber'],'order_no'));
        }

        if(isset($inputData['sellerType']) && !empty($inputData['sellerType'])) {
            $query->whereIn('intra_hp_order.seller_id',self::arrayToArray($inputData['sellerType'],'id'));
        }


        if(isset($inputData['postType']) && !empty($inputData['postType'])) {
            $query->whereIn('intra_hp_order.post_type',self::arrayToArray($inputData['postType'],'id'));
        }

        if(isset($inputData['orderDate']) && !empty($inputData['orderDate'])) {
            $query->whereIn('item.dispatch_date',$inputData['orderDate']);
        }
       

        if(isset($inputData['fromLocation']) && !empty($inputData['fromLocation']) && empty($inputData['toLocation'])) {
            foreach(self::arrayToArray($inputData['fromLocation'],'locality_name') as $key => $value) {
              if($key == 0){
                 $query->where('item.from_location','like',$value);
              }else{
                 $query->orWhere('item.from_location','like',$value);
              }
           }            
        }

        if(isset($inputData['vehicleType']) && !empty($inputData['vehicleType'])) {
            foreach(self::arrayToArray($inputData['vehicleType'],'id') as $key => $value) {
              if($key == 0){
                 $query->where('item.lkp_ict_vehicle_id','like',$value);
              }else{
                 $query->orWhere('item.lkp_ict_vehicle_id','like',$value);
              }
           }
        
        }
        
        if(isset($inputData['fromLocation']) && !empty($inputData['fromLocation']) && !empty($inputData['toLocation'])) {
            foreach($inputData['fromLocation'] as $key => $value) {
               $fromLocations[] = $value['locality_name'];
            }

            foreach($inputData['toLocation'] as $key => $value) {
               $toLocations[] = $value['locality_name'];
            } 
            
            foreach($fromLocations as $key => $val) {
                for($i=0;$i<count($toLocations);$i++){
                     if($key == 0 && $i == 0){
                         $query->where('item.from_location','like',$val)
                               ->where('item.to_location','like',$toLocations[$i]);
                      }else{
                         $query->orWhere('item.from_location','like',$val)
                               ->where('item.to_location','like',$toLocations[$i]);
                      }
                } 
            }     
                   
        }

        if(isset($inputData['toLocation']) && !empty($inputData['toLocation'])  && empty($inputData['fromLocation'])) {
            foreach(self::arrayToArray($inputData['toLocation'],'locality_name') as $key => $value) {
                $query->orWhere('item.to_location','like',$value);
            }   
        }

        return $query;

    }
    
    public static function orderDetailsTest($inputData)
    {   
        $orderId = EncrptionTokenService::idDecrypt($inputData['orderId']);

        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        if ($inputData->serviceType == _INTRACITY_ || $inputData->serviceType == _HYPERLOCAL_) {
            $order=DB::table('intra_hp_order')
                    ->where('intra_hp_order.id','=',$orderId)
                    ->leftJoin('intra_hp_order_items as item','item.order_id','=','intra_hp_order.id')
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('intra_hp_sellerpost_ratecart as rc', 'rc.id','=','intra_hp_order.seller_quote_id')
                    ->select(
                        'intra_hp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'item.is_gsa as isGSA',
                        'rc.rate_cart_type as postType'                        
                    )->groupBy("intra_hp_order.order_no");
        } else if($inputData->serviceType == _BLUECOLLAR_) {
            $order=DB::table('intra_hp_order')
                    ->where('intra_hp_order.id','=',$orderId)
                    ->leftJoin('intra_hp_order_items as item','item.order_id','=','intra_hp_order.id')
                    ->where(function($query) use ($inputData){
                        static::hasBuyerOrSeller($query, $inputData);                       
                    })
                    ->join('bluecollar_seller_registration as bsr', 'bsr.id','=','intra_hp_order.seller_quote_id')
                    ->select(
                        'intra_hp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'item.is_gsa as isGSA',
                        'bsr.profile_type as bcProfileType',
                        'bsr.licence_valid_from as bcLicenceValidFrom',
                        'bsr.licence_valid_to as bcLicenceValidTo',
                        'bsr.first_name as bcFirstName',
                        'bsr.last_name as bcLasttName'
                    )->groupBy("intra_hp_order.order_no");
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
                    $query->where('intra_hp_order.buyer_id','=',$user->get('id'));
                } else {
                    $query->where('intra_hp_order.seller_id','=',$user->get('id'));
                }
            });
    }


    public static function acceptPlaceTruckGSA($request = null)
    {
        if(!empty($request)) {
            $input = json_decode($request->data);
            $orderId = $input->itemId;
            try {
                $order = OrderItem::where('id',$orderId)->update([
                    'is_gsa'=>1
                ]);
                return response()->json([
                    'isSuccessfull'=>true,
                    'payload'=> $input
                ]);
            } catch(Exception $e) {

            }           
        }
    }


    public static function confirmPlaceTruck($request = null)
    {
        if(!empty($request)) {
           
            $truckAttribute = json_encode($request['data']['truck_attribute']);
            
            $orderId = $request['data']['itemId'];
            try {

                $order = OrderItem::where('id',$orderId)->update([
                    'truck_attribute'=>$truckAttribute,
                    'status' => TRUCK_PLACED
                ]);

                return response()->json([
                    'isSuccessfull'=>true,
                    'payload'=> $truckAttribute
                ]);
            } catch(Exception $e) {

            }           
        }
    }


    public static function confirmConsignmentPickup($request = null)
    {

        if(!empty($request)) {
           
            $consignment = json_encode($request['data']['consignment']);
            
            $orderId = $request['data']['itemId'];

            try {
                $status = static::checkStatus($orderId);
                $isSuccess = false;
                $message = '';
                if($status == 'UPDATE_CONSIGNMENT') {
                    $order = OrderItem::where('id',$orderId)->update([
                        'consignment_details'=>$consignment,
                        'status' => CONSIGNMENT_DETAIL_CONFIRMED
                    ]);

                    $isSuccess = true;
                    
                    $message = 'Consignment Details successfully updated';

                } elseif($status == 'UPDATED_CONSIGNMENT_DETAIL') {
                    $isSuccess = false;
                    $message = 'Consignment Details already updated';
                } else {
                    $isSuccess = false;
                    $message = 'You are not eligible to update consignment detail';
                }
                
                return response()->json([
                    'isSuccessfull'=>$isSuccess,
                    'message'=>$message,
                    'payload'=> $consignment
                ]);
                
            } catch(Exception $e) {

            }           
        }
    }

    public static function confirmTransitDetail($request = null)
    {

        if(!empty($request)) {
           
            $transitDetail = json_encode($request['data']['transit_detail']);
            
            $orderId = $request['data']['itemId'];

            try {
                $status = static::checkStatus($orderId);
                $isSuccess = false;
                $message = '';
                if($status == 'UPDATED_CONSIGNMENT_DETAIL') {
                    $order = OrderItem::where('id',$orderId)->update([
                        'transit_detail'=>$transitDetail,
                        'status' => TRANSIT_DETAIL_CONFIRMED
                    ]);

                    $isSuccess = true;
                    
                    $message = 'Consignment Details successfully updated';

                } elseif($status == 'TRANSIT_DETAIL_CONFIRMED') {
                    $isSuccess = false;
                    $message = 'Transit Details already updated';
                } else {
                    $isSuccess = false;
                    $message = 'You are not eligible to update transit detail';
                }
                
                return response()->json([
                    'isSuccessfull'=>$isSuccess,
                    'message'=>$message,
                    'payload'=> $transitDetail
                ]);
                
            } catch(Exception $e) {

            }           
        }
    }

    public static function consignmentDeliveryDetails($request = null)
    {

        if(!empty($request)) {
           
            $deliveryDetail = json_encode($request['data']['delivery_detail']);
            
            $orderId = $request['data']['itemId'];
            $billingDetail = BuyerDetail::where("user_id","=",$request['data']['buyer_id'])
            ->select(
                DB::raw('concat(firstname, " ", lastname) as fullname'),
                "mobile","contact_email","address1","address2","address3","pincode","lkp_city_id"
                )                
            ->first();
            $data = $request['data'];
            $data['billingDetail'] = $billingDetail;
            // $c =  json_encode($data);
            // return $data['consignmentPickupDetails']['consignmentPickupDate'];
            try {
                $status = static::checkStatus($orderId);
                $isSuccess = false;
                $message = '';
                if($status == 'TRANSIT_DETAIL_CONFIRMED') {
                    $order = OrderItem::where('id',$orderId)->update([
                        'delivery_detail'=>$deliveryDetail,
                        'status' => DELIVERY_DETAIL_CONFIRMED
                    ]);

                    $isSuccess = true;
                    
                    // Generate Invoice automatically                    
                    
                    DocumentServices::generateInvoice($data);

                    $message = 'Consignment Details successfully updated';

                } elseif($status == 'DELIVERY_DETAIL_CONFIRMED') {
                    $isSuccess = false;
                    $message = 'Delivery Details already updated';
                } else {
                    $isSuccess = false;
                    $message = 'You are not eligible to update Delivery detail';
                }
                
                return response()->json([
                    'isSuccessfull'=>$isSuccess,
                    'message'=>$message,
                    'payload'=> $deliveryDetail
                ]);
                
            } catch(Exception $e) {

            }           
        }
    }

    public static function confirmDelivery($request = null)
    {

        if(!empty($request)) {
           
            $orderId = $request['data']['itemId'];

            try {
                $status = static::checkStatus($orderId);
                $isSuccess = false;
                $message = '';
            // Check if delivery detail is confirmed by seller    
                if($status == 'DELIVERY_DETAIL_CONFIRMED') {
                    $order = OrderItem::where('id',$orderId)->update([                        
                        'status' => DELIVERY_CONFIRMED_BY_BUYER
                    ]); // then update "confirm delivery by Buyer"
                    $isSuccess = true;
                    $message = 'Delivery Details successfully updated';

                }
                
                return response()->json([
                    'isSuccessfull'=>$isSuccess,
                    'message'=>$message,
                    'payload'=> $request['data']
                ]);
                
            } catch(Exception $e) {

            }           
        }
    }

    public static function checkStatus($orderId) {
        $status = OrderItem::find($orderId)->status;
        $s = '';
        if($status == 1) {
            $s = 'UPDATE_VEHICLE';
        }

        if($status == 2) {
            $s = 'UPDATE_CONSIGNMENT';
        }
        if($status == 3) {
            $s = 'UPDATED_CONSIGNMENT_DETAIL';
        }

        if($status == 4) {
            $s = 'TRANSIT_DETAIL_CONFIRMED';
        }

        if($status == 5) {
            $s = 'DELIVERY_DETAIL_CONFIRMED';
        }

        if($status == 6) {
            $s = 'DELIVERY_CONFIRMED_BY_BUYER';
        }


        return $s;

    }


    

}
