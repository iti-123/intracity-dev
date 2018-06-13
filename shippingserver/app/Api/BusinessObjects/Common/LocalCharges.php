<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 11:00 PM
 */

namespace Api\BusinessObjects\Common;

/**
 * Class PackageDimensions
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class LocalCharges
{
    /**
     * @Type("string")
     * @SerializedName("chargeType")
     */
    public $chargeType;

    /**
     * @Type("string")
     * @SerializedName("currency")
     */
    public $currency;

    /**
     * @Type("string")
     * @SerializedName("amount")
     */
    public $amount;
}