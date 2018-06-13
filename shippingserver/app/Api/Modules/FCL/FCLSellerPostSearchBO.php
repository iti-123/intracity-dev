<?php
/**
 * Created by PhpStorm.
 * User: 10341
 * Date: 2/24/2017
 * Time: 4:47 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerPostSearchBO;


/**
 * Class FCLSellerPostSearchBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLSellerPostSearchBO extends SellerPostSearchBO
{
    /**
     * @Type("array<string>")
     * @SerializedName("loadPort")
     */
    public $loadPort = [];

    /**
     * @Type("array<string>")
     * @SerializedName("dischargePort")
     */
    public $dischargePort = [];

    /**
     * @Type("array<string>")
     * @SerializedName("commodity")
     */
    public $commodity = [];

    /**
     * @Type("array<string>")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate = [];

    /**
     * @Type("array<string>")
     * @SerializedName("containerType")
     */
    public $containerType = [];

    /**
     * @Type("array<Api\Modules\FCL\FCLSellerPostSearchContainerFilter>")
     * @SerializedName("containers")
     */
    public $containers = [];

}

class FCLSellerPostSearchContainerFilter
{

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("int")
     * @SerializedName("containerQuantity")
     */
    public $containerQuantity;

    /**
     * @Type("double")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

}