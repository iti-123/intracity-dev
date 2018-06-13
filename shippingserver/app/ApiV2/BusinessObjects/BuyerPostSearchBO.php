<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 10-02-2017
 * Time: 17:02
 */

namespace ApiV2\BusinessObjects;

/**
 * Class BuyerPostSearchBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class BuyerPostSearchBO extends AbstractSearchBO
{

    /**
     * @Type("boolean")
     * @SerializedName("serviceId")
     */
    public $serviceId;


    /**
     * @Type("string")
     * @SerializedName("serviceSubType")
     */
    public $serviceSubType;

    /**
     * @Type("string")
     * @SerializedName("buyerName")
     */
    public $buyerName;

    /**
     * @Type("array<string>")
     * @SerializedName("buyer")
     */
    public $buyer = [];

    /**
     * @Type("string")
     * @SerializedName("leadType")
     */
    public $leadType;

    /**
     * @Type("integer")
     * @SerializedName("lastDateTimeForQuote")
     */
    public $lastDateTimeForQuote;
}