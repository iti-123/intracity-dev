<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 24/2/17
 * Time: 12:07 AM
 */

namespace ApiV2\Modules\RoRo;

use ApiV2\BusinessObjects\BuyerPostSearchBO;

/**
 * Class RoRoBuyerPostSearchBO
 * @package Api\Modules\RoRo
 * @ExclusionPolicy("none")
 */
class RoRoBuyerPostSearchBO extends BuyerPostSearchBO
{

    /**
     * @Type("string")
     * @SerializedName("loadPort")
     */
    public $loadPort;

    /**
     * @Type("string")
     * @SerializedName("$dischargePort")
     */
    public $dischargePort;

    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("int")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate;
}