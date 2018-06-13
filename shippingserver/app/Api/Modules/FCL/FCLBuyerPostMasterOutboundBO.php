<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/5/2017
 * Time: 9:50 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\BuyerPostSearchBO;

/**
 * Class FCLBuyerPostMasterOutboundBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLBuyerPostMasterOutboundBO extends BuyerPostSearchBO
{

    /**
     * @Type("string")
     * @SerializedName("isPublic")
     */
    public $isPublic;

    /**
     * @Type("array<string>")
     * @SerializedName("visibleToSeller")
     */
    public $visibleToSeller = [];

    /**
     * @Type("array<string>")
     * @SerializedName("loadPort")
     */
    public $loadPort;
    /**
     * @Type("array<string>")
     * @SerializedName("dischargePort")
     */
    public $dischargePort;

    /**
     * @Type("array<string>")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("string")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate;

    /**
     * @Type("array<string>")
     * @SerializedName("containerType")
     */
    public $containerType = [];

    /**
     * @Type("array<string>")
     * @SerializedName("priceType")
     */
    public $priceType = [];

    /**
     * @Type("string")
     * @SerializedName("validFrom")
     */
    public $validFrom;

    /**
     * @Type("string")
     * @SerializedName("validTo")
     */
    public $validTo;

    /**
     * @Type("array<string>")
     * @SerializedName("status")
     */
    public $status;

    /**
     * @Type("array<string>")
     * @SerializedName("postIds")
     */
    public $postIds;

}