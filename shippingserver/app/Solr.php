<?php

namespace App;

use Api\Requests\IntraHyperBuyerPostRequest as BuyerPostRequest;
use App\Exceptions\ApplicationException;
use Log;

class Solr
{
    protected $solr_base_url = "http://localhost:8983/solr/logistiks";

    public function __construct()
    {

    }

    public function add($route = null, $additional, $primaryData = null)
    {

        $additionalData = array(
            'city_name' => BuyerPostRequest::getCityName($additional),
            'hour_dis_slab_name' => BuyerPostRequest::getHDSlabsName($additional),
            'from_location' => property_exists($additional, 'd_from_location') ? $additional->d_from_location->locality_name : '',
            'to_location' => property_exists($additional, 'd_to_location') ? $additional->d_to_location->locality_name : '',
            'vehicle_type' => BuyerPostRequest::getVehicleTypeName($additional),
            'leadType' => $primaryData->lead_type,
            'lastDateTimeForQuote' => strtotime($primaryData->last_date . "T" . $primaryData->last_time),
            'created_at' => strtotime($primaryData->created_at),
            'updated_at' => strtotime($primaryData->updated_at),
            'id' => 'bp-' . $primaryData->id, // Buyer Post Id
        );

        $addedData = array_merge($route, $additionalData);


        $ch = curl_init($this->solr_base_url . "/update?wt=json");

        $data = array(
            "add" => array(
                "doc" => $addedData,
                "commitWithin" => 1000,
            ),
        );


        $data_string = json_encode($data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $response = curl_exec($ch);
        dd($response);
        return response()->json($response);
    }

// Solr Search Service 

    public function solrSearchService($q, $start = 0, $rows = 100)
    {

        $url = $this->solr_base_url . "/select?wt=json";

        LOG::info("searching documents from SOLR");

        $url = $url . "&start=" . $start;

        $url = $url . "&rows=" . $rows;

        $url = $url . "&q=" . urlencode($q);

        $ch = curl_init($url);

        //curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

        $response = curl_exec($ch);

        Log::info($response);

        $jsonResponse = json_decode($response);

        if ($jsonResponse->responseHeader->status != 0) {
            LOG:
            info("Failed searching SOLR. See responses above. Hint Solr Status => " . $jsonResponse->responseHeader->status);
            throw new ApplicationException([], ["Failed searching document(s) from SOLR Store"]);
        }

        return $response;

    }

    public function solarSaveRatecard($IntraHyperSellerPost, $route, $seller_ids)
    {
        $Buyer = '';
        if (!empty($seller_ids)) {
            $Buyer = $seller_ids;
        }
        $additionaldata = array(
            'id' => 'SP' . $route['fk_buyer_seller_post_id'],
            'rate_cart_type' => $IntraHyperSellerPost->rate_cart_type,
            'notes' => $IntraHyperSellerPost->notes,
            'is_private_public' => $IntraHyperSellerPost->is_private_public,
            'is_private_public' => $IntraHyperSellerPost->is_private_public,
            'terms_cond' => $IntraHyperSellerPost->terms_cond,
            'posted_by' => $IntraHyperSellerPost->posted_by,
            'is_active' => $IntraHyperSellerPost->is_active,
            'updated_at' => $IntraHyperSellerPost->updated_at,
            'created_at' => $IntraHyperSellerPost->created_at,
            'buyer' => $Buyer,
            'city_name' => BuyerPostRequest::getCityName($route['city_id']),
            'vehicle_type' => BuyerPostRequest::getVehicleTypeName($route['vehicle_type_id']),
        );
        // $addedData = array_merge($route,$additionaldata);
        // dd($addedData);


    }

}
