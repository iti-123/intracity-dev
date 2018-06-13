<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:42 PM
 */
namespace Api\Modules\FCL\BuyerPost;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Class HazardousAttributes
 * @package Api\Modules\FCL\BuyerPost
 * @ExclusionPolicy("none")
 */
class HazardousAttributes
{
    /**
     * @Type("string")
     * @SerializedName("imoClass")
     */
    public $imoClass;

    /**
     * @Type("string")
     * @SerializedName("imoSubclass")
     */
    public $imoSubclass;

    /**
     * @Type("double")
     * @SerializedName("flashpoint")
     */
    public $flashpoint;

    /**
     * @Type("string")
     * @SerializedName("flashpointUnit")
     */
    public $flashpointUnit;

    /**
     * @Type("string")
     * @SerializedName("properShippingName")
     */
    public $properShippingName;


    /**
     * @Type("string")
     * @SerializedName("technicalName")
     */
    public $technicalName;

    /**
     * @Type("string")
     * @SerializedName("packingGroup")
     */
    public $packingGroup;

    /**
     * @Type("array<Api\Modules\FCL\BuyerPost\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}

