<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 30-Jan-17
 * Time: 11:06 AM
 */

namespace ApiV2\Requests;


class FCLBuyerPostRequest
{
    public $postId;
    public $title;
    public $buyerId;
    public $serviceId;
    public $leadType;
    public $serviceSubType;
    public $lastDateOfQuoteSubmission;
    public $lastTimeOfQuoteSubmission;
    public $visibleToSellers = [];
    public $viewCount;
    public $isPublic;
    public $isPrivate;
    public $sysSolrSync;
    public $createdBy;
    public $updatedBy;
    public $createdIP;
    public $updatedIP;
    public $createdAt;
    public $updatedAt;
    public $isTermAccepted;
    public $attributes;
    public $originLocation;
    public $destinationLocation;
    public $isHazardous;
    public $hazardousAttributes;


    /**
     * @return array
     */
    public function getVisibleToSellers()
    {
        return $this->visibleToSellers;
    }

    /**
     * @param array $visibleToSellers
     * @return FCLBuyerPostRequest
     */
    public function setVisibleToSellers($visibleToSellers)
    {
        $this->visibleToSellers = $visibleToSellers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     * @return FCLBuyerPostRequest
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return FCLBuyerPostRequest
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginLocation()
    {
        return $this->originLocation;
    }

    /**
     * @param mixed $originLocation
     * @return FCLBuyerPostRequest
     */
    public function setOriginLocation($originLocation)
    {
        $this->originLocation = $originLocation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestinationLocation()
    {
        return $this->destinationLocation;
    }

    /**
     * @param mixed $destinationLocation
     * @return FCLBuyerPostRequest
     */
    public function setDestinationLocation($destinationLocation)
    {
        $this->destinationLocation = $destinationLocation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsHazardous()
    {
        return $this->isHazardous;
    }

    /**
     * @param mixed $isHazardous
     * @return FCLBuyerPostRequest
     */
    public function setIsHazardous($isHazardous)
    {
        $this->isHazardous = $isHazardous;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHazardousAttributes()
    {
        return $this->hazardousAttributes;
    }

    /**
     * @param mixed $hazardousAttributes
     * @return FCLBuyerPostRequest
     */
    public function setHazardousAttributes($hazardousAttributes)
    {
        $this->hazardousAttributes = $hazardousAttributes;
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
     * @return FCLBuyerPostRequest
     */
    public function setBuyerId($buyerId)
    {
        $this->buyerId = $buyerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param mixed $serviceId
     * @return FCLBuyerPostRequest
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLeadType()
    {
        return $this->leadType;
    }

    /**
     * @param mixed $leadType
     * @return FCLBuyerPostRequest
     */
    public function setLeadType($leadType)
    {
        $this->leadType = $leadType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceSubType()
    {
        return $this->serviceSubType;
    }

    /**
     * @param mixed $serviceSubType
     * @return FCLBuyerPostRequest
     */
    public function setServiceSubType($serviceSubType)
    {
        $this->serviceSubType = $serviceSubType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastDateOfQuoteSubmission()
    {
        return $this->lastDateOfQuoteSubmission;
    }

    /**
     * @param mixed $lastDateOfQuoteSubmission
     * @return FCLBuyerPostRequest
     */
    public function setLastDateOfQuoteSubmission($lastDateOfQuoteSubmission)
    {
        $this->lastDateOfQuoteSubmission = $lastDateOfQuoteSubmission;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getLastTimeOfQuoteSubmission()
    {
        return $this->lastTimeOfQuoteSubmission;
    }

    /**
     * @param mixed $lastTimeOfQuoteSubmission
     * @return FCLBuyerPostRequest
     */
    public function setLastTimeOfQuoteSubmission($lastTimeOfQuoteSubmission)
    {
        $this->lastTimeOfQuoteSubmission = $lastTimeOfQuoteSubmission;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param mixed $viewCount
     * @return FCLBuyerPostRequest
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
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
     * @return FCLBuyerPostRequest
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param mixed $isPrivate
     * @return FCLBuyerPostRequest
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSysSolrSync()
    {
        return $this->sysSolrSync;
    }

    /**
     * @param mixed $sysSolrSync
     * @return FCLBuyerPostRequest
     */
    public function setSysSolrSync($sysSolrSync)
    {
        $this->sysSolrSync = $sysSolrSync;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     * @return FCLBuyerPostRequest
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     * @return FCLBuyerPostRequest
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedIP()
    {
        return $this->createdIP;
    }

    /**
     * @param mixed $createdIP
     * @return FCLBuyerPostRequest
     */
    public function setCreatedIP($createdIP)
    {
        $this->createdIP = $createdIP;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedIP()
    {
        return $this->updatedIP;
    }

    /**
     * @param mixed $updatedIP
     * @return FCLBuyerPostRequest
     */
    public function setUpdatedIP($updatedIP)
    {
        $this->updatedIP = $updatedIP;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return FCLBuyerPostRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return FCLBuyerPostRequest
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsTermAccepted()
    {
        return $this->isTermAccepted;
    }

    /**
     * @param mixed $isTermAccepted
     * @return FCLBuyerPostRequest
     */
    public function setIsTermAccepted($isTermAccepted)
    {
        $this->isTermAccepted = $isTermAccepted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     * @return FCLBuyerPostRequest
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }


}
