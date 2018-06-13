<?php

namespace ApiV2\Services\LogistiksCommonServices;

use DB;
use ApiV2\Services\BlueCollar\BaseServiceProvider;
use ApiV2\Model\IntraHyperQuotaion;
use Log;
class NegotationServices extends BaseServiceProvider
{
    // 1-seller quote price,2-counter offer by buyer,3-final quote price by seller,
    // 4-accepted,5-denied,6-canel contract,9-accepted

    private static $status = array(
        1 => 'SELLER_INITIAL_QUOTE',
        2 => 'COUNTER_BY_BUYER',
        3 => 'SELLER_FINAL_QUOTE',
        4 => 'ACCEPTED_QUOTE',
        5 => 'DENIED_QUOTE',
        6 => 'CANCEL_QUOTE'
    );

    public static function buyerQuoteAction($data) {
        Log::info('buyerQuoteAction');
        $model = IntraHyperQuotaion::find($data['id']); 

        $model->status = array_search($data['action'],static::$status);
        $model = static::getUpdateColumns($model,$data);
        return $model->save();
    }

    public static function sellerQuoteAction($data) {      
        Log::info('sellerQuoteAction');
       
        $model = IntraHyperQuotaion::find($data['id']);
        
        $model->status = array_search($data['action'],static::$status);
        
        $model = static::getUpdateColumns($model,$data);

        return $model->save();
    }

    private static function getUpdateColumns($model,$data) {
        if($data['action'] == 'COUNTER_BY_BUYER') {
            $model->buyer_quote_price = $data['buyerPrice'];
            $model->buyer_counter_transit_days = isset($data['buyerCounterTransitDay']) && !empty($data['buyerCounterTransitDay']) ? $data['buyerCounterTransitDay']:''; 
        }
        if($data['action'] == 'SELLER_FINAL_QUOTE') {
            $model->seller_quote_price = $data['sellerFinalQuotePrice'];
            $model->seller_final_transit_days = $data['sellerFinalTransitDay'];            
        }

        return $model;
    }



    


    

    

}
