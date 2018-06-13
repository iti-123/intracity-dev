<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 6:24 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\BuyerPostBO;


/**
 * Class LCLSpotBuyerPostBO
 * @package Api\Modules\LCL
 * @ExclusionPolicy("none")
 */
class LCLSpotBuyerPostBO extends BuyerPostBO
{

    /**
     * @Type("Api\Modules\LCL\LCLBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;
}


class LCLBuyerPostAttributes
{

    /**
     * @Type("Api\Modules\LCL\Route")
     * @SerializedName("route")
     */
    public $route;

}

class OriginCustoms
{

    /**
     * @Type("string")
     * @SerializedName("shippingBillType")
     */
    public $shippingBillType;

    /**
     * @Type("integer")
     * @SerializedName("numberOfBills")
     */
    public $numberOfBills;

    /**
     * @Type("string")
     * @SerializedName("isReturnable")
     */
    public $isReturnable;

    /**
     * @Type("string")
     * @SerializedName("returnableCategory")
     */
    public $returnableCategory;

    /**
     * @Type("string")
     * @SerializedName("otherInstructions")
     */
    public $otherInstructions;

    /**
     * @Type("array<Api\Modules\LCL\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}

class DestinationCustoms
{

    /**
     * @Type("string")
     * @SerializedName("otherInstructions")
     */
    public $importBillType;

    /**
     * @Type("integer")
     * @SerializedName("numberOfBills")
     */
    public $numberOfBills;

    /**
     * @Type("array<Api\Modules\LCL\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}

class ExportTPT
{

    /**
     * @Type("string")
     * @SerializedName("trailerType")
     */
    public $trailerType;
}

class Document
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

class Route
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
     * @Type("string")
     * @SerializedName("incoTerms")
     */
    public $incoTerms;

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
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("integer")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate;

    /**
     * @Type("string")
     * @SerializedName("priceType")
     */
    public $priceType;

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

    /**
     * @Type("string")
     * @SerializedName("specialConditionType")
     * One of temperature|tanktainer|odc|goh
     */
    //public $specialConditionType;

    /**
     * @Type("Api\Modules\LCL\SpecialCondition")
     * @SerializedName("specialConditions")
     */
    public $specialConditions;

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

class HazardousAttributes
{
    /**
     * @Type("string")
     * @SerializedName("imoClass")
     */
    public $imoClass;

    /**
     * @Type("string")
     * @SerializedName("imoSubclass")
     */
    public $imoSubclass;

    /**
     * @Type("double")
     * @SerializedName("flashpoint")
     */
    public $flashpoint;

    /**
     * @Type("string")
     * @SerializedName("flashpointUnit")
     */
    public $flashpointUnit;

    /**
     * @Type("string")
     * @SerializedName("properShippingName")
     */
    public $properShippingName;


    /**
     * @Type("string")
     * @SerializedName("technicalName")
     */
    public $technicalName;

    /**
     * @Type("string")
     * @SerializedName("packingGroup")
     */
    public $packingGroup;

    /**
     * @Type("array<Api\Modules\LCL\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}

Class packageDimensions
{


    /**
     * @Type("string")
     * @SerializedName("packagingType")
     */
    public $packagingType;

    /**
     * @Type("string")
     * @SerializedName("noOfPackages")
     */
    public $noOfPackages;

    /**
     * @Type("integer")
     * @SerializedName("length")
     */
    public $length;

    /**
     * @Type("integer")
     * @SerializedName("height")
     */
    public $height;

    /**
     * @Type("integer")
     * @SerializedName("breadth")
     */
    public $breadth;

    /**
     * @Type("string")
     * @SerializedName("dimensionUnit")
     */
    public $dimensionUnit;

    /**
     * @Type("string")
     * @SerializedName("totalCBM")
     */
    public $totalCBM;

    /**
     * @Type("double")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

}
