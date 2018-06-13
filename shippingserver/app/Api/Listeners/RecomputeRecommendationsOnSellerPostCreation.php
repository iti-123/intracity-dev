<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 12:57 AM
 */

namespace App\Api\Listeners;


use Api\BusinessObjects\SellerPostBO;
use Api\Modules\AbstractServiceFactory;
use App\Api\Events\SellerPostCreated;

class RecomputeRecommendationsOnSellerPostCreation
{

    public function handle(SellerPostCreated $event)
    {
        $this->_handle($event->bo);
    }


    private function _handle(SellerPostBO $bo)
    {

        $serviceId = $bo->serviceId;

        $factory = AbstractServiceFactory::getSellerPostFactory($serviceId);

        $recommender = $factory->makeRecommender();

        $recommender->handleSellerPostAdded($bo);

    }
}