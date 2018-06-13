<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 30-Jan-17
 * Time: 11:06 AM
 */

namespace Api\BusinessObjects;


/**
 * Class NotificationBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class NotificationBO
{
    /**
     * @Type("integer")
     * @SerializedName("postId")
     */
    public $postId = 0;

    /**
     * @Type("string")
     * @SerializedName("postTitle")
     */
    public $postTitle = '';

    /**
     * @Type("string")
     * @SerializedName("postNumber")
     */
    public $postNumber = '';

    /**
     * @Type("string")
     * @SerializedName("routeLevel")
     */
    public $routeLevel = '';

    /**
     * @Type("integer")
     * @SerializedName("postStatus")
     */

    public $postStatus = 0;
    /**
     * @Type("integer")
     * @SerializedName("service")
     */
    public $service = 0;

    /**
     * @Type("string")
     * @SerializedName("type")
     */
    public $type = '';

    /**
     * @Type("string")
     * @SerializedName("messageBody")
     */
    public $messageBody = '';

    /**
     * @Type("integer")
     * @SerializedName("postType")
     */
    public $postType = 1;


    /**
     * @Type("integer")
     * @SerializedName("messageType")
     */
    public $messageType = 1;

    /**
     * @Type("integer")
     * @SerializedName("role")
     */
    public $role = 2;

    /**
     * @Type("integer")
     * @SerializedName("createdBy")
     */
    public $createdBy;

    /**
     * @Type("string")
     * @SerializedName("event")
     */
    public $event = '';

    /**
     * @Type("integer")
     * @SerializedName("postEnquiries")
     */
    public $postEnquiries = 0;

    /**
     * @Type("integer")
     * @SerializedName("postLeads")
     */
    public $postLeads = 0;

    /**
     * @Type("integer")
     * @SerializedName("postOffers")
     */
    public $postOffers = 0;

    /**
     * @Type("integer")
     * @SerializedName("postMessages")
     */
    public $postMessages = 0;

    /**
     * @Type("integer")
     * @SerializedName("documents")
     */
    public $documents = 0;

    /**
     * @Type("integer")
     * @SerializedName("orderMessages")
     */
    public $orderMessages = 0;

    /**
     * @Type("integer")
     * @SerializedName("orderIndents")
     */
    public $orderIndents = 0;

    /**
     * @Type("integer")
     * @SerializedName("orderStatus")
     */
    public $orderStatus = "";

    /**
     * @Type("integer")
     * @SerializedName("orderBilling")
     */
    public $orderBilling = 0;

    /**
     * @Type("integer")
     * @SerializedName("orderDocs")
     */
    public $orderDocs = 0;

    /**
     * @Type("integer")
     * @SerializedName("orderDocs")
     */
    public $orderId = 0;

    /**
     * @Type("integer")
     * @SerializedName("viewCount")
     */
    public $viewCount = 0;

    /**
     * @Type("boolean")
     * @SerializedName("isActive")
     */
    public $isActive = false;

    /**
     * @Type("string")
     * @SerializedName("updatedAt")
     */

    public $updatedAt = '';

}

