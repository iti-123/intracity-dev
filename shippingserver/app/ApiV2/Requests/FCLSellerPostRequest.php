<?php
/**
 * Created by PhpStorm.
 * User: pc32261
 * Date: 28-Jan-17
 * Time: 6:06 PM
 */

namespace ApiV2\Requests;


class FCLSellerPostRequest
{
    public $serviceId;
    public $serviceSubcategory;
    public $sellerId;
    public $postTitle;
    public $validFrom;
    public $validTo;
    public $postStatusId;
    public $tracking;
    public $termsConditions;
    public $isPublic;
    public $isTermsAccepted;
    public $payment = [];
    //Discount Object
    public $discount = [];

    /**
     * @return mixed
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param mixed $serviceId
     * @return FCLSellerPostRequest
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceSubcategory()
    {
        return $this->serviceSubcategory;
    }

    /**
     * @param mixed $serviceSubcategory
     * @return FCLSellerPostRequest
     */
    public function setServiceSubcategory($serviceSubcategory)
    {
        $this->serviceSubcategory = $serviceSubcategory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @param mixed $sellerId
     * @return FCLSellerPostRequest
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostTitle()
    {
        return $this->postTitle;
    }

    /**
     * @param mixed $postTitle
     * @return FCLSellerPostRequest
     */
    public function setPostTitle($postTitle)
    {
        $this->postTitle = $postTitle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @param mixed $validFrom
     * @return FCLSellerPostRequest
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * @param mixed $validTo
     * @return FCLSellerPostRequest
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostStatusId()
    {
        return $this->postStatusId;
    }

    /**
     * @param mixed $postStatusId
     * @return FCLSellerPostRequest
     */
    public function setPostStatusId($postStatusId)
    {
        $this->postStatusId = $postStatusId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @param mixed $tracking
     * @return FCLSellerPostRequest
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTermsConditions()
    {
        return $this->termsConditions;
    }

    /**
     * @param mixed $termsConditions
     * @return FCLSellerPostRequest
     */
    public function setTermsConditions($termsConditions)
    {
        $this->termsConditions = $termsConditions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $isPublic
     * @return FCLSellerPostRequest
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsTermsAccepted()
    {
        return $this->isTermsAccepted;
    }

    /**
     * @param mixed $isTermsAccepted
     * @return FCLSellerPostRequest
     */
    public function setIsTermsAccepted($isTermsAccepted)
    {
        $this->isTermsAccepted = $isTermsAccepted;
        return $this;
    }

    /**
     * @return array
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param array $payment
     * @return FCLSellerPostRequest
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @return array
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param array $discount
     * @return FCLSellerPostRequest
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

}

class Discount
{


    public $discount;
    public $buyerId;
    public $discountType;
    public $creditDays;

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