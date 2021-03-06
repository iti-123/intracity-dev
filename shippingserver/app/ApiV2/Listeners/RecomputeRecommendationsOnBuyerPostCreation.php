<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 12:57 AM
 */

namespace App\Api\Listeners;


use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\Modules\AbstractServiceFactory;
use App\ApiV2\Events\BuyerPostCreated;
use Log;

class RecomputeRecommendationsOnBuyerPostCreation
{

    public function handle(BuyerPostCreated $event)
    {
        $this->_handle($event->bo);
    }


    private function _handle(BuyerPostBO $bo)
    {

        LOG::info("BuyerPostCreated event handled");

        $serviceId = $bo->serviceId;

        $factory = AbstractServiceFactory::getBuyerPostFactory($serviceId);

        $recommender = $factory->makeRecommender();

        $recommender->handleBuyerPostAdded($bo);

    }

}