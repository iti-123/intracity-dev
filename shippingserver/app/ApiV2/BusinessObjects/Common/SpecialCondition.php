<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:47 PM
 */

namespace ApiV2\BusinessObjects\Common;


/**
 * Class SpecialCondition
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class SpecialCondition
{

    /**
     * @Type("Api\BusinessObjects\Common\TemperatureAttributes")
     * @SerializedName("temperatureAttributes")
     */
    public $temperatureAttributes;

    /**
     * @Type("Api\BusinessObjects\Common\ODC")
     * @SerializedName("ODC")
     */
    public $ODC;

    /**
     * @Type("Api\BusinessObjects\Common\GOH")
     * @SerializedName("GOH")
     */
    public $GOH;

    /**
     * @Type("Api\BusinessObjects\Common\TankTainer")
     * @SerializedName("tankTainers")
     */
    public $tankTainers;
}

class TemperatureAttributes
{

    /**
     * @Type("string")
     * @SerializedName("consignmentType")
     */
    public $consignmentType;

    /**
     * @Type("string")
     * @SerializedName("temperatureUnit")
     */
    public $temperatureUnit;

    /**
     * @Type("double")
     * @SerializedName("temperature")
     */
    public $temperature;
}

//TODO This class can extend Packaging Dimensions and specify  only additional field here
class ODC
{

    /**
     * @Type("string")
     * @SerializedName("dimensionUnit")
     */
    public $dimensionUnit;

    /**
     * @Type("double")
     * @SerializedName("$length")
     */
    public $length;

    /**
     * @Type("double")
     * @SerializedName("breadth")
     */
    public $breadth;

    /**
     * @Type("double")
     * @SerializedName("height")
     */
    public $height;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

    /**
     * @Type("double")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;

    /**
     * @Type("string")
     * @SerializedName("packagingMethod")
     */
    public $packagingMethod;

    /**
     * @Type("array<Api\BusinessObjects\Common\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}

class GOH
{

    /**
     * @Type("integer")
     * @SerializedName("numberOfBars")
     */
    public $numberOfBars;

    /**
     * @Type("integer")
     * @SerializedName("numberOfRopes")
     */
    public $numberOfRopes;

    /**
     * @Type("integer")
     * @SerializedName("knotsPerRope")
     */
    public $knotsPerRope;
}

class TankTainer
{

    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

    /**
     * @Type("double")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;
}
