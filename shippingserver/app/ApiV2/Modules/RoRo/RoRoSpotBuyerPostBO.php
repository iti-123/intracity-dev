<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:07 AM
 */

namespace ApiV2\Modules\RoRo;

use ApiV2\BusinessObjects\BuyerPostBO;

/**
 * Class FCLSpotBuyerPostBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class RoRoSpotBuyerPostBO extends BuyerPostBO
{

    /**
     * @Type("Api\Modules\RoRo\RoROBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class RoROBuyerPostAttributes
{
    /**
     * @Type("array<Api\Modules\RoRO\Route>")
     * @SerializedName("routes")
     */
    public $routes = [];
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
     * @Type("boolean")
     * @SerializedName("isTyre")
     */
    public $isTyre;

    /**
     * @Type("boolean")
     * @SerializedName("isChain")
     */
    public $isChain;

    /**
     * @Type("boolean")
     * @SerializedName("isMafi")
     */
    public $isMafi;

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
     * @Type("boolean")
     * @SerializedName("isSelfDriven")
     */
    public $isSelfDriven;


    /**
     * @Type("boolean")
     * @SerializedName("isTowable")
     */
    public $isTowable;

    /**
     * @Type("integer")
     * @SerializedName("noOfPins")
     */
    public $noOfPins;

    /**
     * @Type("array<Api\Modules\RoRo\Measurement>")
     * @SerializedName("measurement")
     */
    public $measurement = [];

    /**
     * @Type("Api\Modules\RoRo\OriginCustoms")
     * @SerializedName("originCustoms")
     */
    public $originCustoms;


    /**
     * @Type("Api\Modules\RoRo\DestinationCustoms")
     * @SerializedName("destinationCustoms")
     */
    public $destinationCustoms;


    /**
     * @Type("Api\Modules\RoRo\ExportTPT")
     * @SerializedName("exportTPT")
     */
    public $exportTPT;

}

class Measurement
{

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;

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
     * @Type("array<Api\Modules\RoRo\Document>")
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
     * @Type("array<Api\Modules\RoRo\Document>")
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