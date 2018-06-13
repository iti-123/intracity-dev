<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 1:18 AM
 */

namespace ApiV2\Modules\Intracity;


use ApiV2\BusinessObjects\AbstractSearchBO;
use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\SellerPostBO;
use ApiV2\Modules\Common\SettingService;
use ApiV2\Services\CacheControlService;
use ApiV2\Services\NotificationService;
use DB;
use Log;

class OrderRecommender //implements IBuyerSellerPostRecommender
{
    
    public function handleBuyerOrder($bo)
    {
        Log::info('OrderRecommender :: DATA ->'.$bo);
        LOG::info("Handling Buyer Order ");
        NotificationService::notifyBookNow($bo);
        
    }

    public function handleSellerPostAdded($bo)
    {
        Log::info('handleSellerPostAdded :: DATA ->'.$bo);
        LOG::info("Handling Buyer Post Term Addition for Intracity Hyper Local");
        NotificationService::notifySellerPostCreated($bo);
        
    }

}