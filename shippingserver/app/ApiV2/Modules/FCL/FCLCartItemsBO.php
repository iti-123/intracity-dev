<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/16/2017
 * Time: 8:39 AM
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\CartItemsBO;

/**
 * Class FCLCartItemsBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLCartItemsBO extends CartItemsBO
{

    /**
     * @Type("Api\Modules\FCL\FCLCartItemsAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class FCLCartItemsAttributes
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
     * @Type("Api\Modules\FCL\FCLCartItemsContainers")
     * @SerializedName("containers")
     */
    public $containers;

    /**
     * @Type("string")
     * @SerializedName("carrierIndex")
     */
    public $carrierIndex;

    /**
     * @Type("Api\Modules\FCL\FCLCartItemsCarrierDetails")
     * @SerializedName("containers")
     */
    public $carrierDetails;

}

class FCLCartItemsContainers
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
     * @Type("Api\Modules\FCL\FCLCartItemsOffer")
     * @SerializedName("offer")
     */
    public $offer;

}

class FCLCartItemsOffer
{


}

class FCLCartItemsCarrierDetails
{


}