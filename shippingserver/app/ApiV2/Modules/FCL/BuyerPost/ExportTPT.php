<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/7/2017
 * Time: 4:45 PM
 */
namespace ApiV2\Modules\FCL\BuyerPost;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;



/**
 * Class ExportTPT
 * @package Api\Modules\FCL\BuyerPost
 * @ExclusionPolicy("none")
 */
class ExportTPT
{

    /**
     * @Type("string")
     * @SerializedName("trailerType")
     */
    public $trailerType;
}