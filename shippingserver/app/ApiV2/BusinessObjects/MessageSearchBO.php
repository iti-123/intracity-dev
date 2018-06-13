<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 10-02-2017
 * Time: 17:02
 */

namespace ApiV2\BusinessObjects;

/**
 * Class MessageSearchBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class MessageSearchBO
{

    /**
     * @Type("boolean")
     * @SerializedName("serviceId")
     */
    public $serviceId = "";


    /**
     * @Type("string")
     * @SerializedName("message_type")
     */
    public $message_type = "";


    /***
     * @Type("string")
     * @SerializedName("message_keywords")
     */
    public $message_keywords = "";

    /***
     * @Type("string")
     * @SerializedName("from_message")
     */
    public $from_message = "";


    /***
     * @Type("string")
     * @SerializedName("message_to")
     */
    public $message_to = "";

}