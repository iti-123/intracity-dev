<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 10:51 PM
 */

namespace Api\BusinessObjects\Common;

/**
 * Class PackageDimensions
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class SellerQuoteFinalOffer
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