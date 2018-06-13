<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 6:21 PM
 */

namespace Api\Modules\LCL;

use Api\BusinessObjects\Common\Carries;
use Api\BusinessObjects\SellerQuoteBO;

class LCLSellerQuoteSpotBO extends SellerQuoteBO
{

    /**
     * @Type("Api\Modules\LCL\LCLSellerQuoteAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class LCLSellerQuoteAttributes
{
    /**
     * @Type("array<Api\Modules\LCL\LCLCarriers>")
     * @SerializedName("carriers")
     */
    public $carriers = [];
}

class LCLCarriers
{
    /**
     * @Type("string")
     * @SerializedName("carrierName")
     */
    public $carrierName;

    /**
     * @Type("string")
     * @SerializedName("etd")
     */
    public $etd;

    /**
     * @Type("string")
     * @SerializedName("cfsCutOffDate")
     */
    public $cfsCutOffDate;

    /**
     * @Type("string")
     * @SerializedName("transitDays")
     */
    public $transitDays;

    /**
     * @Type("string")
     * @SerializedName("validTill")
     */
    public $validTill;

    /**
     * @Type("string")
     * @SerializedName("tracking")
     */
    public $tracking;

    /**
     * @Type("string")
     * @SerializedName("routingType")
     */
    public $routingType;


    /**
     * @Type("Api\Modules\LCL\LCLRoutingVia")
     * @SerializedName("routingVia")
     */
    public $routingVia;

    /**
     * @Type("string")
     * @SerializedName("chargeableWeight")
     */
    public $chargeableWeight;

    /**
     * @Type("string")
     * @SerializedName("minFreightPerCbm")
     */
    public $minFreightPerCbm;

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

class LCLRoutingVia
{
    /**
     * @Type("string")
     * @SerializedName("port1")
     */
    public $port1;

    /**
     * @Type("string")
     * @SerializedName("port2")
     */
    public $port2;

    /**
     * @Type("string")
     * @SerializedName("port3")
     */
    public $port3;

}
