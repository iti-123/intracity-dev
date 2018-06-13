<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-04-2017
 * Time: 21:49
 */

namespace Api\BusinessObjects\Common;


/**
 * Class AbstractServiceType
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class AbstractServiceType
{
    /**
     * @Type("string")
     * @SerializedName("serviceSubType")
     */
    public $serviceSubType;

    /**
     * @Type("string")
     * @SerializedName("originLocation")
     */
    public $originLocation;

    /**
     * @Type("string")
     * @SerializedName("destinationLocation")
     */
    public $destinationLocation;


}