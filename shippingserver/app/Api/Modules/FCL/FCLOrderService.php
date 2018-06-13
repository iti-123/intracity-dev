<?php

namespace Api\Modules\FCL;

use Api\Services\OrderService;
use Illuminate\Database\Eloquent\Collection;

class FCLOrderService extends OrderService
{

    protected $serviceName = "FCL";

    public function __construct()
    {
        $this->cartItemService = new FCLCartItemService();
    }

    public function bosToSellerPostsOrderBo($boArray)
    {

        $orderCollection = new Collection($boArray);
        $sellerPostOrdersBoObj = new FCLSellerPostOrdersBo();

        $uniqValidFrom = $orderCollection->unique('validFrom');
        if (count($uniqValidFrom)) {
            $sellerPostOrdersBoObj->validFrom = $uniqValidFrom[0]->validFrom;
        }
        $uniqValidTo = $orderCollection->unique('validTo');
        if (count($uniqValidFrom)) {
            $sellerPostOrdersBoObj->validTo = $uniqValidTo[0]->validTo;
        }
        $uniqTitles = $orderCollection->unique('title');
        if (count($uniqTitles)) {
            $sellerPostOrdersBoObj->title = $uniqTitles[0]->title;
        }
        $uniqPortPairs = $orderCollection->unique('loadPort', 'dischargePort');
        if (count($uniqPortPairs) == 1) {
            $sellerPostOrdersBoObj->loadPort = $uniqPortPairs[0]->loadPort;
            $sellerPostOrdersBoObj->dischargePort = $uniqPortPairs[0]->dischargePort;
        }

        $allContainers = $orderCollection->map(function ($order) {
            $containers = collect($order->attributes->containers);
            foreach ($containers as $eachContainer) {
                return $eachContainer;
            }
        });
        $sellerPostOrdersBoObj->totalContainers = $allContainers->sum('quantity');
        $uniqContainers = $allContainers->unique('containerType');
        foreach ($uniqContainers as $eachUniqContainer) {
            $sellerPostOrdersBoObj->containerType[] = $eachUniqContainer->containerType;
        }
        $sellerPostOrdersBoObj->orderDetails = $boArray;

        return $sellerPostOrdersBoObj;
    }

}