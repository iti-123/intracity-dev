<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/19/2017
 * Time: 7:33 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\BuyerPostBO;


/**
 * Class FCLTermBuyerPostBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLTermBuyerPostBO extends BuyerPostBO
{

    /**
     * @Type("Api\Modules\FCL\FCLTermBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;
}

class FCLTermBuyerPostAttributes
{
    /**
     * @Type("string")
     * @SerializedName("validFrom")
     */
    public $validFrom;

    /**
     * @Type("string")
     * @SerializedName("validTo")
     */
    public $validTo;

    /**
     * @Type("string")
     * @SerializedName("emdAmount")
     */
    public $emdAmount;

    /**
     * @Type("string")
     * @SerializedName("emdMode")
     */
    public $emdMode;

    /**
     * @Type("string")
     * @SerializedName("awardCriteria")
     */
    public $awardCriteria;

    /**
     * @Type("string")
     * @SerializedName("contractAllotment")
     */
    public $contractAllotment;

    /**
     * @Type("string")
     * @SerializedName("paymentTerms")
     */
    public $paymentTerms;

    /**
     * @Type("string")
     * @SerializedName("credit")
     */
    public $credit;

    /**
     * @Type("string")
     * @SerializedName("creditDays")
     */
    public $creditDays;

    /**
     * @Type("string")
     * @SerializedName("comments")
     */
    public $comments;

    /**
     * @Type("Api\Modules\FCL\RfpEligibility")
     * @SerializedName("rfpEligibility")
     */
    public $rfpEligibility;

    /**
     * @Type("array<Api\Modules\FCL\BidTermsAndConditionsDocs>")
     * @SerializedName("bidTermsAndConditionsDocs")
     */
    public $bidTermsAndConditionsDocs = [];

    /**
     * @Type("array<Api\Modules\FCL\ServiceType>")
     * @SerializedName("serviceType")
     */
    public $serviceType = [];

}

class BidTermsAndConditionsDocs
{
    /**
     * @Type("string")
     * @SerializedName("documentId")
     */
    public $documentId;

    /**
     * @Type("string")
     * @SerializedName("documentName")
     */
    public $documentName;
}

class RfpEligibility
{

    /**
     * @Type("string")
     * @SerializedName("avgTurnOverLastThreeYears")
     */
    public $avgTurnOverLastThreeYears;

    /**
     * @Type("string")
     * @SerializedName("incometaxAssement")
     */
    public $incometaxAssement;

    /**
     * @Type("string")
     * @SerializedName("numberOfYearsInBusiness")
     */
    public $numberOfYearsInBusiness;

    /**
     * @Type("string")
     * @SerializedName("termContractWithOther")
     */
    public $termContractWithOther;


}

class ServiceType
{
    /**
     * @Type("string")
     * @SerializedName("serviceSubType")
     */
    public $serviceSubType;

    /**
     * @Type("string")
     * @SerializedName("originLocation")
     */
    public $originLocation;

    /**
     * @Type("string")
     * @SerializedName("destinationLocation")
     */
    public $destinationLocation;

    /**
     * @Type("array<Api\Modules\FCL\TermRoute>")
     * @SerializedName("routes")
     */
    public $routes = [];

}

class TermRoute
{
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
     * @SerializedName("incoTerms")
     */
    public $incoTerms;

    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("string")
     * @SerializedName("commodityDescription")
     */
    public $commodityDescription;

    /**
     * @Type("string")
     * @SerializedName("packagingType")
     */
    public $packagingType;

    /**
     * @Type("integer")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate;


    /**
     * @Type("boolean")
     * @SerializedName("isFumigationRequired")
     */
    public $isFumigationRequired = false;


    /**
     * @Type("boolean")
     * @SerializedName("isFactoryStuffingRequired")
     */
    public $isFactoryStuffingRequired = false;

    /**
     * @Type("boolean")
     * @SerializedName("isHazardous")
     */
    public $isHazardous = false;

    /**
     * @Type("Api\Modules\FCL\BuyerPost\HazardousAttributes")
     * @SerializedName("hazardousAttributes")
     */
    public $hazardousAttributes;


    /**
     * @Type("array<Api\Modules\FCL\BuyerPost\Container>")
     * @SerializedName("containers")
     */
    public $containers = [];

    /**
     * @Type("string")
     * @SerializedName("specialConditionType")
     * One of temperature|tanktainer|odc|goh
     */
    public $specialConditionType = "None";

    /**
     * @Type("Api\Modules\FCL\BuyerPost\SpecialCondition")
     * @SerializedName("specialConditions")
     */
    public $specialConditions;

    /**
     * @Type("Api\Modules\FCL\BuyerPost\OriginCustoms")
     * @SerializedName("originCustoms")
     */
    public $originCustoms;


    /**
     * @Type("Api\Modules\FCL\BuyerPost\DestinationCustoms")
     * @SerializedName("destinationCustoms")
     */
    public $destinationCustoms;


    /**
     * @Type("Api\Modules\FCL\BuyerPost\ExportTPT")
     * @SerializedName("exportTPT")
     */
    public $exportTPT;

}
