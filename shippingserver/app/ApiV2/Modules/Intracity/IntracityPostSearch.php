<?php

namespace ApiV2\Modules\Intracity;

use ApiV2\Model\IntraHyperRoute;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\IntraHyperBuyerPost;

class IntracityPostSearch
{

    public function document()
    {
        return $this->hasMany('ApiV2\Model\IntraHyperDocumentUpload', 'buyerpost_terms_id', 'id');
    }

    public function buyerSearch($data)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $dispatch_date = str_replace('/','-',$data->dispatchDate);
       // return $dispatch_date;
        $results = IntraHyperRoute::
            with(['post' => function ($query) use ($data) {
                $query->where('lkp_service_id', '=', _INTRACITY_)
                    ->where('last_date', '>=', date("Y-m-d", strtotime($dispatch_date)))
                    ->where('type_basis', '=', $data->type)
                    ->where('lead_type', '=', $data->termPost)
                    ->where('is_active', '=', 1);
             }])
            ->leftJoin('intra_hp_buyer_posts', 'intra_hp_buyer_seller_routes.fk_buyer_seller_post_id', '=', 'intra_hp_buyer_posts.id')
            ->with(['post.postBy','quote.contract','post.vehicleType', 'city', 'fromLocalities', 'toLocalities', 'vehicleType'])
            ->with(['quote' => function ($query) use ($userID) {
                $query->where('seller_id', '=', $userID);
            }])
            
            ->where('intra_hp_buyer_seller_routes.valid_from', '<=',date("Y-m-d", strtotime($dispatch_date)))
            ->where('intra_hp_buyer_seller_routes.is_seller_buyer', '=', 1)
            ->where('intra_hp_buyer_seller_routes.city_id', '=', $data->city->id)
            ->where('intra_hp_buyer_seller_routes.lkp_service_id', '=', _INTRACITY_)
            ->where('intra_hp_buyer_posts.lead_type', '=', (int)$data->termPost)
            ->where('intra_hp_buyer_posts.type_basis', '=', (int)$data->type)
            ->where(function($q) use($data) {
                if(property_exists($data, 'timeSlot') && !empty($data->timeSlot)) {
                    $q->where('intra_hp_buyer_seller_routes.vehicle_rep_time', '=', (int)$data->timeSlot);
               }
            })
            ->orderBy('sortDate', 'DESC')
            ->orderBy('intra_hp_buyer_seller_routes.id', 'desc')
            ->select("intra_hp_buyer_seller_routes.*",\DB::raw("date_format(concat(last_date,' ',last_time), '%Y-%m-%d %h:%i') as sortDate"))
            ->get();


        $response = array();
        $response['payload'] = $results;

        return $response;

    }
    public function sellerSearch($data)
    {
       
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $dispatch_date = str_replace('/','-',$data->date);
        
        $result = IntraHyperRoute::
        with(['postResult' => function ($query) use ($data) {
            $query->where('lkp_service_id', '=', _HYPERLOCAL_)
                // ->where('last_date', '>=', $data->last_date)
                // ->where('lead_type', '=', $data->lead_type)
                ->where('is_active', 1);

        }])
        ->with(['postResult.postBy','quote.contract','cityRes','fromLocalitiesRes','toLocalitiesRes'])
        ->with(['quote' => function ($query) use ($userID) {
            $query->where('seller_id', '=', $userID)->where('lkp_service_id', '=', _HYPERLOCAL_);
        }])
        ->leftJoin("intra_hp_buyer_posts as post",function($join) use ($data) {
            $join->on("post.id","=","intra_hp_buyer_seller_routes.fk_buyer_seller_post_id");
        })
        ->where("post.last_date","<=", date("Y-m-d", strtotime($dispatch_date)))
        ->where("post.lead_type","=", (int) $data->type)
        ->groupBy("intra_hp_buyer_seller_routes.fk_buyer_seller_post_id")
        ->where('intra_hp_buyer_seller_routes.is_seller_buyer', '=', 1)
        ->where('intra_hp_buyer_seller_routes.city_id', '=', $data->city->id)
        ->where('intra_hp_buyer_seller_routes.from_location', '=', $data->from_location->id)
        ->where('intra_hp_buyer_seller_routes.to_location', '=', $data->to_location->id)
        ->where('intra_hp_buyer_seller_routes.lkp_service_id', '=', _HYPERLOCAL_)
        ->orderBy('sortDate', 'DESC')
        ->select("intra_hp_buyer_seller_routes.*",\DB::raw("date_format(concat(last_date,' ',last_time), '%Y-%m-%d %h:%i') as sortDate"))
        ->get();
        $res = array();
        $keys = 0;

        foreach($result as $key => $val){
           if($val->postResult->last_date > date('Y-m-d')){
              $res[$keys] = $val;
              ++$keys;
           }
        }
        
        foreach($result as $key => $val){
           if($val->postResult->last_date < date('Y-m-d')){
              $res[$keys] = $val;
              ++$keys;
           }
        }
       // return $res;
        $response = array();
        $response['payload'] = $res;

        return $response;
    }

}
