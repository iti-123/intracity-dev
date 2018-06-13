<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 9:15 AM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerQuoteBO;

class FCLSellerQuoteSpotBO extends SellerQuoteBO
{

    /**
     * @Type("Api\Modules\FCL\FCLSellerQuoteAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class FCLSellerQuoteAttributes
{
    /**
     * @Type("array<Api\Modules\FCL\Carriers>")
     * @SerializedName("carriers")
     */
    public $carriers = [];
}

class Carriers
{
    /**
     * @Type("string")
     * @SerializedName("carrierStatus")
     */
    public $carrierStatus;

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
     * @SerializedName("cyCutOffDate")
     */
    public $cyCutOffDate;

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
     * @Type("Api\Modules\FCL\RoutingVia")
     * @SerializedName("routingVia")
     */
    public $routingVia;

    /**
     * @Type("array<Api\Modules\FCL\Containers>")
     * @SerializedName("containers")
     */
    public $containers = [];

    public function isRoutingTypeVia()
    {
        return $this->routingType == "Via";
    }
}

class RoutingVia
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

class Containers
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
     * @Type("Api\Modules\FCL\FCLSellerQuoteInitialOfferAttributes")
     * @SerializedName("initialOffer")
     */
    public $initialOffer;

    /**
     * @Type("Api\Modules\FCL\FCLSellerQuoteCounterOfferAttributes")
     * @SerializedName("counterOffer")
     */
    public $counterOffer;

    /**
     * @Type("Api\Modules\FCL\FCLSellerQuoteFinalOfferAttributes")
     * @SerializedName("finalOffer")
     */
    public $finalOffer;

}

class SpotFreightCharges
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

class SpotLocalCharges
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

class Carrier
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
     * @SerializedName("cyCutOffDate")
     */
    public $cyCutOffDate;

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
     * @Type("Api\Modules\FCL\RoutingVia")
     * @SerializedName("routingVia")
     */
    public $routingVia;

    /**
     * @Type("array<Api\Modules\FCL\Containers>")
     * @SerializedName("containers")
     */
    public $containers = [];
}

class FCLSellerQuoteInitialOfferAttributes
{
    /**
     * @Type("array<Api\Modules\FCL\SpotFreightCharges>")
     * @SerializedName("freightCharges")
     */
    public $freightCharges = [];

    /**
     * @Type("array<Api\Modules\FCL\SpotLocalCharges>")
     * @SerializedName("localCharges")
     */
    public $localCharges = [];
}

class FCLSellerQuoteCounterOfferAttributes
{
    /**
     * @Type("Api\Modules\FCL\SpotFreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("string")
     * @SerializedName("comments")
     */
    public $comments;

}

class FCLSellerQuoteFinalOfferAttributes
{
    /**
     * @Type("Api\Modules\FCL\SpotFreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("Api\Modules\FCL\SpotLocalCharges")
     * @SerializedName("localCharges")
     */
    public $localCharges;
}
