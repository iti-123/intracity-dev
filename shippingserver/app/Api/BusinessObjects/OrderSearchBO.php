<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/21/17
 * Time: 12:44 PM
 */

namespace Api\BusinessObjects;


class OrderSearchBO extends AbstractSearchBO
{

    public $serviceId;

    public $orderId;

    public $leadTypeId;

    public $sellerId;

    public $buyerId;

    public $orderStates = [];

    public $postTitle;

    public $orderNumber;

    public $consignee;

    public $additionalFilters = [];

}