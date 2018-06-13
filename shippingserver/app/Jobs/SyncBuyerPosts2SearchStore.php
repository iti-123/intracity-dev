<?php

namespace App\Jobs;

use Api\BusinessObjects\BuyerPostBO;
use Api\Modules\AbstractServiceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SyncBuyerPosts2SearchStore extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $bo = null;

    /**
     * SyncBuyerPosts2SearchStore constructor.
     * @param BuyerPostBO $bo
     */
    public function __construct(BuyerPostBO $bo)
    {
        $this->bo = $bo;

    }

    /**
     * Executes this job
     */
    public function handle()
    {
        $serviceFactory = AbstractServiceFactory::getFactory($this->bo->serviceId, USECASE_BUYERPOST);


        if ($serviceFactory == null) {
            LOG::critical("Factory not found for Service = [" . $this->bo->serviceId . "] UseCase = [" . USECASE_BUYERPOST . "]");
            return;
        }

        $indexer = $serviceFactory->makeIndexer();

        /*if(!empty($this->bo->postId)){
            //Only drop from search store if post is existing
            $indexer->dropIndex($this->bo->postId);
        }*/

        $isRebuild = $indexer->rebuildIndex($this->bo);

        if ($isRebuild) {
            Log::info("Solr Sync finished for buyerpost with title = [" . $this->bo->title . "]");

            //TODO : Flag sync_search as complete if post to SOLR is finished.
//            $model->sync_search = true;
//            $model->save();
        }

    }

}