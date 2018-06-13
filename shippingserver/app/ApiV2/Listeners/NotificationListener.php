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

class NotificationListener
{


    public function handle($event)
    {
        Log::info('message::'.get_class($event));
        if (get_class($event) == 'App\Api\Events\BuyerPostCreated') {
            $this->_handleBuyer($event->bo);
        }
        if (get_class($event) == 'App\Api\Events\SellerPostCreated') {
            $this->_handleSeller($event->bo);
        }
        if (get_class($event) == 'App\Api\Events\MessageCreated') {
            $this->_handleSeller($event->bo);
        } else if(get_class($event) == 'App\ApiV2\Events\BookNow') {
            $this->_handleBooking($event->bo);
        }
        /*   if($event instanceof BuyerPostCreated){
              LOG::info("handle Event BuyerPostCreated");
              $this->_handleSeller($event->bo);
          }

         if($event instanceof BuyerPostCreated){
              LOG::info("handle Event BuyerPostCreated");
              $this->_handleBuyer($event->bo);
          }
  */
        /*   if($event instanceof BuyerPostCreated) {
   
               //NotificationSe
   
   
           }else if($event instanceof SellerPostCreated){
   
   
           }else if($event instanceof SellerAcceptedOffer){
   
   
           }else if($event instanceof SellerSubmittedQuote){
   
   
           }else if($event instanceof BookNow){
   
   
   
           }else{
   
               LOG::alert("No notification listener found for event of type " . get_class($event));
   
           }
   */


    }


    private function _handleBuyer(BuyerPostBO $bo)
    {

        LOG::info("FCLTermBuyerPostBO event handled");

        $serviceId = $bo->serviceId;

        $factory = AbstractServiceFactory::getSellerPostFactory($serviceId);

        $recommender = $factory->makeRecommender();

        $recommender->handleBuyerPostTermAdded($bo);

    }

    private function _handleSeller(SellerPostBO $bo)
    {

        LOG::info("SellerPostCreated event handled");

        $serviceId = $bo->serviceId;

        $factory = AbstractServiceFactory::getSellerPostFactory($serviceId);

         $factory->makeRecommender();

        $recommender->handleSellerPostAdded($bo);

    }

    private function _handleBooking($bo) {
        Log::info('_handleBooking::'.$bo->id);

        $serviceId = $bo->lkp_service_id;

        $factory = AbstractServiceFactory::getOrderFactory($serviceId);

        $recommender = $factory->makeRecommender();

        $recommender->handleBuyerOrder($bo);
    }

}