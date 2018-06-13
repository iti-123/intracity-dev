<?php

namespace ApiV2\Modules\FCL;

use ApiV2\Framework\ICartItemTransformer;
use ApiV2\Framework\SerializerServiceFactory;
use Log;

class FCLCartItemTransformer implements ICartItemTransformer
{

    public function createBo()
    {

        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize("{}", 'Api\Modules\FCL\FCLCartItemsBO', 'json');
        return $post;

    }

    public function ui2bo_save($payload)
    {

        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLCartItemsBO', 'json');
        return $post;

    }

    public function model2boGet($model)
    {
        // TODO: Implement model2boGet() method.
    }


    public function model2boSave($model)
    {
        $response = 'FCLCartIteamTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelDelete($bo)
    {
        $response = 'FCLCartIteamTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'FCLCartIteamTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function bo2modelGet($bo)
    {
        $response = 'FCLCartIteamTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function cart_xls2bo_save(array $details)
    {


        $bos = [];

        foreach ($details as $row) {
            $counter = 0;
            $bo = new FCLCartItemsBO();
            $bo->loadPort = $row[$counter++];
            $bo->dischargePort = $row[$counter++];
            $bo->title = $row[$counter++];
            $bo->buyerId = $row[$counter++];
            $bo->sellerId = $row[$counter++];
            $bo->serviceId = $row[$counter++];
            $bo->serviceType = $row[$counter++];
            $bo->CarrierIndex = $row[$counter++];
            $bo->postType = $row[$counter++];
            $bo->sellerQuoteId = $row[$counter++];
            $bo->commodityType = $row[$counter++];
            $bo->cargoReadyDate = $row[$counter++];
            $bo->buyerPostId = $row[$counter++];
            $bo->additionalDetails = $row[$counter++];
            $bo->isConsignmentInsured = $row[$counter++];

            /*   if($bo->isConsignmentInsured) {
                       //$bo->insuranceDetails->cargoType=$row[$counter++];
                       //$bo->insuranceDetails->sumAssured=$row[$counter++];
                       //$bo->insuranceDetails->bov=$row[$counter++];
               } */
            $cargoType = $row[$counter++];
            $sumAssured = $row[$counter++];
            $bov = $row[$counter++];

            $bo->isGsaAccepted = $row[$counter++];
            $bo->consignorName = $row[$counter++];
            $bo->consignorEmail = $row[$counter++];
            $bo->consignorMobile = $row[$counter++];
            $bo->consignorAddress = $row[$counter++];
            $bo->consignorAddress2 = $row[$counter++];
            $bo->consignorAddress3 = $row[$counter++];
            $bo->consignorPincode = $row[$counter++];
            $bo->consignorCity = $row[$counter++];
            $bo->consignorState = $row[$counter++];
            $bo->consignorCountry = $row[$counter++];
            $bo->consigneeName = $row[$counter++];
            $bo->consigneeEmail = $row[$counter++];
            $bo->consigneeMobile = $row[$counter++];
            $bo->consigneeAddress = $row[$counter++];
            $bo->consigneeAddress2 = $row[$counter++];
            $bo->consigneeAddress3 = $row[$counter++];
            $bo->consigneePincode = $row[$counter++];
            $bo->consigneeCity = $row[$counter++];
            $bo->consigneeState = $row[$counter++];
            $bo->consigneeCountry = $row[$counter++];
            $bo->attributes = json_decode('{"containers":[{"containerType":"20 Dry Standard","quantity":3,"weightUnit":"MTs","grossWeight":20,"freightCharges":"","offer":{"freightCharges":{"chargeType":"Total Freight Charges","currency":"USD","amount":"2300","unit":null},"localCharges":{"chargeType":"Total Local Charges","currency":"INR","amount":"1000"}}},{"containerType":"40 Dry Standard","quantity":4,"weightUnit":"MTs","grossWeight":25,"freightCharges":"","offer":{"freightCharges":{"chargeType":"Total Freight Charges","currency":"USD","amount":"2000","unit":null},"localCharges":{"chargeType":"Total Local Charges","currency":"INR","amount":"1000"}}}],"carrierDetails":{"carrierName":"carrier - 2","etd":"1490918400000","cyCutOffDate":"1490918400000","transitDays":"3","validTill":null,"tracking":"No Tracking","routingType":"Direct","routingVia":{"port1":"","port2":"","port3":""}},"carrierIndex":"1","fclTypeOfBol":"OBL","consignmentType":"1","consignmentValue":"2312432","blrequirement":"MBL"}');

            array_push($bos, $bo);
        }

        return $bos;

    }


}