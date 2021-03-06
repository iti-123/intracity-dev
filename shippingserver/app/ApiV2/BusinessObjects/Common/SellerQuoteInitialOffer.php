<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 10:50 PM
 */

namespace ApiV2\BusinessObjects\Common;

/**
 * Class PackageDimensions
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class SellerQuoteInitialOffer
{
    /**
     * @Type("Api\BusinessObjects\Common\FreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("Api\BusinessObjects\Common\LocalCharges")
     * @SerializedName("localCharges")
     */
    public $localCharges;
}