<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/23/2017
 * Time: 4:13 PM
 */

namespace ApiV2\Modules\AirFreight;


use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\Common\AbstractRoute;


class AirFreightSpotBuyerPostBO extends BuyerPostBO
{


    /**
     * @Type("Api\Modules\AirFreight\AirFreightBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class AirFreightBuyerPostAttributes
{
    /**
     * @Type("array<Api\Modules\AirFreight\Route>")
     * @SerializedName("routes")
     */
    public $routes = [];
}

class Route extends AbstractRoute
{


    /**
     * @Type("string")
     * @SerializedName("airFreightType")
     */
    public $airFreightType;

    /**
     * @Type("Api\Modules\AirFreight\TemperatureAttributes")
     * @SerializedName("temperatureAttributes")
     */
    public $temperatureAttributes;

    /**
     * @Type("boolean")
     * @SerializedName("isStackable")
     */
    public $isStackable;


    /**
     * @Type("boolean")
     * @SerializedName("isRadioActive")
     */
    public $isRadioActive;

    /**
     * @Type("array<Api\BusinessObjects\Common\PackageDimensions>")
     * @SerializedName("packageDimensions")
     */
    public $packageDimensions = [];

}

class TemperatureAttributes
{

    /**
     * @Type("string")
     * @SerializedName("isPassive")
     */
    public $isPassive;

    /**
     * @Type("string")
     * @SerializedName("isActive")
     */
    public $isActive;


    /**
     * @Type("double")
     * @SerializedName("temperature")
     */
    public $temperature;

    /**
     * @Type("double")
     * @SerializedName("temperatureTo")
     */
    public $temperatureTo;

    /**
     * @Type("string")
     * @SerializedName("temperatureUnit")
     */
    public $temperatureUnit;
}



