<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:46
 */

namespace ApiV2\Modules\FCL;

use ApiV2\Framework\AbstractBuyerPostFactory;
use Log;

class FCLBuyerPostFactory extends AbstractBuyerPostFactory
{

    public function __construct()
    {
        LOG::info('FCLBuyerPostFactory __constructer called');
    }

    public function makeAuthorizer()
    {
        return new FCLBuyerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new FCLBuyerPostTransformer();
    }

    public function makeValidator()
    {
        return new FCLBuyerPostValidator();
    }

    public function makeService()
    {
        return new FCLBuyerPostService();
    }

    function makeIndexer()
    {
        return new FCLBuyerPostIndexer();
    }

    function makeRecommender()
    {
        return new FCLBuyerSellerPostRecommender();
    }

}


