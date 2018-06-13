<?php

namespace ApiV2\Controllers;

use ApiV2\Model\IntraHyperBuyerPost;
use ApiV2\Model\IntraHyperQuotaion;
use App\Http\Controllers\Controller as BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\SettingsSearchService;    
use ApiV2\Services\LogistiksCommonServices\NegotationServices;

class IntracityNegotiation extends BaseController
{

    public function sellerQuoteSubmit(Request $request)
    {
       // return $request->all();
        $quote = new IntraHyperQuotaion();
        $seller_id = JWTAuth::parseToken()->getPayload()->get('id');
        $quote->post_id = $request->post_id;
        $quote->route_id = $request->route_id;
        $quote->buyer_id = $request->buyer_id;
        $quote->seller_id = $seller_id;
        $quote->quotation_type = $request->quotation_type;
        $quote->lkp_service_id = $request->lkp_service_id;
         
        $quote->seller_status = 'OFFER';
        $quote->seller_quote_at = Carbon::now();
        $quote->status = 1;
        $quote->transit_day = $request->transit_day;

        if($request->lkp_service_id == _INTRACITY_){
          $quote->initial_quote_price = $request->quotedPrice;
        }else if($request->lkp_service_id == _HYPERLOCAL_){
          $quote->initial_quote_price = $request->quotedPrice;
        }
        
        $quote->save();

        if($quote->id){
            $data['success'] = true;
            $data['error'] = $quote;
        }else{
            $data['success'] = false;
            $data['error'] = 400;
        }
        return $data;
    }

    public function sellerhpQuoteSubmit(Request $request)
    {
        $quote = new IntraHyperQuotaion();
        $request->seller_id = JWTAuth::parseToken()->getPayload()->get('id');
        $quote->post_id = $request->post_id;
        $quote->route_id = $request->route_id;
        $quote->buyer_id = $request->buyer_id;
        $quote->seller_id = $request->seller_id;
        $quote->quotation_type = $request->quotation_type;
        $quote->lkp_service_id = _HYPERLOCAL_;
         
        $quote->seller_status = 'OFFER';
        $quote->seller_quote_at = Carbon::now();
        $quote->status = 1;
       
        $quote->payment_method = $request->payment_method;
        $quote->transit_day = $request->transit_day;
        
           
        $quote->payment_term = $request->payment_term;
        $quote->initial_quote_price = $request->quotedPrice;
        
        if ($quote->payment_term == 'CREDIT') {
            $quote->credit_days = $request->credit_days;
        }
        $quote->save();
        if($quote->id){
            $data['success'] = true;
            $data['error'] = 400;
        }else{
            $data['success'] = false;
            $data['error'] = 400;
        }
        return $data;
        
    }

    public function buyerQuoteAction(Request $request) {
        
        try {
            return response()->json([
            'payload'=>NegotationServices::buyerQuoteAction($request->data)
            ]);
        } catch(Exception $e) {

        }
    }

    public function sellerQuoteAction(Request $request)
    {
        try {
            return response()->json([
            'payload'=>NegotationServices::sellerQuoteAction($request->data)
            ]);
        } catch(Exception $e) {
            return $this->errorResponse($e);
        }        
    }









