<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:47 PM
 */
namespace ApiV2\Modules\FCL\BuyerPost;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;



/**
 * Class DestinationCustoms
 * @package Api\Modules\FCL\BuyerPost
 * @ExclusionPolicy("none")
 */
class DestinationCustoms
{

    /**
     * @Type("string")
     * @SerializedName("otherInstructions")
     */
    public $importBillType;

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
     * @Type("array<Api\Modules\FCL\BuyerPost\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}