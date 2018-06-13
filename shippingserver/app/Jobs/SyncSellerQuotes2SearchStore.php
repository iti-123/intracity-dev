<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/30/2017
 * Time: 4:56 PM
 */

namespace App\Jobs;

use Api\BusinessObjects\SellerPostBO;
use Api\BusinessObjects\SellerQuoteBO;
use Api\Modules\AbstractServiceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SyncSellerQuotes2SearchStore extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $bo = null;

    /**
     * SyncSellerPosts2SearchStore constructor.
     * @param SellerPostBO $bo
     */
    public function __construct(SellerQuoteBO $bo)
    {
        $this->bo = $bo;
    }

    /**
     * Executes this job
     */
    public function handle()
    {
        $serviceFactory = AbstractServiceFactory::getFactory($this->bo->serviceId, USECASE_SELLERQUOTE);

        if ($serviceFactory == null) {
            LOG::critical("Factory not found for Service = [" . $this->bo->serviceId . "] UseCase = [" . USECASE_SELLERQUOTE . "]");
            return;
        }

        $indexer = $serviceFactory->makeIndexer();

        /*if(isset($this->bo->postId)) {
            //Only drop from search store if post is existing
            $indexer->dropIndex($this->bo->postId);
        }*/

        $isRebuild = $indexer->rebuildIndex($this->bo);

        if ($isRebuild) {
            Log::info("Solr Sync finished for SellerQuote");
            //TODO : Flag sync_search as complete if post to SOLR is finished.
//            $model->sync_search = true;
//            $model->save();
        }

    }
}