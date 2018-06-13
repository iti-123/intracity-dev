<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/20/17
 * Time: 3:25 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\OrderBO;

/**
 * Class FCLOrderBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLOrderBO extends OrderBO
{

    /**
     * @Type("Api\Modules\FCL\FCLOrdersAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class FCLOrdersAttributes
{

    /**
     * @Type("string")
     * @SerializedName("consignmentType")
     */
    public $consignmentType;

    /**
     * @Type("string")
     * @SerializedName("consignmentValue")
     */
    public $consignmentValue;

    /**
     * @Type("string")
     * @SerializedName("fclTypeOfBol")
     */
    public $fclTypeOfBol;

    /**
     * @Type("string")
     * @SerializedName("blrequirement")
     */
    public $blrequirement;

    /**
     * @Type("Api\Modules\FCL\FCLOrderContainers")
     * @SerializedName("containers")
     */
    public $containers;

    /**
     * @Type("string")
     * @SerializedName("carrierIndex")
     */
    public $carrierIndex;

    /**
     * @Type("Api\Modules\FCL\FCLOrderCarrierDetails")
     * @SerializedName("containers")
     */
    public $carrierDetails;

}

class FCLOrderContainers
{

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

    /**
     * @Type("string")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;

    /**
     * @Type("Api\Modules\FCL\FCLOrderOffer")
     * @SerializedName("offer")
     */
    public $offer;

}

class FCLOrderOffer
{


}

class FCLOrderCarrierDetails
{


}

class FCLCROBO extends OrderBO
{

    /**
     * @Type("string")
     * @SerializedName("fileName")
     */
    public $fileName;

    /**
     * @Type("string")
     * @SerializedName("filePath")
     */
    public $filePath;

}

class FCLSIBO extends OrderBO
{

    /**
     * @Type("string")
     * @SerializedName("fileName")
     */
    public $fileName;

    /**
     * @Type("string")
     * @SerializedName("filePath")
     */
    public $filePath;

}

class FCLDraftBLBO extends OrderBO
{

    /**
     * @Type("string")
     * @SerializedName("fileName")
     */
    public $fileName;

    /**
     * @Type("string")
     * @SerializedName("filePath")
     */
    public $filePath;

}
