<?php
/**
 * Created by PhpStorm.
 * User: 10341
 * Date: 2/24/2017
 * Time: 5:09 PM
 */

namespace App\Jobs;

use Api\BusinessObjects\SellerPostBO;
use Api\Modules\AbstractServiceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SyncSellerPosts2SearchStore extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $bo = null;

    /**
     * SyncSellerPosts2SearchStore constructor.
     * @param SellerPostBO $bo
     */
    public function __construct(SellerPostBO $bo)
    {
        $this->bo = $bo;
    }

    /**
     * Executes this job
     */
    public function handle()
    {
        $serviceFactory = AbstractServiceFactory::getFactory($this->bo->serviceId, USECASE_SELLERPOST);

        if ($serviceFactory == null) {
            LOG::critical("Factory not found for Service = [" . $this->bo->serviceId . "] UseCase = [" . USECASE_SELLERPOST . "]");
            return;
        }

        $indexer = $serviceFactory->makeIndexer();
        $isRebuild = $indexer->rebuildIndex($this->bo);

        if ($isRebuild) {
            Log::info("Solr Sync finished for buyerpost with title = [" . $this->bo->title . "]");
        }

    }

}