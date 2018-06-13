<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 13-02-2017
 * Time: 16:24
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerPostBO;


/**
 * Class FCLSellerPostBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLSellerPostBO extends SellerPostBO
{

    /**
     * @Type("Api\Modules\FCL\FCLSellerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

}

class FCLSellerPostAttributes
{
    /**
     * @Type("array<string>")
     * @SerializedName("selectedPayment")
     */
    public $selectedPayment = [];

    /**
     * @Type("array<Api\Modules\FCL\FCLSellerPortPair>")
     * @SerializedName("portPair")
     */
    public $portPair = [];
    /**
     * @Type("array<Api\Modules\FCL\Discount>")
     * @SerializedName("discount")
     */
    public $discount = [];    //This discount is applicable at Rate Card Level


}

class FCLSellerPortPair
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
     * @Type("array<Api\Modules\FCL\Discount>")
     * @SerializedName("discount")
     */
    public $discount = [];    //This discount is applicable at Rate Card Level

    /**
     * @Type("array<Api\Modules\FCL\SellerCarriers>")
     * @SerializedName("carriers")
     */
    public $carriers = [];    //This discount is applicable at Rate Card Level

}

class  Discount
{
    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;
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


class SellerCarriers
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
     * @Type("Api\Modules\FCL\RoutingVia")
     * @SerializedName("routingVia")
     */
    public $routingVia;
    /**
     * @Type("array<Api\Modules\FCL\SellerContainer>")
     * @SerializedName("containers")
     */
    public $containers = [];
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
    public $port2;
    /**
     * @Type("string")
     * @SerializedName("port3")
     */
    public $port3;


}

class SellerContainer
{

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("array<Api\BusinessObjects\Common\FreightCharges>")
     * @SerializedName("freightCharges")
     */
    public $freightCharges = [];

    /**
     * @Type("array<Api\BusinessObjects\Common\LocalCharges>")
     * @SerializedName("localCharges")
     */
    public $localCharges = [];

}




