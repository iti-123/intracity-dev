<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:47 PM
 */
namespace Api\Modules\FCL\BuyerPost;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;


/**
 * Class OriginCustoms
 * @package Api\Modules\FCL\BuyerPost
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
     * @Type("array<Api\Modules\FCL\BuyerPost\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}