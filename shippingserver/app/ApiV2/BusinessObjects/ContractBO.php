<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/31/2017
 * Time: 7:08 PM
 */

namespace ApiV2\BusinessObjects;

class ContractBO
{
    /**
     * @Type("string")
     * @SerializedName("id")
     */
    public $id;

    /**
     * @Type("string")
     * @SerializedName("title")
     */
    public $title;

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
     * @SerializedName("validFrom")
     */
    public $validFrom;

    /**
     * @Type("string")
     * @SerializedName("validTo")
     */
    public $validTo;

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
     * @Type("string")
     * @SerializedName("awardType")
     */
    public $awardType;

    /**
     * @Type("string")
     * @SerializedName("status")
     */
    public $status;


}