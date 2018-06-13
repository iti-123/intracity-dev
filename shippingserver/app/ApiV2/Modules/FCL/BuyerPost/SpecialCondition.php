<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:47 PM
 */
namespace ApiV2\Modules\FCL\BuyerPost;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;


/**
 * Class SpecialCondition
 * @package Api\Modules\FCL\BuyerPost
 * @ExclusionPolicy("none")
 */
class SpecialCondition
{

    /**
     * @Type("Api\Modules\FCL\BuyerPost\TemperatureAttributes")
     * @SerializedName("temperatureAttributes")
     */
    public $temperatureAttributes;

    /**
     * @Type("Api\Modules\FCL\BuyerPost\ODC")
     * @SerializedName("ODC")
     */
    public $ODC;

    /**
     * @Type("Api\Modules\FCL\BuyerPost\GOH")
     * @SerializedName("GOH")
     */
    public $GOH;

    /**
     * @Type("Api\Modules\FCL\BuyerPost\TankTainer")
     * @SerializedName("tankTainers")
     */
    public $tankTainers;
}

class TemperatureAttributes{

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

class ODC {

    /**
     * @Type("string")
     * @SerializedName("dimensionUnit")
     */
    public $dimensionUnit;

    /**
     * @Type("double")
     * @SerializedName("length")
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
     * @Type("array<Api\Modules\FCL\BuyerPost\ODCDocument>")
     * @SerializedName("documents")
     */
    public $documents=[];
}

class GOH {

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

class TankTainer{

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

class ODCDocument {

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