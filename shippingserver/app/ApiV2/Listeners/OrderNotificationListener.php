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
use ApiV2\Services\NotificationService;
use Log;

class OrderNotificationListener
{


    public function handle($event)
    {
        LOG::info("event handled Class :: ".get_class($event));
       
        
        $this->_handleEmail($event->bo);

    }


    private function _handleEmail($bo)
    {
        LOG::info("event handled Data :: ".$bo);
        
        NotificationService::notifyDocumentEmail($bo);
        
    }

    private function _handleSeller($bo)
    {

        LOG::info("SellerPostCreated event handled");


    }

}