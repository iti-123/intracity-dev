<?php

namespace Api\Controllers;
use DB;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Response;
use Api\Model\IntraHyperSellerPost;
use Api\Model\CartItem;
use Api\Model\OrderItem;
use Api\Model\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Model\IntraHyperOrder;
use Api\Modules\Intracity\IntracityPostSearch;
use Exception;
use App\Solr;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use Api\Services\LogistiksCommonServices\OrderServices;

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

            //return $cartItem;
            $order= new Order;
            foreach($cartItem as $item)
            {
              
              $order->buyer_id=$item->buyer_id;
              $order->seller_id=$item->seller_id;
              $order->lkp_service_id=$item->lkp_service_id;
                  $order->seller_quote_id = $item->seller_post_item_id;
              $order->buyer_name=$item->buyer_name;
              $order->seller_name=$item->seller_name;
              $order->valid_from=$item->valid_from;
              $order->valid_to=$item->valid_to;
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
              $order->search_data=$item->search_data;
              switch ($item->lkp_service_id) {
                case 3:
                    $order->order_no=NumberGeneratorServices::generateTranscationId(new Order,_INTRACITY_);
                    break;
                case 22:
                    $order->order_no=NumberGeneratorServices::generateTranscationId(new Order,_HYPERLOCAL_);
                    break;
                    case 23:
                    $order->order_no=NumberGeneratorServices::generateTranscationId(new Order,_BLUECOLLAR_);
                    break;
                default:
                    break;
                 
              }
              $order->save();
              $id=$order->id;
              $OrderItem=new OrderItem;
              $OrderItem->order_id=$id;
              $OrderItem->service_id=$item->lkp_service_id;
              switch ($item->lkp_service_id) {
                  case 3:
                   $OrderItem->service_name='INTRACITY';
                        break;
                  case 22:
              $OrderItem->service_name='HYPERLOCAL';
                        break;
                  case 23:
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

              $OrderItem->lkp_ict_vehicle_id =  $item->lkp_ict_vehicle_id;
              $OrderItem->dispatch_date =  $item->dispatch_date;
              $OrderItem->from_location=$item->from_location;
              $OrderItem->to_location=$item->to_location;
              $OrderItem->created_ip=$request->ip();
              
              $OrderItem->save();
              
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
            $order=Order::WHERE(['buyer_id'=>$userID,'id'=>$request->orderid])
                   ->update(['status'=>1]);
            $cartItem = CartItem::where('buyer_id',$userID)
                        ->update(['status'=>'ORDER_CONFIRMED']);
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
        try{
            $order=DB::table('shp_order')
                    ->leftJoin('shp_order_items as item','item.order_id','=','shp_order.id')
                   
                    ->where('shp_order.id',$request->orderid)
                   ->select('shp_order.*',
                    'item.service_name as service',
                    'item.price',
                    'item.service_name',
                    'item.seller_name as seller'
                    )
                    ->get();
                    //dd($order);
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

    public function orderMaster($serviceId = null) {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        try{
            $order=DB::table('shp_order')
                   ->leftJoin('shp_order_items as item','item.order_id','=','shp_order.id')
                   ->join('intra_hp_sellerpost_ratecart as rc', 'rc.id','=','shp_order.seller_quote_id')
                   ->select(
                        'shp_order.*',
                        'item.service_name as service',
                        'item.price',
                        'item.to_location',
                        'item.from_location',
                        'item.service_name',
                        'item.seller_name as seller',
                        'rc.rate_cart_type as postType'
                    )
                    ->where('shp_order.buyer_id',$userID)
                    ->where('shp_order.lkp_service_id',$serviceId)
                    ->groupBy('shp_order.id')
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
        return response()->json([
            'isSuccessfull'=>true,
            'payload'=>OrderServices::orderDetails($request)
        ]);

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
       return OrderServices::orderMasterFilter($request);
       
    }

    
   
   
}