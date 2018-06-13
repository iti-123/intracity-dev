<?php

namespace Api\Controllers;

use Api\Model\CartItem;
use Api\Requests\BookNowRequest as BookingRequest;
use Api\Requests\IntraHyperBuyerPostRequest as PostRequest;
use Api\Services\CartItemService;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;


class IntracityCartItemsController extends BaseController
{

    public function __construct()
    {

    }

    public function addInitialCartDetails(Request $request)
    {
         return "uygjygu";
       $bo = json_decode($request->data);
      
        if(isset($bo->initialDetails->quote->id)){
		$bo->initialDetails->quote->id = EncrptionTokenService::idDecrypt($bo->initialDetails->quote->id);}
		
        $firstname = JWTAuth::parseToken()->getPayload()->get('firstname');
        //return $firstname;
        LOG::info('add initial intracity cart items');

          $serviceId = $bo->initialDetails->serviceId;
        $model = new CartItem();

        switch ($serviceId) {
            case _INTRACITY_:
                $fromlocation = PostRequest::has($bo->initialDetails->searchData->data->fromLocation,'locality_name');
                $tolocation = PostRequest::has($bo->initialDetails->searchData->data->toLocation,'locality_name');

                $dispatchDate = $bo->initialDetails->searchData->data->dispatchDate;
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id = $bo->initialDetails->sellerId;
                $model->lkp_service_id = _INTRACITY_;
                $model->service_type = $bo->initialDetails->serviceType;
                $model->buyer_post_id = PostRequest::has($bo->initialDetails, 'buyerPostId');
                $model->seller_post_item_id = $bo->initialDetails->sellerQuoteId;
                $model->status = 'DRAFT';
                $model->post_type = $bo->initialDetails->postType;
                $model->lead_type = PostRequest::has($bo, 'leadType');
                $model->buyer_name = $firstname;

                $quote = $bo->initialDetails->quote;
                //dd($model);
                /**************price calculation *************/

                $base_distance = PostRequest::has($quote, 'base_distance');
                $rate_base_distance = PostRequest::has($quote, 'rate_base_distance');
                if ((int)$quote->type_basis == INTRA_HYPER_DISTANCE)
                    $price = PostRequest::has($quote, 'finalPrice');

                if ((int)$quote->type_basis == INTRA_HYPER_HOURS) {
                    $base_time = PostRequest::has($quote, 'base_time');
                    $cost_base_time = PostRequest::has($quote, 'cost_base_time');
                    $price = $base_time * $cost_base_time;
                }
                $model->price = $price;
                $model->from_location =  $fromlocation;
                $model->to_location =  $tolocation;
                $model->lkp_ict_vehicle_id = PostRequest::has($quote, 'vehicle_type_id');
                $model->seller_name = PostRequest::has($quote, 'seller');
                $model->rootId = PostRequest::has($quote, 'id');
                $model->tracking_type = PostRequest::has($quote, 'tracking');
                $model->transit_hour = PostRequest::has($quote, 'transit_hour');
                $model->quantity = PostRequest::has($quote, 'qty');
                $model->material_type = json_encode(PostRequest::has($quote, 'materialType'));

                $model->dispatch_date = $dispatchDate;

                $model->search_data = json_encode($bo->initialDetails->searchData);

                if (isset($quote->valid_from))
                    $model->valid_from = $quote->valid_from;
                if (isset($quote->valid_to))
                    $model->valid_to = $quote->valid_to;

                break;


            case _HYPERLOCAL_:
            
                $fromlocation = $bo->initialDetails->searchData->data->location[0]->fromLocation->locality_name;
                $tolocation = $bo->initialDetails->searchData->data->location[0]->tolocation->locality_name;
                $model->lkp_service_id = _HYPERLOCAL_;
                $model->buyer_id = $bo->initialDetails->buyerId;
                $model->seller_id = $bo->initialDetails->sellerId;
                $model->search_data = json_encode($bo->initialDetails->searchData);
                $model->price = $bo->initialDetails->quote->price;
                $model->buyer_name = $firstname;
                $model->seller_post_item_id = $bo->initialDetails->quote->id;
                $model->status = 'DRAFT';
                $model->created_ip = $request->ip();
                $model->valid_from = $bo->initialDetails->quote->from_date;
                $model->valid_to = $bo->initialDetails->quote->to_date;
                $model->from_location = $fromlocation;
                $model->to_location = $tolocation;
                $model->seller_id = $bo->initialDetails->quote->posted_by;
                $model->seller_name = $bo->initialDetails->quote->vendor;
                
                break;
            case  _BLUECOLLAR_:
                //return json_encode($bo->initialDetails);
                 //return json_encode(EncrptionTokenService::idDecrypt($bo->initialDetails->searchData->id));
                    $model->lkp_service_id= _BLUECOLLAR_;

                    $model->dispatch_date = date("Y-m-d");
                    $model->buyer_id = $bo->initialDetails->buyerId;
                    $model->seller_id= $bo->initialDetails->sellerId;
                    $model->search_data = json_encode($bo->initialDetails->searchData);
                    $model->price =$bo->initialDetails->searchData->BlueCollarbookData->seller_salary;
                    $model->buyer_post_id = EncrptionTokenService::idDecrypt($bo->initialDetails->searchData->BlueCollarbookData->id);
                    $model->status = 'DRAFT';
                    $model->buyer_quote_item_id = EncrptionTokenService::idDecrypt($bo->initialDetails->searchData->BlueCollarbookData->id);
                    $model->buyer_name = $firstname;
                $model->seller_name = $bo->initialDetails->searchData->BlueCollarbookData->seller_first_name;
                $model->seller_post_item_id = $bo->initialDetails->searchData->BlueCollarbookData->seller_bc_reg_id;

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


    public function updateCartDetails(BookingRequest $request)
    {

        $bo = json_decode($request->data);
        //return json_encode($bo);
        //return EncrptionTokenService::idDecrypt($bo->cartId);

        LOG::info('Update Cart Items');

        $model = CartItem::find(EncrptionTokenService::idDecrypt($bo->cartId));

        //return $model;
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
        $model->buyer_consignee_state = $bo->consignee->state;
        if(isset($bo->consignment_pickup_date))
        {
            $model->buyer_consignment_pick_up_date = $bo->consignment_pickup_date;
            // $model->dispatch_date = $bo->consignment_pickup_date;
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
            $cartItem = CartItem::
                with('bcSellerData', 'bcSellerData.createdBy', 'bcSellerData.curCity', 'bcSellerData.curDistrict', 'bcSellerData.curState')
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
    
}
