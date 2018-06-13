<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 09-02-2017
 * Time: 23:18
 */

namespace Api\BusinessObjects;


class SellerSearchResult
{


    public $postId;
    public $title;
    public $buyerId;
    public $serviceId;
    public $leadType;
    public $serviceSubType;
    public $originLocation;
    public $destinationLocation;
    public $lastDateTimeOfQuoteSubmission;
    public $viewCount;
    public $isPublic;
    public $loadPort;
    public $dischargePort;
    public $commodity;
    public $commodityDescription;
    public $packagingType;
    public $cargoReadyDate;
//public $isFumigationRequired;
//public $isFactoryStuffingRequired;
    public $containerType;
    public $quantity;
    public $weightUnit;
    public $grossWeight;
    public $priceType;
    public $actualPrice;
    public $counterOffer;
    public $currency;
    public $transitDays;
    public $visibleToSellersIds;
    public $visibleToSellersNames;

}