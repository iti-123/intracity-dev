<?php

namespace ApiV2\Controllers;

use ApiV2\Model\CartItem;
use ApiV2\Model\PaymentLog;
use ApiV2\Requests\BookNowRequest as BookingRequest;
use ApiV2\Requests\IntraHyperBuyerPostRequest as PostRequest;
use ApiV2\Services\CartItemService;
use ApiV2\Model\BlueCollar\SellerRegistration;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

class IntracityCartItemsController extends BaseController
{

    public function __construct()
    {

    }

    public function addInitialCartDetails(Request $request)
    {
        $bo = json_decode($request->data);

      //  return json_encode($bo);
       // return json_encode($bo->initialDetails);
        $serviceId = $bo->initialDetails->serviceId;
         
        if($serviceId == _INTRACITY_ || $serviceId == _HYPERLOCAL_){
          
            $route = $bo->initialDetails->quote;
            //    return json_encode(PostRequest::has($route, 'id'));

            $routeId = '';
            $gettype=gettype(PostRequest::has($route, 'id'));

            if ($serviceId == _INTRACITY_) {
                if ($gettype == 'integer') {
                   $routeId = PostRequest::has($route, 'id');
                } else {
                   $routeId = EncrptionTokenService::idDecrypt(PostRequest::has($route, 'id'));
                }
            }
            
           if(self::checkCartValue($routeId, $serviceId)) {                
                return response()->json([
                    'isSuccessful' => true, 
                    'payload' => array('enc_id'=>PostRequest::has($route, 'id')),
                    'isCartValue' => true
                ]);            
           }
           
           
           $gettype = gettype($bo->initialDetails->quote->id);
            if(isset($bo->initialDetails->quote->id)){
               if($gettype=='integer')
               {
                $bo->initialDetails->quote->id =$bo->initialDetails->quote->id;
               }else{
                $bo->initialDetails->quote->id = EncrptionTokenService::idDecrypt($bo->initialDetails->quote->id);
               }
           }
        }elseif ($serviceId == _BLUECOLLAR_) {
            //return $bo->initialDetails->quote;
          if(isset($bo->initialDetails->quote->id)){
              $bo->initialDetails->quote->id = EncrptionTokenService::idDecrypt($bo->initialDetails->quote->id);}
        
        }
      
		
        $firstname = JWTAuth::parseToken()->getPayload()->get('firstname');

        //return $firstname;
        LOG::info('add initial intracity cart items');

        $model = new CartItem();
        
        switch ($serviceId) {
            case _INTRACITY_:
                $d = $bo->initialDetails->searchData->data;
                $fromlocation = PostRequest::has($d,'fromLocation')?$d->fromLocation->locality_name:'';
                
                $tolocation = PostRequest::has($d,'toLocation')?$d->toLocation->locality_name:'';
                 
                $dispatchDate = PostRequest::has($d,'dispatchDate')?$d->dispatchDate:date("d-m-Y");
                $dispatchDate=date("Y-m-d", strtotime($dispatchDate));
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id = $bo->initialDetails->sellerId;
                $model->lkp_service_id = _INTRACITY_;
                $model->service_type = $bo->initialDetails->serviceType;
                $model->buyer_post_id = PostRequest::has($bo->initialDetails, 'buyerPostId');
                $model->seller_post_item_id = $bo->initialDetails->sellerQuoteId;
                $model->status = 'DRAFT';
                
                $model->city_name="";

               // $model->post_type = $bo->initialDetails->postType;
                $model->lead_type = PostRequest::has($bo, 'leadType');
                $model->buyer_name = $firstname;
                
                $quote = $bo->initialDetails->quote;
                /**************price calculation *************/

                $model->city_id=$quote->city_id;
               



                $price = '';
                if(!isset($quote->lead_type)){
                    if((int)$quote->type_basis) {
                        $price = PostRequest::has($quote, 'finalPrice');
                    } else {
                        $base_distance = PostRequest::has($quote, 'base_distance');
                        $rate_base_distance = PostRequest::has($quote, 'rate_base_distance');
                        if ((int)$quote->type_basis == INTRA_HYPER_DISTANCE) {
                            
                            $price = PostRequest::has($quote, 'finalPrice');
                            
                        }
                        
                        if ((int)$quote->type_basis == INTRA_HYPER_HOURS) {
                            $base_time = PostRequest::has($quote, 'base_time');
                            $cost_base_time = PostRequest::has($quote, 'cost_base_time');
                            $price = $base_time * $cost_base_time;
                        }
                    }
                }
                $model->post_type = PostRequest::has($d,'type')?$d->type:'';    

                if(!isset($quote->lead_type)){  
                  $model->price = $price;
                }else{
                  $model->price = $quote->price;  
                }

                $model->from_location =  $fromlocation;
                $model->to_location =  $tolocation;
                $model->lkp_ict_vehicle_id = PostRequest::has($quote, 'vehicle_type_id');

                if(isset($bo->initialDetails->quote->isIndent) && $bo->initialDetails->quote->isIndent == 1){
                    $model->title = $bo->initialDetails->quote->rateCardTitle;
                }else{
                    $model->title = PostRequest::has($quote,'title');
                }
               // $model->title = PostRequest::has($quote,'title');

                $model->seller_name = PostRequest::has($quote, 'seller');
                $model->rootId = PostRequest::has($quote, 'id');
                $model->tracking_type = PostRequest::has($quote, 'tracking');
                $model->transit_hour = PostRequest::has($quote, 'transit_hour');
                $model->quantity = PostRequest::has($quote, 'qty');
                $model->material_type = PostRequest::has($quote, 'material_type');
                $model->order_id = property_exists($bo->initialDetails->quote,'orderId')?EncrptionTokenService::idDecrypt($bo->initialDetails->quote->orderId):'';
                $model->dispatch_date = $dispatchDate;

                $model->search_data = json_encode($bo->initialDetails->searchData);

                if (isset($quote->valid_from))
                    $model->valid_from = $quote->valid_from;
                if (isset($quote->valid_to))
                    $model->valid_to = $quote->valid_to;
                //return $model;
                break;


            case _HYPERLOCAL_:
                // return json_encode($bo->initialDetails);
                $l = $bo->initialDetails->searchData->data;
                $fromlocation = property_exists($l,'location')? $l->location[0]->fromLocation->locality_name:'';
                $tolocation = property_exists($l,'location')?$l->location[0]->tolocation->locality_name:'';
                $model->lkp_service_id = _HYPERLOCAL_;
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id = $bo->initialDetails->sellerId;
                $model->search_data = json_encode($bo->initialDetails->searchData);
                $model->price = $bo->initialDetails->quote->price;
                $model->buyer_name = $firstname;
                $model->seller_post_item_id = $bo->initialDetails->quote->id;
                $model->status = 'DRAFT';

                $model->city_id = $bo->initialDetails->quote->city_id;
                $model->rootId =  $bo->initialDetails->quote->id;
                $model->created_ip = $request->ip();
                $model->valid_from = $bo->initialDetails->quote->from_date;
                $model->valid_to = $bo->initialDetails->quote->to_date;
                $model->from_location = $fromlocation;
                $model->to_location = $tolocation;
                $model->seller_id = $bo->initialDetails->quote->posted_by;
                $model->seller_name = $bo->initialDetails->quote->vendor;

                if(isset($bo->initialDetails->quote->is_indent) && $bo->initialDetails->quote->is_indent == 1){
                    $model->title = $bo->initialDetails->quote->rateCardTitle;
                }else{
                    $model->title = PostRequest::has($bo->initialDetails->quote,'title');
                }
                
                $model->order_id = property_exists($bo->initialDetails->quote,'orderId')?EncrptionTokenService::idDecrypt($bo->initialDetails->quote->orderId):'';
                $model->is_indent = property_exists($bo->initialDetails->quote,'isIndent')?$bo->initialDetails->quote->isIndent:'';
                break;
            case _BLUECOLLAR_ :
                $model->lkp_service_id= _BLUECOLLAR_;
                $model->buyer_id = $bo->initialDetails->buyerId;

                $model->search_data = json_encode($bo->initialDetails->searchData);
                $model->price =$bo->initialDetails->searchData->BlueCollarbookData->seller_salary;
                if(gettype($bo->initialDetails->searchData->BlueCollarbookData->id) == 'integer'){
                   $model->buyer_post_id = $bo->initialDetails->searchData->BlueCollarbookData->id;
                }else{
                   $model->buyer_post_id = EncrptionTokenService::idDecrypt($bo->initialDetails->searchData->BlueCollarbookData->id);
                }
                $model->status = 'DRAFT';
                if(gettype($bo->initialDetails->searchData->BlueCollarbookData->id) == 'integer'){
                    $model->buyer_quote_item_id = $bo->initialDetails->searchData->BlueCollarbookData->id;
                    $selRegId = DB::table('bluecollar_seller_registration')
                                 ->select('id')
                                 ->where('created_by','=',$bo->initialDetails->sellerId)
                                 ->first()->id;
                    $model->seller_id= $selRegId;
                }else{
                    $model->buyer_quote_item_id = EncrptionTokenService::idDecrypt($bo->initialDetails->searchData->BlueCollarbookData->id);
                    $model->seller_id= $bo->initialDetails->sellerId;
                }
                    
                $model->buyer_name = $firstname;
                if(isset($bo->initialDetails->searchData->BlueCollarbookData->seller_first_name)){
                    $model->seller_name = $bo->initialDetails->searchData->BlueCollarbookData->seller_first_name;
                }else{
                    $model->seller_name = '';
                }
                if(isset($bo->initialDetails->searchData->BlueCollarbookData->seller_bc_reg_id)){
                    $model->seller_post_item_id = $bo->initialDetails->searchData->BlueCollarbookData->seller_bc_reg_id;
                }else{
                    $model->seller_post_item_id = $selRegId;
                }
               break;
            case 100 :
                //return json_encode($bo->initialDetails);
                 //return json_encode(EncrptionTokenService::idDecrypt($bo->initialDetails->searchData->id));
                $userID = JWTAuth::parseToken()->getPayload()->get('id');
                $bc_reg_id = SellerRegistration::where('created_by', '=', $userID)->first()->id;
                $first_name = SellerRegistration::where('created_by', '=', $userID)->first()->first_name;
                $last_name = SellerRegistration::where('created_by', '=', $userID)->first()->first_name;

                $model->lkp_service_id= 23;
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id= $bc_reg_id;
                $model->search_data = json_encode($bo->initialDetails->searchData);
                $model->price =$bo->initialDetails->searchData->BlueCollarbookData->salary;
                $model->buyer_post_id = $bo->initialDetails->searchData->BlueCollarbookData->id;
                $model->status = 'DRAFT';
                $model->buyer_quote_item_id = $bo->initialDetails->searchData->BlueCollarbookData->quote[0]->id;
                $model->buyer_name = $firstname;
                $model->seller_name = $first_name.' '.$last_name;
                $model->seller_post_item_id = $bo->initialDetails->searchData->BlueCollarbookData->quote[0]->id;

            break;
            default :
                return "invalid request";
                break;

        }


        //        LOG::info('bo2model End ');

        try {
            $model->save();
            $model->enc_id = EncrptionTokenService::valueEncrypt($model->id);
            return response()->json(['isSuccessful' => 'true', 'payload' => $model]);
        } catch (Exception $e) {
            return $e->errorMessage();
        }
    }
    
