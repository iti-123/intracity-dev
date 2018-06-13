<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 30-Jan-17
 * Time: 11:06 AM
 */

namespace ApiV2\BusinessObjects;


/**
 * Class BuyerPostBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class BuyerPostBO
{
    /**
     * @Type("string")
     * @SerializedName("postId")
     */
    public $postId;

    /**
     * @Type("string")
     * @SerializedName("title")
     */
    public $title;

    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;

    /**
     * @Type("string")
     * @SerializedName("serviceId")
     */
    public $serviceId;

    /**
     * @Type("string")
     * @SerializedName("leadType")
     */
    public $leadType;

    /**
     * @Type("string")
     * @SerializedName("transactionId")
     */
    public $transactionId;

    /**
     * @Type("string")
     * @SerializedName("lastDateTimeOfQuoteSubmission")
     */
    public $lastDateTimeOfQuoteSubmission;

    /**
     * @Type("array<integer>")
     * @SerializedName("visibleToSellers")
     */
    public $visibleToSellers = [];

    /**
     * @Type("integer")
     * @SerializedName("viewCount")
     */
    public $viewCount;

    /**
     * @Type("boolean")
     * @SerializedName("isPublic")
     */
    public $isPublic;

    /**
     * @Exclude
     * @Type("string")
     * @SerializedName("createdBy")
     */
    public $createdBy;

    /**
     * @Type("string")
     * @SerializedName("status")
     */
    public $status;

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
     * @SerializedName("isTermAccepted")
     */
    public $isTermAccepted = false;

    /**
     * @Type("integer")
     * @SerializedName("version")
     */
    public $version;

}

