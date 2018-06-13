<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 13-02-2017
 * Time: 16:24
 */

namespace ApiV2\Modules\AirFreight;

use ApiV2\BusinessObjects\SellerPostBO;


/**
 * Class AirFreightSellerPostBO
 * @package Api\Modules\AirFreight
 * @ExclusionPolicy("none")
 */
class AirFreightSellerPostBO extends SellerPostBO
{

    /**
     * @Type("array<Api\Modules\AirFreight\AirFreightSellerPostAttributes>")
     * @SerializedName("attributes")
     */
    public $attributes;

    /**
     * @Type("Api\Modules\AirFreight\Discount")
     * @SerializedName("discount")
     */
    public $discount;    //This discount is applicable at Rate Card Level

}

class  Discount
{
    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $sellerId;
    /**
     * @Type("string")
     * @SerializedName("discountType")
     */
    public $discountType;
    /**
     * @Type("string")
     * @SerializedName("discount")
     */
    public $discount;
    /**
     * @Type("string")
     * @SerializedName("creditDays")
     */
    public $creditDays;
}

class AirFreightSellerPostAttributes
{

    /**
     * @Type("array<Api\Modules\AirFreight\AirFreightSellerPostPair>")
     * @SerializedName("portpair")
     */
    public $portpair = [];
    /**
     * @Type("array<Api\Modules\AirFreight\Discount>")
     * @SerializedName("discount")
     */
    public $discount = [];    //This discount is applicable at Rate Card Level


}

class AirFreightSellerPostPair
{

    /**
     * @Type("string")
     * @SerializedName("loadPort")
     */
    public $loadPort;

    /**
     * @Type("string")
     * @SerializedName("dischargePort")
     */
    public $dischargePort;

    /**
     * @Type("integer")
     * @SerializedName("transitdays")
     */
    public $transitdays;

    /**
     * @Type("array<Api\Modules\AirFreight\Discount>")
     * @SerializedName("discount")
     */
    public $discount;    //This discount is applicable at Rate Card Level

    /**
     * @Type("array<Api\Modules\AirFreight\Carriers>")
     * @SerializedName("carriers")
     */
    public $carriers;    //This discount is applicable at Rate Card Level
}

class Carriers
{
    /**
     * @Type("string")
     * @SerializedName("carrier")
     */
    public $carrierName = 'FedX';
    /**
     * @Type("string")
     * @SerializedName("etd")
     */
    public $etd;
    /**
     * @Type("string")
     * @SerializedName("cyCutOfDate")
     */
    public $cyCutOffDate;


    /**
     * @Type("integer")
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
     * @Type("Api\Modules\AirFreight\RoutingVia")
     * @SerializedName("routingVia")
     */
    public $routingVia;

}

class routingVia
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
    public $port2 = "USD";
    /**
     * @Type("string")
     * @SerializedName("port3")
     */
    public $port3;


}

class Container
{

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;
    /**
     * @Type("array<Api\Modules\AirFreight\FreightCharges>")
     * @SerializedName("FreightCharges")
     */
    public $FreightCharges = [];
    /**
     * @Type("array<Api\Modules\AirFreight\LocalCharges>")
     * @SerializedName("LocalCharges")
     */
    public $LocalCharges = [];
    /**
     * @Type("array<Api\Modules\AirFreight\Discount>")
     * @SerializedName("discount")
     */
    public $discount = [];

}

class FreightCharges
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
    public $currency = "USD";
    /**
     * @Type("string")
     * @SerializedName("amount")
     */
    public $amount;
    /**
     * @Type("string")
     * @SerializedName("unit")
     */
    public $unit = "per Container";
}

class localCharges
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


