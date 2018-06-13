<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/31/2017
 * Time: 7:22 PM
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\ContractBO;

class FCLContractBO extends ContractBO
{
    /**
     * @Type("string")
     * @SerializedName("documentId")
     */
    public $documentId;

    /**
     * @Type("string")
     * @SerializedName("documentName")
     */
    public $documentName;

    /**
     * @Type("array<Api\Modules\FCL\FCLContractPortPairs>")
     * @SerializedName("portPairs")
     */
    public $portPairs;
}

class FCLContractPortPairs
{

    /**
     * @Type("string")
     * @SerializedName("commodity")
     */
    public $commodity;

    /**
     * @Type("integer")
     * @SerializedName("quoteId")
     */
    public $quoteId;

    /**
     * @Type("string")
     * @SerializedName("loadPort")
     */
    public $loadPort;

    /**
     * @Type("string")
     * @SerializedName("dischargePort")
     */
    public $dischargePort;

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("string")
     * @SerializedName("quantity")
     */
    public $quantity;

    /**
     * @Type("Api\Modules\FCL\FCLContractTermCharges")
     * @SerializedName("charges")
     */
    public $charges;


}

class FCLContractTermCharges
{
    /**
     * @Type("Api\Modules\FCL\FCLContractTermFreightCharges")
     * @SerializedName("freightCharges")
     */
    public $freightCharges;

    /**
     * @Type("Api\Modules\FCL\FCLContractTermLocalCharges")
     * @SerializedName("localCharges")
     */
    public $localCharges;
}


class FCLContractTermFreightCharges
{
    /**
     * @Type("string")
     * @SerializedName("chargeType")
     */
    public $chargeType;

    /**
     * @Type("string")
     * @SerializedName("currency")
     */
    public $currency;

    /**
     * @Type("string")
     * @SerializedName("amount")
     */
    public $amount;

    /**
     * @Type("string")
     * @SerializedName("unit")
     */
    public $unit;

}

class FCLContractTermLocalCharges
{
    /**
     * @Type("string")
     * @SerializedName("chargeType")
     */
    public $chargeType;

    /**
     * @Type("string")
     * @SerializedName("currency")
     */
    public $currency;

    /**
     * @Type("string")
     * @SerializedName("amount")
     */
    public $amount;
}