    public function addInitialLeadsCartDetail(Request $request)
    {
       
        $bo = json_decode($request->data);
       // return json_encode($bo->initialDetails->searchData);
        $firstname = JWTAuth::parseToken()->getPayload()->get('firstname');
        $id = JWTAuth::parseToken()->getPayload()->get('id');

        $model = new CartItem();
        $serviceId = $bo->initialDetails->serviceId;   
        switch ($serviceId) {
            case _INTRACITY_:
                $model->lkp_service_id = _INTRACITY_;

                if($bo->initialDetails->searchData->type_basis == 2){
                    $model->from_location = $bo->initialDetails->searchData->fromLocation->locality_name;
                    $model->to_location = $bo->initialDetails->searchData->tolocation->locality_name;
                }else if($bo->initialDetails->searchData->type_basis == 1){
                    $model->from_location = $bo->initialDetails->searchData->city_name;
                    $model->to_location = $bo->initialDetails->searchData->reporting_location->locality_name;
                }
                $bo->initialDetails->searchData->serviceId = _INTRACITY_;
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id = $bo->initialDetails->sellerId;
                $model->search_data = json_encode($bo->initialDetails->searchData);
                $model->price = $bo->initialDetails->searchData->price;

                $model->city_id = $bo->initialDetails->searchData->city_id;
                $model->city_name = $bo->initialDetails->searchData->city_name;

                $model->buyer_name = $firstname;
                $model->seller_post_item_id = $bo->initialDetails->post->id;
                $model->status = 'DRAFT';
                $model->rootId =  $bo->initialDetails->post->id;
                $model->created_ip = $request->ip();
                $model->title = $bo->initialDetails->searchData->title;
                $model->dispatch_date = $bo->initialDetails->searchData->valid_from;
                $model->delivery_date = $bo->initialDetails->searchData->valid_to;

                $user = DB::table('users')
                        ->where('id','=',$id)
                        ->select('username')
                        ->get();

                $model->seller_id = $bo->initialDetails->post->posted_by;
                $model->seller_name = $user[0]->username;
                break;


            case _HYPERLOCAL_:
                $model->lkp_service_id = _HYPERLOCAL_;
                $model->from_location = $bo->initialDetails->searchData->fromLocation->locality_name;
                $model->to_location = $bo->initialDetails->searchData->tolocation->locality_name;
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id = $bo->initialDetails->sellerId;
                $model->search_data = json_encode($bo->initialDetails->searchData);
                $model->price = $bo->initialDetails->post->base_price;

                $model->city_id = $bo->initialDetails->searchData->city_id;
                $model->city_name = $bo->initialDetails->searchData->city_name;

                $model->buyer_name = $firstname;
                $model->seller_post_item_id = $bo->initialDetails->post->id;
                $model->status = 'DRAFT';
                $model->rootId =  $bo->initialDetails->post->id;
                $model->created_ip = $request->ip();
                $model->title = $bo->initialDetails->searchData->title;
                $model->dispatch_date = $bo->initialDetails->searchData->from_date;
                $model->delivery_date = $bo->initialDetails->searchData->to_date;

                $user = DB::table('users')
                        ->where('id','=',$id)
                        ->select('username')
                        ->get();

                $model->seller_id = $bo->initialDetails->post->posted_by;
                $model->seller_name = $user[0]->username;
                //return $model;
                break;
            default :
                return "invalid request";
                break;

        }
        try {
            $model->save();
            $model->enc_id = EncrptionTokenService::valueEncrypt($model->id);
            return response()->json(['isSuccessful' => 'true', 'payload' => $model]);
        } catch (Exception $e) {
            return $e->errorMessage();
        }
    }