    public function buyerFirmQuoteAction(Request $request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = IntraHyperQuotaion::
        where('id', '=', $request->quote)
            // ->where('buyer_status', '=', NULL)
            // ->where('buyer_id', '=', $userId)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('quotation_type', '=', 'FIRM')
                    ->orWhere('quotation_type', '=', 'COMPETITIVE');
            })
            ->first();

        if ($quote != null) {
            if ($request->isSeller) {
                $quote->seller_status = $request->sellerAction;
            } else {
                $quote->buyer_status = $request->sellerAction;
            }
            $quote->save();
        }

        return response()->json(['payload' => array('success' => true, 'data' => $quote)]);
    }

    public function buyerFinalQuoteAction(Request $request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = IntraHyperQuotaion::
        where('id', '=', $request->quote)
            ->where('buyer_id', '=', $userId)
            ->where(function ($query) {
                $query->where('buyer_status', '=', NULL)
                    ->orWhere('buyer_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'ACCEPT');
            })
            ->first();
        if ($quote != null) {
            $quote->buyer_status = $request->buyerAction;
            $quote->save();
        }
        return response()->json(['payload' => array('success' => true)]);
    }

    public function buyerCompetitiveQuoteAction(Request $request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = IntraHyperQuotaion::
        where('id', '=', $request->quote)
            ->where('buyer_id', '=', $userId)
            ->where('buyer_status', '=', NULL)
            ->where('seller_status', '=', 'OFFER')
            ->where('quotation_type', '=', 'COMPETITIVE')
            ->where('buyer_counter_transit_days', '=', NULL)
            ->first();
        if ($quote != null) {
            $quote->buyer_status = $request->buyerAction;
            $quote->buyer_counter_transit_days = $request->transit_days;
            $quote->buyer_quote_price = $request->buyer_quote;
            $quote->buyer_quote_at = Carbon::now();
            $quote->save();
        }
        return response()->json(['payload' => array('success' => true)]);
    }

    public function buyerInOutBoundCount()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $inCount = IntraHyperQuotaion::
        where('buyer_id', '=', $userId)
            ->where('lkp_service_id', '=', _INTRACITY_)
            ->where('seller_status', '=', 'OFFER')
            ->where('buyer_status', '=', NULL)
            ->count();
        $outCount = IntraHyperQuotaion::
        where('buyer_id', '=', $userId)
            ->where('lkp_service_id', '=', _INTRACITY_)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'ACCEPT')
                    ->orWhere('seller_status', '=', 'COUNTER')
                    ->orWhere('seller_status', '=', 'DENY');
            })
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER')
                    ->orWhere('buyer_status', '=', 'ACCEPT')
                    ->orWhere('buyer_status', '=', 'DENY');
            })
            ->count();

        $bound = array('in_bound' => $inCount, 'out_bound' => $outCount);
        return response()->json(['payload' => $bound]);
    }

    public function buyerInboundList()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $inbound_list = IntraHyperQuotaion::
        with(['route', 'route.fromLocation', 'route.toLocation', 'post', 'post.postBy', 'post.vehicleType'])
            ->where('buyer_id', '=', $userId)
            ->where('lkp_service_id', '=', _INTRACITY_)
            ->where('seller_status', '=', 'OFFER')
            ->where('buyer_status', '=', NULL)
            ->get();

        return response()->json(['payload' => $inbound_list]);
    }

    public function buyerOutboundList()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $inbound_list = IntraHyperQuotaion::
        with(['route', 'route.fromLocation', 'route.toLocation', 'post', 'post.postBy', 'post.vehicleType'])
            ->where('buyer_id', '=', $userId)
            ->where('lkp_service_id', '=', _INTRACITY_)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'ACCEPT')
                    ->orWhere('seller_status', '=', 'DENY')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER')
                    ->orWhere('buyer_status', '=', 'ACCEPT')
                    ->orWhere('buyer_status', '=', 'DENY');
            })
            ->get();

        return response()->json(['payload' => $inbound_list]);
    }

    public function sellerInOutBoundCount()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $inCount = IntraHyperQuotaion::
        where('seller_id', '=', $userId)
        ->where('lkp_service_id', '=', _INTRACITY_)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'DENY')
                    ->orWhere('seller_status', '=', 'ACCEPT')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER')
                    ->orWhere('buyer_status', '=', 'DENY')
                    ->orWhere('buyer_status', '=', 'ACCEPT')
                    ->orWhere('buyer_status', '=', 'ACCEPT');
            })
            ->count();
        $outCount = IntraHyperQuotaion::
        where('seller_id', '=', $userId)
        ->where('lkp_service_id', '=', _INTRACITY_)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'ACCEPT')
                    ->orWhere('seller_status', '=', 'DENY')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'DENY')
                    ->orWhere('buyer_status', '=', NULL);
            })
            ->count();

        $bound = array('in_bound' => $inCount, 'out_bound' => $outCount);
        return response()->json(['payload' => $bound]);
    }

    public function sellerInboundList(Request $request)
    {

        return SettingsSearchService::filterInbound($request);


        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $inbound_list = IntraHyperQuotaion::
        with(['route', 'route.fromLocation', 'route.toLocation', 'route.vehicleType', 'post', 'post.postBy', 'post.vehicleType'])
            ->where('seller_id', '=', $userId)
            ->where('lkp_service_id', '=', _INTRACITY_)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'COUNTER')
                    ->orWhere('seller_status', '=', 'ACCEPT')
                    ->orWhere('seller_status', '=', 'DENY');
            })
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER')
                    ->orWhere('buyer_status', '=', 'ACCEPT')
                    ->orWhere('buyer_status', '=', 'DENY');
            })
            ->get();

        return response()->json(['payload' => $inbound_list]);
    }

    public function sellerOutboundList()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $inbound_list = IntraHyperQuotaion::
        with(['route', 'route.fromLocation', 'route.toLocation', 'post', 'post.postBy', 'post.vehicleType'])
            ->where('seller_id', '=', $userId)
            ->where('lkp_service_id', '=', _INTRACITY_)
            ->where(function ($query) {
                $query->where('seller_status', '=', 'OFFER')
                    ->orWhere('seller_status', '=', 'ACCEPT')
                    ->orWhere('seller_status', '=', 'DENY')
                    ->orWhere('seller_status', '=', 'COUNTER');
            })
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'DENY')
                    ->orWhere('buyer_status', '=', 'ACCEPT')
                    ->orWhere('buyer_status', '=', NULL);
            })
            ->get();

        return response()->json(['payload' => $inbound_list]);
    }

    

    public function sellerQuoteCounterAction(Request $request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $quote = IntraHyperQuotaion::
        where('seller_id', '=', $userId)
        ->where('lkp_service_id', '=', _INTRACITY_)
            ->where('id', '=', $request->quote)
            ->where('seller_status', '=', 'OFFER')
            ->where(function ($query) {
                $query->where('buyer_status', '=', 'COUNTER');
            })
            ->where('quotation_type', '=', 'COMPETITIVE')
            ->first();
        if ($quote != null) {
            $quote->seller_quote_price = $request->seller_quote;
            $quote->seller_final_transit_days = $request->transit_days;
            $quote->seller_status = $request->sellerAction;
            $quote->save();
        }
        return response()->json([
            'payload' => $quote,
            'isSuccessfull' => true
        ]);
    }

    public function declineQuoteAction(Request $request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        // if($request->isSeller)
        $isSeller = $request->isSeller;
        $quote = IntraHyperQuotaion::where('id', '=', $request->quote)->first();

        if ($quote != null) {
            if (!$isSeller) {
                $quote->buyer_status = 'DENY';
            } else {
                $quote->seller_status = 'DENY';
            }

            $quote->save();
        }

        return response()->json([
            'payload' => $quote,
            'isSuccessfull' => true
        ]);
    }

}
