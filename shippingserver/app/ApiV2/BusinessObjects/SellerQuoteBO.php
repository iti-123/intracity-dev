<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 9:09 AM
 */

namespace ApiV2\BusinessObjects;


class SellerQuoteBO
{
    /**
     * @Type("string")
     * @SerializedName("quoteId")
     */
    public $quoteId;

    /**
     * @Type("string")
     * @SerializedName("buyerPostId")
     */
    public $buyerPostId;


    /**
     * @Type("string")
     * @SerializedName("serviceId")
     */
    public $serviceId;


    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;

    /**
     * @Type("string")
     * @SerializedName("sellerId")
     */
    public $sellerId;

    /**
     * @Type("string")
     * @SerializedName("status")
     */
    public $status;

    /**
     * @Type("string")
     * @SerializedName("validTill")
     */
    public $validTill;

    /**
     * @Exclude
     * @Type("string")
     * @SerializedName("createdBy")
     */
    public $createdBy;

    /**
     * @Exclude
     * @Type("string")
     * @SerializedName("updatedBy")
     */
    public $updatedBy;


    /**
     * @Type("integer")
     * @SerializedName("createdAt")
     */
    public $createdAt;

    /**
     * @Type("integer")
     * @SerializedName("updatedAt")
     */
    public $updatedAt;

    /**
     * @Type("boolean")
     * @SerializedName("isSellerAccepted")
     */
    public $isSellerAccepted;

    /**
     * @Type("boolean")
     * @SerializedName("isBuyerAccepted")
     */
    public $isBuyerAccepted;

    /**
     * @Type("string")
     * @SerializedName("awardType")
     */
    public $awardType;

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
     * @SerializedName("totalFreightCharges")
     */
    public $totalFreightCharges;


}