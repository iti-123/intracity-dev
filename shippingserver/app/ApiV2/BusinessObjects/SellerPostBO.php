<?php
/**
 * Created by PhpStorm.
 * User: pc32261
 * Date: 28-Jan-17
 * Time: 6:06 PM
 */

namespace ApiV2\BusinessObjects;

/**
 * Class SellerPostBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class SellerPostBO
{

    /**
     * @Type("string")
     * @SerializedName("postId")
     */
    public $postId;
    /**
     * @Type("integer")
     * @SerializedName("serviceId")
     */
    public $serviceId;

    /**
     * @Type("string")
     * @SerializedName("serviceSubType")
     */
    public $serviceSubType;

    /**
     * @Type("integer")
     * @SerializedName("sellerId")
     */
    public $sellerId;

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
     * @SerializedName("status")
     */
    public $status;

    /**
     * @Type("string")
     * @SerializedName("termsConditions")
     */
    public $termsConditions;

    /**
     * @Type("boolean")
     * @SerializedName("isPublic")
     */
    public $isPublic;

    /**
     * @Type("array<integer>")
     * @SerializedName("discountedBuyers")
     */
    public $discountedBuyers = [];
    /**
     * @Type("boolean")
     * @SerializedName("isTermAccepted")
     */
    public $isTermAccepted;

    /**
     * @Type("integer")
     * @SerializedName("viewCount")
     */
    public $viewCount;

    /**
     * @Exclude
     * @Type("integer")
     * @SerializedName("createdBy")
     */
    public $createdBy;

    /**
     * @Exclude
     * @Type("integer")
     * @SerializedName("updatedBy")
     */
    public $updatedBy;

    /**
     * @Type("string")
     * @SerializedName("createdIp")
     */
    public $createdIp;

    /**
     * @Type("string")
     * @SerializedName("updatedIp")
     */
    public $updatedIp;


    /**
     * @Type("string")
     * @SerializedName("transactionId")
     */
    public $transactionId;
}

