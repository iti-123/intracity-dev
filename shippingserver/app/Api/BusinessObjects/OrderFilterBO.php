<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 14:11
 */

namespace Api\BusinessObjects;


use Api\Framework\Workflow\Transitionable;

class OrderFilterBO implements Transitionable
{


    /**
     * @Type("string")
     * @SerializedName("name")
     */
    public $name;

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
     * @SerializedName("commodityType")
     */
    public $commodityType;

    /**
     * @Type("string")
     * @SerializedName("containerType")
     */
    public $containerType;

    /**
     * @Type("string")
     * @SerializedName("orderNo")
     */
    public $orderNo;

    /**
     * @Type("string")
     * @SerializedName("consignee")
     */
    public $consignee;

}

