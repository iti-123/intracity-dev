<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 02-04-2017
 * Time: 20:40
 */

namespace ApiV2\BusinessObjects\Common;

/**
 * /**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-04-2017
 * Time: 17:41
 */


/**
 * Class PackageDimensions
 * @package Api\BusinessObjects\Common
 * @ExclusionPolicy("none")
 */
class PackageDimensions
{

    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("string")
     * @SerializedName("packagingType")
     */
    public $packagingType;

    /**
     * @Type("string")
     * @SerializedName("length")
     */
    public $length;

    /**
     * @Type("string")
     * @SerializedName("breadth")
     */
    public $breadth;

    /**
     * @Type("string")
     * @SerializedName("height")
     */
    public $height;


    /**
     * @Type("string")
     * @SerializedName("lbhUnit")
     */
    public $lbhUnit;
    /**
     * @Type("string")
     * @SerializedName("noOfPackages")
     */
    public $noOfPackages;

    /**
     * @Type("string")
     * @SerializedName("totalCBM")
     */
    public $totalCBM;  //Is this required?? Karunya

    /**
     * @Type("string")
     * @SerializedName("grossWeight")
     */
    public $grossWeight;

    /**
     * @Type("string")
     * @SerializedName("weightUnit")
     */
    public $weightUnit;

}