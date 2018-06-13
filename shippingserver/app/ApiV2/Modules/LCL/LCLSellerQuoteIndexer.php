<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 4/6/2017
 * Time: 5:03 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\BusinessObjects\SellerQuoteBO;
use ApiV2\Model\FCLSearchSellerPost;
use ApiV2\Model\FCLSellerPostIndex;
use ApiV2\Services\SolrSearchService;
use ApiV2\Utils\DateUtils;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class LCLSellerQuoteIndexer
{
    public function rebuildIndex(SellerQuoteBO $bo)
    {
        $unixNow = DateUtils::unixNow();
        try {

            LOG::info("Rebuilding search index for sellerpost " . $bo->quoteId);

            $sellerPostIndexes = [];

            $index = [];
            $now = date('Y-m-d H:i:s');
            $index['entity'] = "sellerquote";
            //$index['serviceSubType'] = $bo->serviceSubType;
            $index['postId'] = $bo->quoteId;
            $index['serviceId'] = $bo->serviceId;
            $index['serviceName'] = "LCL";
            $index['sellerId'] = $bo->sellerId;
            $index['sellerName'] = JWTAuth::parseToken()->getPayload()->get('firstname');
            $index['title'] = "N/A";//$bo->title;
            $index['validFrom'] = $unixNow;
            $index['validTo'] = $bo->validTill;
            $index['isPublic'] = false;
            $index['status'] = $bo->status;
            $index['isDeleted'] = 0;
            $index['created_at'] = $now;
            $index['updated_at'] = $now;
            $index['loadPort'] = $bo->loadPort;
            $index['dischargePort'] = $bo->dischargePort;

            LOG::info("Pushing " . count($index) . " entries to index table for SellerPostId = " . $bo->quoteId);

            //Push collected index records to database
            FCLSearchSellerPost::insert($index);

            LOG::info("Finished rebuilding search index for sellerpost " . $bo->quoteId);

            $service = new SolrSearchService();
            $service->deltaImport("sellerposts");

            return true;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Failed posting sellerpost to Search Store"]);

        }

    }

    /**
     * @param $postId
     * @throws ApplicationException
     */
    public function dropIndex($postId)
    {
        LOG::info('Dropping existing search index for sellerpost = ' . $postId);
        try {

            $delQuery = "{'delete': {'query': 'entity:sellerpost and sellerPostId:$postId'}}";

            $service = new SolrSearchService();
            $service->remove($delQuery);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException(["postid" => $postId], ["Error deleting existing sellerposts from search store"]);
        }
    }

}