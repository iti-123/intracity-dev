<?php

namespace Api\Modules\Intracity;

use Api\Model\IntraHyperRoute;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Model\IntraHyperBuyerPost;

class IntracityPostSearch
{


    public function buyerSearch($data)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $results = IntraHyperRoute::
        with(['post' => function ($query) use ($data) {
            $query->where('lkp_service_id', '=', 3)
                ->where('last_date', '>=', $data->dispatchDate)
                ->where('type_basis', '=', $data->type)
                ->where('lead_type', '=', $data->termPost)
                ->where('is_active', '=', 1);
        }])
            ->with(['post.postBy', 'post.vehicleType', 'city', 'fromLocalities', 'toLocalities', 'vehicleType'])
            ->with(['quote' => function ($query) use ($userID) {
                $query->where('seller_id', '=', $userID);
            }])
            ->where('valid_from', '<=', $data->dispatchDate)
            ->where('is_seller_buyer', '=', 1)
            ->where('city_id', '=', $data->city->id)
            ->get();


        $response = array();
        $response['payload'] = $results;

        return $response;

    }
    public function sellerSearch($data)
    {

        // return response()->json($data);
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        // return $result = IntraHyperBuyerPost::with('quoteHyper')->where('id', '=', 40)->first();

        $result = IntraHyperRoute::
        with(['postResult' => function ($query) use ($data) {
            $query->where('lkp_service_id', '=', _HYPERLOCAL_)
                // ->where('last_date', '>=', $data->last_date)
                // ->where('lead_type', '=', $data->lead_type)
                ->where('is_active', 1);

        }])
        ->with(['postResult.postBy', 'cityRes', 'fromLocalitiesRes', 'toLocalitiesRes'])
        ->with(['quote' => function ($query) use ($userID) {
            $query->where('seller_id', '=', $userID)->where('lkp_service_id', '=', _HYPERLOCAL_);
        }])
        // ->where('last_date', '<=', $data->last_date)
        ->where('is_seller_buyer', '=', 1)
        ->where('city_id', '=', $data->city->id)
        ->where('from_location', '=', $data->from_location->id)
        ->where('to_location', '=', $data->to_location->id)
        ->where('lkp_service_id', '=', _HYPERLOCAL_)
        ->get();
        // ->toSql();

        $response = array();
        $response['payload'] = $result;

        return $response;
    }

}
