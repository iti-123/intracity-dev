<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/19/2017
 * Time: 7:33 PM
 */

namespace Api\Modules\AirFreight;

use Api\BusinessObjects\BuyerPostBO;
use Api\BusinessObjects\Common\AbstractRoute;
use Api\BusinessObjects\Common\AbstractServiceType;
use Api\BusinessObjects\Common\TermAttributes;


/**
 * Class AirFreightTermBuyerPostBO
 * @package Api\Modules\AirFreight
 * @ExclusionPolicy("none")
 */
class AirFreightTermBuyerPostBO extends BuyerPostBO
{

    /**
     * @Type("Api\Modules\AirFreight\AirFreightBuyerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;
}

class AirFreightBuyerPostAttributes extends TermAttributes
{

    /**
     * @Type("array<Api\Modules\AirFreight\AirFreightServiceType>")
     * @SerializedName("serviceType")
     */
    public $serviceType = [];

}


class AirFreightServiceType extends AbstractServiceType
{

    /**
     * @Type("array<Api\Modules\AirFreight\TermRoute>")
     * @SerializedName("routes")
     */
    public $routes = [];

}

class TermRoute extends AbstractRoute
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

