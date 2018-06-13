<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/15/2017
 * Time: 3:18 PM
 */

namespace ApiV2\BusinessObjects;

/**
 * Class CartItemsBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class CartItemsBO
{

    /**
     * @Type("Api\BusinessObjects\InitialDetails")
     * @SerializedName("initialDetails")
     */
    public $initialDetails;

    /**
     * @Type("string")
     * @SerializedName("cartId")
     */
    public $cartId;

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
     * @Type("Api\BusinessObjects\InsuranceDetails")
     * @SerializedName("insuranceDetails")
     */
    public $insuranceDetails;

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
     * @Type("Api\BusinessObjects\SearchData")
     * @SerializedName("searchData")
     */
    public $searchData;

    /**
     * @Type("Api\BusinessObjects\Charges")
     * @SerializedName("charges")
     */
    public $charges;

    /**
     * @Type("Api\BusinessObjects\IndentData")
     * @SerializedName("indentData")
     */
    public $indentData;

}

/**
 * Class InitialDetails
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class InitialDetails
{

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
     * @SerializedName("postType")
     */
    public $postType;

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
     * @Type("Api\BusinessObjects\SearchData")
     * @SerializedName("searchData")
     */
    public $searchData;

    /**
     * @Type("string")
     * @SerializedName("carrierIndex")
     */
    public $carrierIndex;

    /**
     * @Type("Api\BusinessObjects\IndentData")
     * @SerializedName("indentData")
     */
    public $indentData;

}

/**
 * Class SearchData
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class SearchData
{

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
     * @Type("array<Api\BusinessObjects\Containers>")
     * @SerializedName("containers")
     */
    public $containers;

}

/**
 * Class IndentData
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class IndentData
{

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
     * @Type("array<Api\BusinessObjects\Containers>")
     * @SerializedName("containers")
     */
    public $containers;

}

/**
 * Class Charges
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class Charges
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

}

/**
 * Class Containers
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class Containers
{


    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

    /**
     * @Type("string")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;

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