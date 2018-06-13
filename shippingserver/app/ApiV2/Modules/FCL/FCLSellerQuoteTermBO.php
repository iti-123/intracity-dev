<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/15/2017
 * Time: 10:59 PM
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\SellerQuoteBO;

class FCLSellerQuoteTermBO extends SellerQuoteBO
{
    /**
     * @Type("Api\Modules\FCL\FCLSellerTermQuoteAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;
}

class FCLSellerTermQuoteAttributes
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
     * @Type("array<Api\Modules\FCL\TermContainers>")
     * @SerializedName("containers")
     */
    public $containers = [];
}

class TermContainers
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
     * @Type("Api\Modules\FCL\FCLTermSellerQuoteInitialOfferAttributes")
     * @SerializedName("initialOffer")
     */
    public $initialOffer;

    /**
     * @Type("Api\Modules\FCL\FCLTermSellerQuoteCounterOfferAttributes")
     * @SerializedName("counterOffer")
     */
    public $counterOffer;

    /**
     * @Type("Api\Modules\FCL\FCLTermSellerQuoteFinalOfferAttributes")
     * @SerializedName("finalOffer")
     */
    public $finalOffer;
}

class FCLTermSellerQuoteInitialOfferAttributes
{
    /**
     * @Type("Api\Modules\FCL\TermFreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("Api\Modules\FCL\TermLocalCharges")
     * @SerializedName("localCharges")
     */
    public $localCharges;
}

class FCLTermSellerQuoteCounterOfferAttributes
{
    /**
     * @Type("Api\Modules\FCL\TermFreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;

}

class FCLTermSellerQuoteFinalOfferAttributes
{
    /**
     * @Type("Api\Modules\FCL\TermFreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("Api\Modules\FCL\TermLocalCharges")
     * @SerializedName("localCharges")
     */
    public $localCharges;

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;
}

class TermFreightCharges
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

    /**
     * @Type("string")
     * @SerializedName("unit")
     */
    public $unit;

}

class TermLocalCharges
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