    public function updateCartDetails(Request $request)
    {

        $bo = json_decode($request->data);
        //return json_encode($bo);
        //return EncrptionTokenService::idDecrypt($bo->cartId);

        LOG::info('Update Cart Items'.EncrptionTokenService::idDecrypt($bo->cartId));

        $model = CartItem::find(EncrptionTokenService::idDecrypt($bo->cartId));

        $model->status = 'UPDATED';

        $model->buyer_consignor_name = $bo->consignor->name;
        $model->buyer_consignor_email = PostRequest::has($bo->consignor, 'email');
        $model->buyer_consignor_mobile = $bo->consignor->mobile;
        $model->buyer_consignor_address = $bo->consignor->address1;
        $model->buyer_consignor_address2 = PostRequest::has($bo->consignor, 'address2');
        $model->buyer_consignor_address3 = PostRequest::has($bo->consignor, 'address3');
        $model->buyer_consignor_pincode = $bo->consignor->pin_code;
        $model->buyer_consignor_city = PostRequest::has($bo->consignor, 'city');
        $model->buyer_consignor_state = PostRequest::has($bo->consignor, 'state');

        $model->buyer_consignee_name = $bo->consignee->name;
        $model->buyer_consignee_email = PostRequest::has($bo->consignee, 'email_id');
        $model->buyer_consignee_mobile = $bo->consignee->mobile;
        $model->buyer_consignee_address = $bo->consignee->address1;
        $model->buyer_consignee_address2 = PostRequest::has($bo->consignee, 'address2');
        $model->buyer_consignee_address3 = PostRequest::has($bo->consignee, 'address3');
        $model->buyer_consignee_pincode = $bo->consignee->pin_code;
        $model->buyer_consignee_city = PostRequest::has($bo->consignee, 'city');
        $model->buyer_consignee_landmark = PostRequest::has($bo->consignee, 'landmark');
        $model->buyer_consignee_state = $bo->consignee->state;


        if($model->lkp_service_id == _BLUECOLLAR_) {
            $model->dispatch_date = date("Y-m-d");
        }

        if(isset($bo->consignment_pickup_date))
        {
            $pickup_date = str_replace('/','-',$bo->consignment_pickup_date);
            $consignment_pickup_date = date('Y-m-d',strtotime($pickup_date));
            $model->buyer_consignment_pick_up_date = $consignment_pickup_date;
            $model->dispatch_date = $consignment_pickup_date;
            $model->buyer_consignment_value = $bo->consignment_value;
            $model->consignment_type = $bo->consignment_type;
            $model->lkp_src_location_type_id = $bo->source_locations->id;
            $model->lkp_dest_location_type_id = $bo->destination_locations->id;
            $model->lkp_packaging_type_id = $bo->packings->id;
        }







        $model->is_consignment_insured = PostRequest::has($bo, 'need_insurance');
        $model->buyer_additional_details = PostRequest::has($bo->consignee, 'additional_details');

        // $model->attributes = json_encode($bo->attributes);
        // $model->insurance_details = json_encode($bo->insuranceDetails);

        // $model->freight_charges = 0;
        // $model->local_charges = 0;
        // $model->insurance_charges = 0;
        // $model->service_tax = 0;
        // if(isset($bo->charges)) {
        //     $model->freight_charges = isset($bo->charges->freightCharges) ? $bo->charges->freightCharges : 0;
        //     $model->local_charges = isset($bo->charges->localCharges) ? $bo->charges->localCharges : 0;
        //     $model->insurance_charges = isset($bo->charges->insuranceCharges) ? $bo->charges->insuranceCharges : 0;
        //     $model->service_tax = isset($bo->charges->serviceTax) ? $bo->charges->serviceTax : 0;
        // }

        if (isset($quote->valid_from))
            $model->valid_from = $quote->valid_from;
        if (isset($quote->valid_to))
            $model->valid_to = $quote->valid_to;
        //        LOG::info('bo2model End ');

        try {
            $model->save();
            return response()->json(['isSuccessful' => 'true', 'payload' => $model]);
        } catch (Exception $e) {
            LOG::info('Update Cart Items ' . $e->errorMessage());
            return $e->errorMessage();
        }
    }


