<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 6:03 PM
 */
namespace Api\Modules\FCL\BuyerPost;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;

class Document
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