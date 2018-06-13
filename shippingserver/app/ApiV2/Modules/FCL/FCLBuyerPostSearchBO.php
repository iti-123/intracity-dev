<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 10-02-2017
 * Time: 16:59
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\BuyerPostSearchBO;

/**
 * Class FCLBuyerPostSearchBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLBuyerPostSearchBO extends BuyerPostSearchBO
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
     * @Type("array<Api\Modules\FCL\FCLBuyerPostSearchContainerFilter>")
     * @SerializedName("containers")
     */
    public $containers = [];

}

class FCLBuyerPostSearchContainerFilter
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