<?php

namespace ApiV2\Controllers;
use DB;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Response;
use ApiV2\Model\IntraHyperSellerPost;
use ApiV2\Model\CartItem;
use ApiV2\Model\OrderItem;
use ApiV2\Model\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\IntraHyperOrder;
use ApiV2\Modules\Intracity\IntracityPostSearch;
use Exception;
use App\Solr;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Services\LogistiksCommonServices\OrderServices;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;

use App\ApiV2\Events\BookNow;

class OrderController extends BaseController
{

 public function orderPlace(Request $request)
    {
        //return $request;
            
        try {
            $userID = JWTAuth::parseToken()->getPayload()->get('id');
            $cartItem = CartItem::where("buyer_id",$userID)
                                // ->where("lkp_service_id",3)
                                ->where("status","ADDED_TO_ORDERS")
                               ->get();
         
            DB::beginTransaction();

           // return $cartItem;

            $order= new Order;
            foreach($cartItem as $item)
            {
              if($item->order_id){
                $order = Order::find($item->order_id);
                if (!empty($order)) {
                  $order->status = 1;                  
                }
              }
              $order->buyer_id=$item->buyer_id;
              $order->seller_id=$item->seller_id;
              $order->lkp_service_id=$item->lkp_service_id;
              $order->post_type=$item->post_type;
                  $order->seller_quote_id = $item->seller_post_item_id;
              $order->buyer_name=$item->buyer_name;
              $order->seller_name=$item->seller_name;
              $order->valid_from=$item->valid_from;
              $order->valid_to=$item->valid_to;

              $order->city_id=$item->city_id;



              $order->consignor_name=$item->buyer_consignor_name;
              $order->consignee_city=$item->buyer_consignee_city;
              
              $order->consignor_email=$item->buyer_consignor_email;
              $order->consignor_mobile=$item->buyer_consignee_mobile;
              $order->consignor_address1=$item->buyer_consignee_address;
              $order->consignor_address2=$item->buyer_consignor_address2;
              $order->consignor_address3=$item->buyer_consignor_address3;
              $order->consignor_pincode=$item->buyer_consignor_pincode;
              $order->consignor_city=$item->buyer_consignor_city;
              $order->consignor_state=$item->buyer_consignor_state;

              $order->consignee_name=$item->buyer_consignee_name;
              $order->consignee_email=$item->buyer_consignee_email;
              $order->consignee_mobile=$item->buyer_consignee_mobile;
              $order->consignee_address1=$item->buyer_consignee_address;
              $order->consignee_pincode=$item->buyer_consignee_pincode;
              $order->consignee_city=$item->buyer_consignee_city;
              $order->consignee_state=$item->buyer_consignee_state;
              
              $order->search_data=$item->search_data;
              switch ($item->lkp_service_id) {
                case _INTRACITY_:
                    $order->order_no=NumberGeneratorServices::generateTranscationId(new Order,_INTRACITY_);
                    break;
                case _HYPERLOCAL_:
                    $order->order_no=NumberGeneratorServices::generateTranscationId(new Order,_HYPERLOCAL_);
                    break;
                    case _BLUECOLLAR_:
                    $order->order_no=NumberGeneratorServices::generateTranscationId(new Order,_BLUECOLLAR_);
                    break;
                default:
                    break;
                 
              }
              $order->save();
              $id=$order->id;
             
              
              if($item->order_id){
                $OrderItem = OrderItem::where('order_id','=',$id)->first();
                $OrderItem->order_id=$id;
                $OrderItem->service_id=$item->lkp_service_id;
                switch ($item->lkp_service_id) {
                    case _INTRACITY_:
                     $OrderItem->service_name='INTRACITY';
                          break;
                    case _HYPERLOCAL_:
                $OrderItem->service_name='HYPERLOCAL';
                          break;
                    case _BLUECOLLAR_:
                        $OrderItem->service_name='BLUECOLLAR';
                          break;
                   
                }
                
                $OrderItem->buyer_id=$item->buyer_id;
                $OrderItem->buyer_name=$item->buyer_name;
                $OrderItem->seller_id=$item->seller_id;
                $OrderItem->seller_name=$item->seller_name;
                $OrderItem->consignor_name=$item->buyer_consignor_name;
                $OrderItem->consignee_name=$item->buyer_consignee_name;
                $OrderItem->service_id=$item->lkp_service_id;
                $OrderItem->price=$item->price;
                $OrderItem->pickup_date = $item->buyer_consignment_pick_up_date;
                $OrderItem->routeId = $item->rootId;
                $OrderItem->title = $item->title;
                $OrderItem->lkp_ict_vehicle_id =  $item->lkp_ict_vehicle_id;
                $OrderItem->dispatch_date =  $item->dispatch_date;
                $OrderItem->from_location=$item->from_location;
                $OrderItem->to_location=$item->to_location;
                $OrderItem->status=1;
                $OrderItem->created_ip=$request->ip();
                
                $OrderItem->save();
              }else{
                $OrderItem=new OrderItem;
                $OrderItem->order_id=$id;
                $OrderItem->service_id=$item->lkp_service_id;
                switch ($item->lkp_service_id) {
                    case _INTRACITY_:
                     $OrderItem->service_name='INTRACITY';
                          break;
                    case _HYPERLOCAL_:
                $OrderItem->service_name='HYPERLOCAL';
                          break;
                    case _BLUECOLLAR_:
                        $OrderItem->service_name='BLUECOLLAR';
                          break;
                   
                }
                $OrderItem->title = $item->title;
                $OrderItem->buyer_id=$item->buyer_id;
                $OrderItem->buyer_name=$item->buyer_name;
                $OrderItem->seller_id=$item->seller_id;
                $OrderItem->seller_name=$item->seller_name;
                $OrderItem->consignor_name=$item->buyer_consignor_name;
                $OrderItem->consignee_name=$item->buyer_consignee_name;
                $OrderItem->service_id=$item->lkp_service_id;
                $OrderItem->price=$item->price;
                $OrderItem->pickup_date = $item->buyer_consignment_pick_up_date;
                $OrderItem->routeId = $item->rootId;

                $OrderItem->lkp_ict_vehicle_id =  $item->lkp_ict_vehicle_id;
                $OrderItem->dispatch_date =  $item->dispatch_date;
                $OrderItem->from_location=$item->from_location;
                $OrderItem->to_location=$item->to_location;
                $OrderItem->status=1;
                $OrderItem->created_ip=$request->ip();
                
                $OrderItem->save();
              }
              
              
            }
           DB::commit();

           return response()->json([
            'status'=>'success',
            'payload'=>$order
        ]);

            //return IntraHyperOrder::with('order')->where('id', '=', 1)->limit(1)->get();
         }  catch(Exception $e) {
            DB::rollBack();
         LOG::error($e->getMessage());
        return $this->errorResponse($e);

         }
     }
    //orderConform

