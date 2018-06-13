<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/11/17
 * Time: 11:56 AM
 */

namespace App\ApiV2\Listeners;


use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\SellerPostBO;
use ApiV2\Modules\AbstractServiceFactory;
use Log;

class PostNotificationListener
{


    public function handle($event)
    {
        LOG::info("event handled Class :: ".$event);
        
        if (get_class($event) == 'App\ApiV2\Events\BuyerPostCreatedEvent') {
            $this->_handleBuyer($event->bo);
        } else if (get_class($event) == 'App\ApiV2\Events\SellerPostCreatedEvent') {
            $this->_handleSeller($event->bo);
        }

    }


    private function _handleBuyer($bo)
    {

        LOG::info("event handled Data :: ".$bo);

        $serviceId = $bo->lkp_service_id;

        $factory = AbstractServiceFactory::getBuyerPostFactory($serviceId);

        LOG::info("event handled factory :: ".get_class($factory));

        $recommender = $factory->makeRecommender();

        $recommender->handleBuyerPostAdded($bo);

    }

    private function _handleSeller($bo)
    {

        LOG::info("SellerPostCreated event handled");

        $serviceId = $bo->lkp_service_id;

        $factory = AbstractServiceFactory::getSellerPostFactory($serviceId);

        $recommender = $factory->makeRecommender();

        $recommender->handleSellerPostAdded($bo);

    }

}