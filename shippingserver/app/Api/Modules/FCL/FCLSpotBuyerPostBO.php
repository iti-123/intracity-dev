<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 17:42
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\BuyerPostBO;

/**
 * Class FCLSpotBuyerPostBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLSpotBuyerPostBO extends BuyerPostBO
{


    /**
     * @Type("Api\Modules\FCL\FCLBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class FCLBuyerPostAttributes
{

    /**
     * @Type("Api\Modules\FCL\Route")
     * @SerializedName("route")
     */
    public $route;

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
     * @Type("string")
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
