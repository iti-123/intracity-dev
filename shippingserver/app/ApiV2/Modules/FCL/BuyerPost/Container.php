<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:51 PM
 */
namespace ApiV2\Modules\FCL\BuyerPost;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;


/**
 * Class Container
 * @package Api\Modules\FCL\BuyerPost
 * @ExclusionPolicy("none")
 */
class Container
{

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("integer")
     * @SerializedName("quantity")
     */
    public $quantity;

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
     * @SerializedName("freightCharges")
     */
    public $freightCharges;


}