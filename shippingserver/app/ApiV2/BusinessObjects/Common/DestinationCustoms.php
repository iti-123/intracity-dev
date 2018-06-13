<?php

namespace ApiV2\BusinessObjects\Common;

/**
 * /**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-04-2017
 * Time: 17:42
 */


/**
 * Class DestinationCustoms
 * @package Api\BusinessObjects\Common
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
     * @Type("array<Api\BusinessObjects\Common\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}