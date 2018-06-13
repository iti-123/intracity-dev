<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 4/11/2017
 * Time: 4:02 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\SellerPostSearchBO;


class LCLSellerPostSearchBO extends SellerPostSearchBO
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
     * @SerializedName("containerType")
     */
    public $containerType = [];

    /**
     * @Type("array<string>")
     * @SerializedName("packagingType")
     */
    public $packagingType = [];

    /**
     * @Type("array<Api\BusinessObjects\Common\PackageDimensions>")
     * @SerializedName("packageDimensions")
     */
    public $packageDimensions = [];
}
