<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 14:11
 */

namespace ApiV2\BusinessObjects;


use ApiV2\Framework\Workflow\Transitionable;

class OrderBO implements Transitionable
{

    /**
     * @Type("string")
     * @SerializedName("orderId")
     */
    public $orderId;

    /**
     * @Type("string")
     * @SerializedName("orderBatchId")
     */
    public $orderBatchId;

    /**
     * @Type("string")
     * @SerializedName("orderNo")
     */
    public $orderNo;

    /**
     * @Type("string")
     * @SerializedName("messageCount")
     */
    public $messageCount;

    /**
     * @Type("string")
     * @SerializedName("documentCount")
     */
    public $documentCount;

    /**
     * @Type("string")
     * @SerializedName("orderStatus")
     */
    public $orderStatus;

    /**
     * @Type("string")
     * @SerializedName("orderStatusLabel")
     */
    public $orderStatusLabel;

    /**
     * List all allowed transitions for the current order state
     * @var array
     */
    public $allowedTransitions = [];

    /**
     * @Type("string")
     * @SerializedName("serviceId")
     */
    public $serviceId;

    /**
     * @Type("string")
     * @SerializedName("serviceType")
     */
    public $serviceType;

    /**
     * @Type("string")
     * @SerializedName("sellerId")
     */
    public $sellerId;

    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;

    /**
     * @Type("string")
     * @SerializedName("buyerPostId")
     */

    public $buyerPostId;

    /**
     * @Type("string")
     * @SerializedName("sellerQuoteId")
     */

    public $sellerQuoteId;

    /**
     * @Type("string")
     * @SerializedName("sellerName")
     */
    public $sellerName;


    /**
     * @Type("string")
     * @SerializedName("buyerName")
     */
    public $buyerName;

    /**
     * @Type("string")
     * @SerializedName("title")
     */

    public $title;

    /**
     * @Type("string")
     * @SerializedName("commodityType")
     */

    public $commodityType;

    /**
     * @Type("string")
     * @SerializedName("cargoReadyDate")
     */

    public $cargoReadyDate;

    /**
     * @Type("string")
     * @SerializedName("loadPort")
     */

    public $loadPort;

    /**
     * @Type("string")
     * @SerializedName("dischargePort")
     */

    public $dischargePort;

    /**
     * @Type("string")
     * @SerializedName("leadType")
     */

    public $leadType;

    /**
     * @Type("string")
     * @SerializedName("postType")
     */
    public $postType;

    /**
     * @Type("string")
     * @SerializedName("freightCharges")
     */

    public $freightCharges;

    /**
     * @Type("string")
     * @SerializedName("localCharges")
     */

    public $localCharges;

    /**
     * @Type("string")
     * @SerializedName("serviceTax")
     */

    public $serviceTax;

    /**
     * @Type("string")
     * @SerializedName("insuranceCharges")
     */

    public $insuranceCharges;

    /**
     * @Type("string")
     * @SerializedName("paymentStatus")
     */

    public $paymentStatus;

    /**
     * @Type("string")
     * @SerializedName("consignorName")
     */

    public $consignorName;


    /**
     * @Type("string")
     * @SerializedName("consignorEmail")
     */

    public $consignorEmail;

    /**
     * @Type("string")
     * @SerializedName("consignorMobile")
     */
    public $consignorMobile;


    /**
     * @Type("string")
     * @SerializedName("consignorAddress1")
     */
    public $consignorAddress1;


    /**
     * @Type("string")
     * @SerializedName("consignorAddress2")
     */
    public $consignorAddress2;

    /**
     * @Type("string")
     * @SerializedName("consignorAddress3")
     */
    public $consignorAddress3;


    /**
     * @Type("string")
     * @SerializedName("consignorPincode")
     */
    public $consignorPincode;

    /**
     * @Type("string")
     * @SerializedName("consignorCity")
     */
    public $consignorCity;

    /**
     * @Type("string")
     * @SerializedName("consignorState")
     */

    public $consignorState;

    /**
     * @Type("string")
     * @SerializedName("consignorCountry")
     */

    public $consignorCountry;


    /**
     * @Type("string")
     * @SerializedName("consigneeName")
     */

    public $consigneeName;

    /**
     * @Type("string")
     * @SerializedName("consigneeAddress1")
     */

    /**
     * @Type("string")
     * @SerializedName("consigneeEmail")
     */
    public $consigneeEmail;

    /**
     * @Type("string")
     * @SerializedName("consigneeMobile")
     */
    public $consigneeMobile;

    /**
     * @Type("string")
     * @SerializedName("consigneeAddress1")
     */
    public $consigneeAddress1;


    /**
     * @Type("string")
     * @SerializedName("consigneeAddress2")
     */
    public $consigneeAddress2;


    /**
     * @Type("string")
     * @SerializedName("consigneeAddress3")
     */
    public $consigneeAddress3;


    /**
     * @Type("string")
     * @SerializedName("consigneePincode")
     */
    public $consigneePincode;

    /**
     * @Type("string")
     * @SerializedName("consigneeCity")
     */
    public $consigneeCity;


    /**
     * @Type("string")
     * @SerializedName("consigneeState")
     */
    public $consigneeState;

    /**
     * @Type("string")
     * @SerializedName("consigneeCountry")
     */
    public $consigneeCountry;


    /**
     * @Type("string")
     * @SerializedName("isConsignmentInsured")
     */

    public $isConsignmentInsured;

    /**
     * @Type("integer")
     * @SerializedName("isGsaAccepted")
     */
    public $isGsaAccepted;

    /**
     * @Type("string")
     * @SerializedName("additionalDetails")
     */

    public $additionalDetails;

    /**
     * @Type("Api\BusinessObjects\OrderCharges")
     * @SerializedName("charges")
     */
    public $charges;
}


class InsuranceDetails
{

    /**
     * @Type("string")
     * @SerializedName("bov")
     */
    public $bov;

    /**
     * @Type("string")
     * @SerializedName("cargoType")
     */
    public $cargoType;

    /**
     * @Type("string")
     * @SerializedName("discription")
     */
    public $discription;

    /**
     * @Type("string")
     * @SerializedName("sumAssured")
     */
    public $sumAssured;

}

/**
 * Class Charges
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class OrderCharges
{

    /**
     * @Type("float")
     * @SerializedName("freightCharges")
     */
    public $freightCharges = 0;

    /**
     * @Type("float")
     * @SerializedName("localCharges")
     */
    public $localCharges = 0;

    /**
     * @Type("float")
     * @SerializedName("insuranceCharges")
     */
    public $insuranceCharges = 0;

    /**
     * @Type("float")
     * @SerializedName("serviceTax")
     */
    public $serviceTax = 0;

    /**
     * @Type("float")
     * @SerializedName("serviceTax")
     */
    public $totalCharges = 0;

}
