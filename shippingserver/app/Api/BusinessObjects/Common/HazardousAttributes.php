<?php

namespace Api\BusinessObjects\Common;

/**
 * /**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-04-2017
 * Time: 17:44
 */


/**
 * Class HazardousAttributes
 * @package Api\BusinessObjects\Common
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
     * @Type("array<Api\BusinessObjects\Common\Document>")
     * @SerializedName("documents")
     */
    public $documents = [];
}

