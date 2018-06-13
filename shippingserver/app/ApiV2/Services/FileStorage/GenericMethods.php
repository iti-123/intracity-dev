<?php

namespace ApiV2\Services\FileStorage;

use ApiV2\Requests\FCLBuyerPostRequest;
use App\SelectedSellers as SelectedSellers;


class GenericMethods
{
    public static function convert_db_object_to_json($buyerPost)
    {
        $dbObj = json_decode($buyerPost, true);
        $attributes = GenericMethods::get_attributes_json($dbObj["attributes"]);
        $selectedSellers = SelectedSellers::all()->where("post_id", $dbObj["id"])
            ->pluck('seller_id');
        $buyerPostRequest = new FCLBuyerPostRequest();
        $buyerPostRequest
            ->setPostId($dbObj["id"])
            ->setBuyerId($dbObj["buyer_id"])
            ->setServiceId($dbObj["lkp_service_id"])
            ->setLeadType($dbObj["lkp_lead_type"])
            ->setServiceSubType($dbObj["lkp_service_subtype"])
            ->setLastDateOfQuoteSubmission($dbObj["last_date_quote_submission"])
            ->setLastTimeOfQuoteSubmission($dbObj["last_time_quote_submission"])
            ->setVisibleToSellers($selectedSellers)
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
    }

    public static function get_attributes_json($value)
    {
        $value = str_replace(array("\n", "\r", "\t"), '', $value);
        $attributesObj = json_decode($value, true);
        return $attributesObj;
    }

    public static function convert_json_to_db_object($buyerPostRequest)
    {

        $jsonObj = json_decode($buyerPostRequest, true);
        $buyerPostRequest = new FCLBuyerPostRequest();
        $buyerPostRequest
            ->setBuyerId($jsonObj["buyerId"])
            ->setServiceId($jsonObj["serviceId"])
            ->setLeadType($jsonObj["leadType"])
            ->setServiceSubType($jsonObj["serviceSubType"])
            ->setLastDateOfQuoteSubmission($jsonObj["lastDateOfQuoteSubmission"])
            ->setLastTimeOfQuoteSubmission($jsonObj["lastTimeOfQuoteSubmission"])
            ->setVisibleToSellers($jsonObj["visibleToSellers"])
            ->setViewCount($jsonObj["viewCount"])
            ->setIsPublic($jsonObj["publicVisibility"])
            ->setIsPrivate($jsonObj["privateVisibility"])
            ->setSysSolrSync($jsonObj["sysSolrSync"])
            ->setCreatedBy($jsonObj["createdBy"])
            ->setUpdatedBy($jsonObj["updatedBy"])
            ->setCreatedIP($jsonObj["createdIP"])
            ->setUpdatedIP($jsonObj["updatedIP"])
            ->setCreatedAt($jsonObj["createdAt"])
            ->setUpdatedAt($jsonObj["updatedAt"])
            ->setIsTermAccepted($jsonObj["isTermAccepted"])
            ->setOriginLocation($jsonObj["originLocation"])
            ->setDestinationLocation($jsonObj["destinationLocation"])
            ->setIsHazardous($jsonObj["isHazardous"])
            ->setHazardousAttributes($jsonObj["hazardousAttributes"])
            ->setAttributes($jsonObj["attributes"]);

        return $buyerPostRequest;
    }

}

