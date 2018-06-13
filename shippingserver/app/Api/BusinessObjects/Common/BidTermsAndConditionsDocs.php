<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-04-2017
 * Time: 21:47
 */

namespace Api\BusinessObjects\Common;


/**
 * Class BidTermsAndConditions
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class BidTermsAndConditions
{
    /**
     * @Type("string")
     * @SerializedName("documentId")
     */
    public $documentId;

    /**
     * @Type("string")
     * @SerializedName("documentName")
     */
    public $documentName;
}
