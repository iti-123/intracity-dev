<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/5/2017
 * Time: 9:50 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerPostSearchBO;

/**
 * Class FCLSellerPostMasterOutboundBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLSellerPostMasterOutboundBO extends SellerPostSearchBO
{

    /**
     * @Type("string")
     * @SerializedName("isPublic")
     */
    public $isPublic;

    /**
     * @Type("array<string>")
     * @SerializedName("visibleToBuyer")
     */
    public $visibleToBuyer = [];

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
     * @SerializedName("containerType")
     */
    public $containerType = [];

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
     * @SerializedName("tracking")
     */
    public $tracking;

    /**
     * @Type("array<string>")
     * @SerializedName("offers")
     */
    public $offers;


}