<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 14:11
 */

namespace ApiV2\BusinessObjects;


use ApiV2\Framework\Workflow\Transitionable;

class OrderBaseBO implements Transitionable
{


    /**
     * @Type("string")
     * @SerializedName("orderStatus")
     */
    public $orderStatus;


    /**
     * @Type("string")
     * @SerializedName("serviceId")
     */
    public $serviceId;

    /**
     * @Type("string")
     * @SerializedName("serviceType")
     */
    public $serviceType;

    /**
     * @Type("string")
     * @SerializedName("sellerId")
     */
    public $sellerId;

    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;

    /**
     * @Type("string")
     * @SerializedName("sellerQuoteId")
     */
    public $sellerQuoteId;

    /**
     * @Type("string")
     * @SerializedName("buyerPostId")
     */
    public $buyerPostId;


    /**
     * @Type("string")
     * @SerializedName("sellerName")
     */
    public $sellerName;


    /**
     * @Type("string")
     * @SerializedName("buyerName")
     */
    public $buyerName;

    /**
     * @Type("string")
     * @SerializedName("title")
     */

    public $title;


    /**
     * @Type("string")
     * @SerializedName("validFrom")
     */

    public $validFrom;

    /**
     * @Type("string")
     * @SerializedName("validTo")
     */

    public $validTo;

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
     * @Type("string")
     * @SerializedName("postType")
     */
    public $postType;

    /**
     * @Type("Api\BusinessObjects\Details")
     * @SerializedName("details")
     */
    public $details;


}

class Details
{

    /**
     * @Type("string")
     * @SerializedName("consignorName")
     */
    public $consignorName;


    /**
     * @Type("string")
     * @SerializedName("consigneeName")
     */

    public $consigneeName;

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */

    public $containerType;

    /**
     * @Type("string")
     * @SerializedName("containers")
     */

    public $containers;

    /**
     * @Type("string")
     * @SerializedName("orderId")
     */
    public $orderId;

    /**
     * @Type("string")
     * @SerializedName("orderNo")
     */
    public $orderNo;


}

class PostDetails
{


    /**
     * @Type("string")
     * @SerializedName("postId")
     */
    public $postId;

    /**
     * @Type("string")
     * @SerializedName("postTitle")
     */
    public $postTitle;

    /**
     * @Type("string")
     * @SerializedName("validFrom")
     */
    public $validFrom;

    /**
     * @Type("string")
     * @SerializedName("validTo")
     */
    public $validTo;

}

class Filters
{

    /**
     * @Type("string")
     * @SerializedName("userType")
     */
    public $userType;

    /**
     * @Type("string")
     * @SerializedName("user")
     */
    public $user;

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
     * @Type("string")
     * @SerializedName("commodityType")
     */
    public $commodityType;

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("string")
     * @SerializedName("orderNo")
     */
    public $orderNo;

    /**
     * @Type("string")
     * @SerializedName("consignee")
     */
    public $consignee;

}

