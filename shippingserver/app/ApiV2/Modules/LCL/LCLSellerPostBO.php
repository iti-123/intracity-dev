<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 13-02-2017
 * Time: 16:24
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\SellerPostBO;

/**
 * Class LCLSellerPostBO
 * @package Api\Modules\LCL
 * @ExclusionPolicy("none")
 */
class LCLSellerPostBO extends SellerPostBO
{

    /**
     * @Type("Api\Modules\LCL\LCLSellerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

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

class LCLSellerPostAttributes
{

    /**
     * @Type("array<Api\Modules\LCL\LCLSellerPortPair>")
     * @SerializedName("portPair")
     */
    public $portPair = [];
    /**
     * @Type("array<Api\Modules\LCL\Discount>")
     * @SerializedName("discount")
     */
    public $discount = [];    //This discount is applicable at Rate Card Level


}

class LCLSellerPortPair
{

    /**
     * @var serviceSubType
     * @Type("string")
     * @SerializedName("serviceSubType")
     */
    public $serviceSubType;

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
     * @Type("array<Api\Modules\LCL\Discount>")
     * @SerializedName("discount")
     */
    public $discount;    //This discount is applicable at Rate Card Level

    /**
     * @Type("array<Api\Modules\LCL\Carriers>")
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
     * @SerializedName("cfsCutOffDate")
     */
    public $cfsCutOffDate;


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
     * @Type("Api\Modules\LCL\RoutingVia")
     * @SerializedName("routingVia")
     */
    public $routingVia;

    /**
     * @SerializedName("freightCharges")
     * @Type("array<Api\BusinessObjects\Common\FreightCharges>")
     */
    public $freightCharges = [];

    /**
     * @SerializedName("localCharges")
     * @Type("array<Api\BusinessObjects\Common\LocalCharges>")
     */
    public $localCharges = [];

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
//class Packages {

/**
 * @Type("string")
 * @SerializedName("containerType")
 */
//public $containerType;
/**
 * @Type("array<Api\Modules\LCL\FreightCharges>")
 * @SerializedName("FreightCharges")
 */
//public $FreightCharges =[];
/**
 * @Type("array<Api\Modules\LCL\LocalCharges>")
 * @SerializedName("LocalCharges")
 */
// public $LocalCharges=[];
/**
 * @Type("array<Api\Modules\LCL\Discount>")
 * @SerializedName("discount")
 */
// public $discount=[];

//}





