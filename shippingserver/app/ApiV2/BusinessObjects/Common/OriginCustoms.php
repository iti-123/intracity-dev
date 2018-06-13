<?php

namespace ApiV2\BusinessObjects\Common;

/**
 * /**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-04-2017
 * Time: 17:41
 */


/**
 * Class OriginCustoms
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class OriginCustoms
{

    /**
     * @Type("string")
     * @SerializedName("shippingBillType")
     */
    public $shippingBillType;

    /**
     * @Type("string")
     * @SerializedName("otherBillType")
     */
    public $otherBillType;

    /**
     * @Type("integer")
     * @SerializedName("numberOfBills")
     */
    public $numberOfBills;

    /**
     * @Type("string")
     * @SerializedName("isReturnable")
     */
    public $isReturnable;

    /**
     * @Type("string")
     * @SerializedName("returnableCategory")
     */
    public $returnableCategory;


    /**
     * @Type("string")
     * @SerializedName("otherCategory")
     */
    public $otherCategory;

    /**
     * @Type("string")
     * @SerializedName("otherInstructions")
     */
    public $otherInstructions;

    /**
     * @Type("array<Api\BusinessObjects\Common\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}