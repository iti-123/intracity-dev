<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/27/2017
 * Time: 7:45 AM
 */

namespace Api\BusinessObjects;

/**
 * Class PrivateMessagesBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 *
 */
class MessageBO
{

    /**
     * @Type("integer")
     * @SerializedName("id")
     */
    public $messageId;

    /**
     * @Type("string")
     * @SerializedName("serviceId")
     */
    public $serviceId;

    /**
     * @Type("string")
     * @SerializedName("senderId")
     */
    public $senderId;

    /**
     * @Type("string")
     * @SerializedName("recepientID")
     */
    public $recepientID;

    /**
     * @Type("string")
     * @SerializedName("postId")
     */
    public $postId = "";

    /**
     * @Type("string")
     * @SerializedName("postItemId")
     */
    public $postItemId = "";

    /**
     * @Type("string")
     * @SerializedName("orderId")
     */
    public $orderId = "";

    /**
     * @Type("string")
     * @SerializedName("contractId")
     */
    public $contractId = "";

    /**
     * @Type("string")
     * @SerializedName("quoteId")
     *
     */
    public $quoteId = "";

    /**
     * @Type("string")
     * @SerializedName("quoteItemId")
     */
    public $quoteItemId = "";

    /**
     * @Type("string")
     * @SerializedName("enquiryId")
     */
    public $enquiryId = "";

    /**
     * @Type("string")
     * @SerializedName("messageType")
     */
    public $messageType = "";

    /**
     * @Type("string")
     * @SerializedName("leadId")
     */
    public $leadId = "";


    /**
     * @Type("string")
     * @SerializedName("messageNo")
     */

    public $messageNo = "";

    /**
     * @Type("string")
     * @SerializedName("subject")
     */

    public $subject = "";

    /**
     * @Type("string")
     * @SerializedName("message")
     */

    public $message = "";

    /**
     * @Type("string")
     * @SerializedName("isRead")
     */
    public $isRead = 0;

    /**
     * @Type("integer")
     * @SerializedName("isDraft")
     */

    public $isDraft = 0;

    /**
     * @Type("integer")
     * @SerializedName("isReminder")
     */

    public $isReminder = 0;

    /**
     * @Type("integer")
     * @SerializedName("isNotified")
     */

    public $isNotified = 0;

    /**
     * @Type("integer")
     * @SerializedName("isGeneral")
     */

    public $isGeneral = 0;


    /**
     * @Type("integer")
     * @SerializedName("isTerm")
     */

    public $isTerm = 0;


    /**
     * @Type("integer")
     * @SerializedName("parentMessageId")
     */

    public $parentMessageId = 0;

    /**
     * @Type("integer")
     * @SerializedName("actualParentMessageId")
     */

    public $actualParentMessageId = 0;


    /**
     * @Type("string")
     * @SerializedName("createdBy")
     */
    public $createdBy;


    /**
     * @Type("string")
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


}