<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 11:03 PM
 */

namespace Api\Modules\LCL;

use Api\BusinessObjects\BuyerPostSearchBO;

/**
 * Class LCLBuyerPostSearchBO
 * @package Api\Modules\LCL
 * @ExclusionPolicy("none")
 */
class LCLBuyerPostSearchBO extends BuyerPostSearchBO
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
     * @SerializedName("packagingType")
     */
    public $packagingType = [];

    /**
     * @Type("array<Api\Modules\LCL\LCLBuyerPostSearchPackagesFilter>")
     * @SerializedName("containers")
     */
    public $packageDimensions = [];

}

class LCLBuyerPostSearchPackagesFilter
{

    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity = [];

    /**
     * @Type("int")
     * @SerializedName("packagingType")
     */
    public $packagingType = [];

    /**
     * @Type("double")
     * @SerializedName("length")
     */
    public $length;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

}