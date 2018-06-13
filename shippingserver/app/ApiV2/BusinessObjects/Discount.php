<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 05-02-2017
 * Time: 12:16
 */

namespace ApiV2\BusinessObjects;


class Discount
{


    public $discount;
    public $buyerId;
    public $discountType;
    public $creditDays;


    /**
     * Discount constructor.
     * @param $discount
     * @param $buyerId
     * @param $discountType
     * @param $creditDays
     */
    public function __construct($discount, $buyerId, $discountType, $creditDays)
    {
        $this->discount = $discount;
        $this->buyerId = $buyerId;
        $this->discountType = $discountType;
        $this->creditDays = $creditDays;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     * @return Discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBuyerId()
    {
        return $this->buyerId;
    }

    /**
     * @param mixed $buyerId
     * @return Discount
     */
    public function setBuyerId($buyerId)
    {
        $this->buyerId = $buyerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * @param mixed $discountType
     * @return Discount
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreditDays()
    {
        return $this->creditDays;
    }

    /**
     * @param mixed $creditDays
     * @return Discount
     */
    public function setCreditDays($creditDays)
    {
        $this->creditDays = $creditDays;
        return $this;
    }


}