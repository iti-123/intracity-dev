<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 6:41 PM
 */

namespace ApiV2\Modules\LCL;

/**
 * Class FCLTermBuyerPostBO
 * @package Api\Modules\LCL
 * @ExclusionPolicy("none")
 */
class LCLTermBuyerPostBO extends LCLSpotBuyerPostBO
{
    /**
     * @Type("Api\Modules\LCL\LCLTermBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;
}

class LCLTermBuyerPostAttributes
{

    /**
     * @Type("string")
     * @SerializedName("validFrom")
     */
    public $validFromt;

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
     * @SerializedName("emdText")
     */
    public $emdText;

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
     * @Type("Api\Modules\LCL\RfpEligibility")
     * @SerializedName("rfpEligibility")
     */
    public $rfpEligibility;

    /**
     * @Type("array<Api\Modules\LCL\BidTermsAndConditionsDocs>")
     * @SerializedName("bidTermsAndConditionsDocs")
     */
    public $bidTermsAndConditionsDocs = [];

    /**
     * @Type("array<Api\Modules\LCL\ServiceType>")
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
     * @Type("array<Api\Modules\LCL\TermRoute>")
     * @SerializedName("routes")
     */
    public $routes = [];

    /**
     * @Type("Api\Modules\LCL\OriginCustoms")
     * @SerializedName("originCustoms")
     */
    public $originCustoms;


    /**
     * @Type("Api\Modules\LCL\DestinationCustoms")
     * @SerializedName("destinationCustoms")
     */
    public $destinationCustoms;


    /**
     * @Type("Api\Modules\LCL\ExportTPT")
     * @SerializedName("exportTPT")
     */
    public $exportTPT;

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
     * @Type("integer")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate;


    /**
     * @Type("boolean")
     * @SerializedName("isFumigationRequired")
     */
    public $isFumigationRequired;


    /**
     * @Type("boolean")
     * @SerializedName("isStackable")
     */
    public $isStackable;

    /**
     * @Type("boolean")
     * @SerializedName("isHazardous")
     */
    public $isHazardous;

    /**
     * @Type("Api\Modules\LCL\HazardousAttributes")
     * @SerializedName("hazardousAttributes")
     */
    public $hazardousAttributes;

    /**
     * @Type("array<Api\Modules\LCL\PackageDimensions>")
     * @SerializedName("packageDimensions")
     */
    public $packageDimensions = [];

}
