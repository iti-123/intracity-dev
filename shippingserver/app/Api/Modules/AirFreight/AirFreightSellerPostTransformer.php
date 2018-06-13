<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:33
 */

namespace Api\Modules\AirFreight;

use Api\Framework\ISellerPostTransformer;
use Api\Framework\SerializerServiceFactory;
use League\Fractal\TransformerAbstract;
use Log;

//use Api\Transformers\SellerPostBOTransformer;

class AirFreightSellerPostTransformer extends TransformerAbstract implements ISellerPostTransformer
{


    public function ui2bo_save($payload)
    {

        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\AirFreight\AirFreightSellerPostBO', 'json');
        //  dd($post);
        return $post;

    }

    public function ui2bo_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\AirFreight\AirFreightSellerPostSearchBO', 'json');
        return $post;
    }

    /**
     *
     * @param $model
     * @return array
     */
    public function model2boGet($model)
    {
//TODO: Implement model2boGet() method.
        $visibleToSellers = array();
        $model = $model["attributes"];
        $model['postId'] = $model["id"];
        $model['attributes'] = json_decode($model["attributes"]);
        $visibleToBuyers = []; //$this->getVisibleToBuyersBuyerId($model);
        $model['visibleToBuyers'] = $visibleToBuyers;
        unset($model["id"]);
        return $model;
        // TODO: Implement model2boGet() method.
    }

    public function model2boGetAll($models)
    {
        //TODO: Implement model2boGet() method.
        $visibleToSellers = $model = array();
        for ($i = 0; $i < sizeof($models); $i++) {
            $model[$i] = $models[$i]["attributes"];
            $model[$i]['postId'] = $model[$i]["id"];
            $model[$i]['attributes'] = json_decode($model[$i]["attributes"]);
            $visibleToSellers = []; //$this->getVisibleToBuyersBuyerId($model[$i]);
            $model[$i]['visibleToSellers'] = $visibleToSellers;
            unset($model[$i]["id"]);
        }
        return $model;
    }


    public function model2boSave($model)
    {
        $response = 'AirFreightSellerPostTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function bo2modelDelete($bo)
    {
        $response = 'AirFreightSellerPostTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'AirFreightSellerPostTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelSave($bo)
    {
        $response = 'AirFreightSellerPostTransformer.bo2modelSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelGet($bo)
    {

        //$mapper = new JsonMapper();
        //$contactObject = $mapper->map($bo, new SellerPostBO());

        $response = 'AirFreightSellerPostTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }


    /*    public static function convert_db_object_to_json($post){
            $dbObj = json_decode($post, true);
            $attributes = GenericMethods::get_attributes_json($dbObj["attributes"]);
            //$selectedSellers = SelectedSellers::all()->where("post_id", $dbObj["id"])
             //   ->pluck('seller_id');
            $buyerPostRequest = new SellerPostBO();
            $buyerPostRequest
                ->setPostId($dbObj["id"])
                ->setPostId($dbObj["title"])
                ->setBuyerId($dbObj["buyer_id"])
                ->setServiceId($dbObj["lkp_service_id"])
                ->setLeadType($dbObj["lkp_lead_type"])
                ->setServiceSubType($dbObj["lkp_service_subtype"])
                ->setLastDateOfQuoteSubmission($dbObj["last_date_quote_submission"])
                ->setLastTimeOfQuoteSubmission($dbObj["last_time_quote_submission"])
               // ->setVisibleToSellers($selectedSellers)
                ->setViewCount($dbObj["post_view_count"])
                ->setIsPublic($dbObj["post_is_public"])
                ->setIsPrivate($dbObj["post_is_private"])
                ->setSysSolrSync($dbObj["sys_solr_sync"])
                ->setCreatedBy($dbObj["created_by"])
                ->setUpdatedBy($dbObj["updated_by"])
                ->setCreatedIP($dbObj["created_ip"])
                ->setUpdatedIP($dbObj["updated_ip"])
                ->setCreatedAt($dbObj["created_at"])
                ->setUpdatedAt($dbObj["updated_at"])
                ->setIsTermAccepted($dbObj["is_terms_accepted"])
                ->setOriginLocation($attributes["originLocation"])
                ->setDestinationLocation($attributes["destinationLocation"])
                ->setIsHazardous($attributes["isHazardous"])
                ->setHazardousAttributes($attributes["hazardousAttributes"])
                ->setAttributes($attributes["attributes"]);

            return $buyerPostRequest;
        }*/

}