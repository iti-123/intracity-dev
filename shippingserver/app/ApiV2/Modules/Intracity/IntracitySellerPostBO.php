<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 13-02-2017
 * Time: 16:24
 */

namespace ApiV2\Modules\Intracity;

use ApiV2\BusinessObjects\SellerPostBO;


/**
 * Class FCLSellerPostBO
 * @package Api\Modules\FCL
 * @ExclusionPolicy("none")
 */
class IntracitySellerPostBO extends SellerPostBO
{

    /**
     * @Type("Api\Modules\FCL\FCLSellerPostAttributes")
     * @SerializedName("attributes")
     */
    public $attributes;

    public $routes = array();

}


class  Discount
{
    /**
     * @Type("string")
     * @SerializedName("buyerId")
     */
    public $buyerId;
    /**
     * @Type("string")
     * @SerializedName("discountType")
     */
    public $discountType;
    /**
     * @Type("string")
     * @SerializedName("discount")
     */
    public $discount;
    /**
     * @Type("string")
     * @SerializedName("creditDays")
     */
    public $creditDays;
}

class IntracitySellerPostAttributes
{
    /**
     * @Type("array<string>")
     * @SerializedName("selectedPayment")
     */
    public $selectedPayment = [];

    /**
     * @Type("array<Api\Modules\FCL\FCLSellerPortPair>")
     * @SerializedName("portPair")
     */
    public $portPair = [];
    /**
     * @Type("array<Api\Modules\FCL\Discount>")
     * @SerializedName("discount")
     */
    public $discount = [];    //This discount is applicable at Rate Card Level

    /**
     * @Type("array<Api\Modules\FCL\Discount>")
     * @SerializedName("discount")
     */
    public $routes = [];    


}




