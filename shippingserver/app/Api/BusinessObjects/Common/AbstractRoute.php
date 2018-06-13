<?php

namespace Api\BusinessObjects\Common;
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-04-2017
 * Time: 17:43
 */

/**
 * Class AbstractRoute
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */


class AbstractRoute
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

    //TODO following two params to be moved to FCL specific class  not required for Airfreight
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
     * @Type("Api\BusinessObjects\Common\HazardousAttributes")
     * @SerializedName("hazardousAttributes")
     */
    public $hazardousAttributes;

    /**
     * @Type("string")
     * @SerializedName("specialConditionType")
     * One of temperature|tanktainer|odc|goh
     */
    //public $specialConditionType;

    /**
     * @Type("Api\BusinessObjects\Common\SpecialCondition")
     * @SerializedName("specialConditions")
     */
    public $specialConditions;

    /**
     * @Type("Api\BusinessObjects\Common\OriginCustoms")
     * @SerializedName("originCustoms")
     */
    public $originCustoms;


    /**
     * @Type("Api\BusinessObjects\Common\DestinationCustoms")
     * @SerializedName("destinationCustoms")
     */
    public $destinationCustoms;


    /**
     * @Type("Api\BusinessObjects\Common\ExportTPT")
     * @SerializedName("exportTPT")
     */
    public $exportTPT;


}