     public function orderConform(Request $request)
     {
        try {
            //ADDED_TO_ORDERS
            $userID = JWTAuth::parseToken()->getPayload()->get('id');
            $order=Order::WHERE(['buyer_id'=>$userID,'id'=>$request->orderid]);

            //$order->update(['status'=>1]);
            
//to update landmark start here
            $order_tmp_object = Order::find( $request->orderid );

            $get_landmark_value_object = CartItem::WHERE( ['buyer_id' => $order_tmp_object->buyer_id , 'seller_id' => $order_tmp_object->seller_id , 'lkp_service_id' => $order_tmp_object->lkp_service_id ] )->orderBy('id', 'DESC')->get()->first();
//to update landmark end here 


//to update landmark start here
            $order->update(['status'=>1, 'consignee_landmark' => $get_landmark_value_object->buyer_consignee_landmark ]);
//to update landmark end here 

            $cartItem = CartItem::where('buyer_id',$userID)
                        ->update(['status'=>'ORDER_CONFIRMED']);
            $orderData = $order->get()->first();
        // Notification for buyer 
            $orderData->title = 'Buyer Confirm order';
            $orderData->roleId = 1;//BUYER
            $orderData->createdBy = $orderData->buyer_id;
            event(new BookNow($orderData));
        // Notification for seller 
            $orderData->title = 'Seller Order Confirmation';
            $orderData->roleId = 2;//SELLER
            $orderData->createdBy = $orderData->seller_id;
            event(new BookNow($orderData));
            
            return response()->json([
                'status'=>'success',
                'payload'=>$order
            ]);

        }catch(Exception $e)
        {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
     }
     public function orderInfo(Request $request)
     {
        try { 
            
           $orders = Order::with(["orderItems","orderItems.seller" ,"city"])
            ->where([
                    ['id',$request->orderid],
                    ['status',1]            
                ])

                ->get()->first();
         

         //   event(new BookNow($orders));

            return response()->json([
                'status'=>'success',
                'payload'=>$orders
            ]);
                    
        } catch(Exception $e) {
          LOG::error($e->getMessage());
          return $this->errorResponse($e); 
        }
    }

    public function orderMaster($serviceId = null) {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        try {
            $order=DB::table('intra_hp_order')
                   ->leftJoin('intra_hp_order_items as item','item.order_id','=','intra_hp_order.id')
                   ->select(
                        'intra_hp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'rc.rate_cart_type as postType'
                    )
                    ->where('intra_hp_order.buyer_id',$userID)
                    ->where('intra_hp_order.lkp_service_id',$serviceId)
                    ->groupBy('intra_hp_order.id')
                    ->get();
                    //dd($order);
            return response()->json([
                'isSuccessfull'=>true,
                'payload'=> EncrptionTokenService::idEncrypt($order)
            ]);                    
        } catch(Exception $e) {
          LOG::error($e->getMessage());
          return $this->errorResponse($e); 
        }
    }

    public function orderDetails(Request $request) {
        return OrderServices::orderMasterFilter($request);

    }

    public function getOrderNumber($serviceId = null) {

        try {
            $orders = Order::where('lkp_service_id','=',$serviceId)
                ->whereNotNull("order_no")
                ->select('id','order_no')
                ->groupBy("order_no")
                ->distinct()
                ->orderBy('id',"ASC")->get();
            
            $dispatchDate = OrderItem::
                where("service_id","=",$serviceId)
                ->whereNotNull('dispatch_date')
                ->orderBy('dispatch_date',"DESC")                
                ->select('dispatch_date')
                ->groupBy("dispatch_date")
                ->distinct()
                ->get();

            return response()->json([
                'isSuccessfull'=>true,
                'payload'=> array("orderNo"=>$orders,"dispatchDate"=>$dispatchDate) 
            ]);
        } catch(Exception $e) {
          LOG::error($e->getMessage());
          return $this->errorResponse($e); 
        }
        
    }

    public function orderMasterFilter(Request $request)
    {   

      //return $request;
       return OrderServices::orderMasterFilter($request);
       
    }


    public function acceptPlaceTruckGSA(Request $request)
    {   
       return OrderServices::acceptPlaceTruckGSA($request);       
    }

    public function confirmPlaceTruck(Request $request)
    {   
       return OrderServices::confirmPlaceTruck($request);       
    }

    public function confirmConsignmentPickup(Request $request)
    {   
       return OrderServices::confirmConsignmentPickup($request);       
    }

    public function confirmTransitDetail(Request $request)
    {   
       return OrderServices::confirmTransitDetail($request);       
    }

    public function consignmentDeliveryDetails(Request $request)
    {   
       return OrderServices::consignmentDeliveryDetails($request);       
    }

    public function fetchOrderDocument(Request $request)
    {   
       return DocumentServices::fetchOrderDocument($request);       
    }

    public function downloadOrderDocument(Request $request)
    {   
       return DocumentServices::generateInvoice($request->data);       
    }
    
    public function emailOrderDocument(Request $request)
    {   
       return DocumentServices::emailOrderDocument($request);       
    }

    // Confirm delivery by buyer
    
    public function confirmDelivery(Request $request)
    {   
       return OrderServices::confirmDelivery($request);       
    }
    
}