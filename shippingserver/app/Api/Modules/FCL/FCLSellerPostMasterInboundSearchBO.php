<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/4/17
 * Time: 9:07 AM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\AbstractSearchBO;

/**
 * Class FCLSellerPostMasterInboundSearchBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class FCLSellerPostMasterInboundSearchBO extends AbstractSearchBO
{

    /**
     * @Type("string")
     * @SerializedName("cacheControl")
     * Possible values use | check | rebuild
     * use => Uses the cache if available
     * check => Validates if the cache has not expired and then uses it. If expired, rebuilds it.
     * rebuild => Rebuild the cache irrespective of it's expiration.
     */
    public $cacheControl = "use";

    /**
     * @Type("array<string>")
     * @SerializedName("buyer")
     */
    public $buyer = [];

    /**
     * @Type("array<integer>")
     * @SerializedName("priceType")
     */
    public $priceType = [];

    /**
     * @Type("array<integer>")
     * @SerializedName("allotments")
     */
    public $allotments = [];

    /**
     * @Type("integer")
     * @SerializedName("lastDateForQuoteTime")
     */
    public $lastDateForQuoteTime;

    /**
     * @Type("integer")
     * @SerializedName("cargoReadyDate")
     */
    public $cargoReadyDate;


}