    public function updateCartStatus(Request $request)
    {
        try {
            $cartItem = new CartItem();
            $cartItem->updateCartItemStatus(EncrptionTokenService::idDecrypt($request->cartId));
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function getDetailsByCartId(Request $request,$id)
    {
        try {
            if(isset($request->id))
            {
                $id = $request->id;
            }
//            $cartItem = CartItem::with('bcSellerData')
//                        ->where('id', EncrptionTokenService::idDecrypt($id))
//                        ->where('status', 'DRAFT')
//                        ->first();
//            return $cartItem;
            $cartItem = CartItem::with(
                        'bcSellerData', 
                        'bcSellerData.createdBy', 
                        'bcSellerData.curCity', 
                        'bcSellerData.curDistrict', 
                        'bcSellerData.curState',
                        'mType'
                    )
                ->with("getRoute")
                ->where('id', EncrptionTokenService::idDecrypt($id))
                ->where('status', 'DRAFT')
                ->select("*", \DB::raw("(select vehicle_type FROM lkp_vehicle_types WHERE id=lkp_ict_vehicle_id ) as vehicle"))
                ->get()->first();
            return response()->json(['payload' => $cartItem]);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function cartCount()
    {
        try {
            return CartItem::getCartCount();
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function getCartItems()
    {
        try {
            $userID = JWTAuth::parseToken()->getPayload()->get('id');
            $cartItem = CartItem::where("buyer_id", $userID)
                //->where("lkp_service_id",3)
                ->where("status", "ADDED_TO_ORDERS")
                ->get();
            return response()->json(["isSuccessfull" => "true", "payload" => $cartItem]);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function deleteCartItem(Request $request)
    {
        try {

            $buyerId = $request->buyerId;
            $cartId = $request->Id;
            $serviceId = 3;//intracity service id
            $model = new CartItem();
            $model->deleteByCartId($cartId, $buyerId, $serviceId);
            return response()->json(["isSuccessfull" => "true", "payload" => $cartId]);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

        //dd($model);
    }

    public function emptyCartItem(Request $request)
    {
        try {
            $buyerId = $request->buyerId;

            $serviceId = 3;//intracity service id
            $model = new CartItem();
            $model->emptyCart($buyerId, $serviceId);
            return response()->json(["isSuccessfull" => "true", "payload" => $buyerId]);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }
    public function dataPrepaid(Request $request)
    {
         try{
            $orderid=$request->id;
            $result = DB::table('intra_hp_order')
            ->join('intra_hp_order_items', 'intra_hp_order.id', '=', 'intra_hp_order_items.order_id')
            ->selectRaw('*,sum(intra_hp_order_items.price) as price')
            ->where('intra_hp_order.id', $orderid)
            ->get();
            //return $result->buyer_name;
            $log= new PaymentLog;
            $array=array('channel'=>10,
                       'account_id'=>HDFC_PAYMENT_GATEWAY_ACCOUNT_ID,
                      
                     );
            foreach ($result as $key => $value) {
                $log->order_payment_id=$value->order_id;
                $log->gateway='HDFC';
                $log->amount=$value->price;
                $log->verified_status='pending';
                $log->order_status='pending';
                $array['reference_no']=$value->order_id;
                $array['amount']=$value->price;
                $array['currency']='INR';
                $array['description']=$value->order_id.''.$value->consignee_name;
                $array['return_url']='http://localhost:8000/api/v1/l/pgresponce';
                $array['mode']='LIVE';
                $array['name']=$value->consignee_name;
                $array['address']=$value->consignor_address1;
                $array['city']=$value->consignor_city;
                $array['state']=$value->consignee_state;
                $array['postal_code']=$value->consignor_pincode;
                $array['country']='IND';
                $array['email']=$value->consignor_email;
                $array['phone']=$value->consignor_mobile;
            }
              $has=self::hash($array);
              $array['hash']=$has;
              $log->request=serialize($array);
              $log->save();
              return response()->json(['payload' =>$array]);
        }catch(Exception $e)
         {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
         }

    }

    public static function hash($POST)
    {
            ksort($POST);
            $HASHING_METHOD = 'sha512'; // md5,sha1
            $ACTION_URL = "https://secure.ebs.in/pg/ma/payment/request/";
            $hashData =HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY;
            //'secretkey'=>HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY
            foreach ($POST as $key => $value){
                if (strlen($value) > 0) {
                    $hashData .= '|'.$value;
                }
            }
            if (strlen($hashData) > 0) {
                return $secureHash = strtoupper(hash($HASHING_METHOD, $hashData));
            }
    }

    public static function checkCartValue($routeId, $serviceId) 
    {
        try {
            
            $userID = JWTAuth::parseToken()->getPayload()->get('id');
            $isCartExist = CartItem::where([
                ['rootId',$routeId],
                ['lkp_service_id', $serviceId],
                ['status','UPDATED'],
                ['buyer_id',$userID]
            ])->get();

            return count($isCartExist) ? true:false;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

}
