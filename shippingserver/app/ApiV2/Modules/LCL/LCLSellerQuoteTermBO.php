<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 6:21 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\SellerQuoteBO;

class LCLSellerQuoteTermBO extends SellerQuoteBO
{
    /**
     * @Type("Api\Modules\LCL\LCLSellerTermQuoteAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;
}

class LCLSellerTermQuoteAttributes
{
    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("string")
     * @SerializedName("transitDays")
     */
    public $transitDays;

    /**
     * @Type("string")
     * @SerializedName("tracking")
     */
    public $tracking;

    /**
     * @Type("array<Api\Modules\LCL\LCLTermContainers>")
     * @SerializedName("containers")
     */
    public $containers = [];
}

class LCLTermContainers
{
    /**
     * @Type("string")
     * @SerializedName("packagingType")
     */
    public $packagingType;

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;

    /**
     * @Type("Api\BusinessObjects\Common\SellerQuoteInitialOffer")
     * @SerializedName("initialOffer")
     */
    public $initialOffer;

    /**
     * @Type("Api\BusinessObjects\Common\SellerQuoteCounterOffer")
     * @SerializedName("counterOffer")
     */
    public $counterOffer;

    /**
     * @Type("Api\BusinessObjects\Common\SellerQuoteFinalOffer")
     * @SerializedName("finalOffer")
     */
    public $finalOffer;
}
