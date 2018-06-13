<?php
/**
 * Created by PhpStorm.
 * User: 10341
 * Date: 2/24/2017
 * Time: 4:40 PM
 */

namespace Api\BusinessObjects;

/**
 * Class SellerPostSearchBO
 * @package Api\BusinessObjects
 * @ExclusionPolicy("none")
 */
class SellerPostSearchBO extends AbstractSearchBO
{
    /**
     * @Type("boolean")
     * @SerializedName("serviceId")
     */
    public $serviceId;

    /**
     * @Type("string")
     * @SerializedName("sellerName")
     */
    public $sellerName;

    /**
     * @Type("array<string>")
     * @SerializedName("seller")
     */
    public $seller;

    /**
     * @Type("string")
     * @SerializedName("serviceSubType")
     */
    public $serviceSubType;

}