<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:46
 */

namespace ApiV2\Modules\FCL;


use ApiV2\Framework\AbstractSellerPostFactory;
use ApiV2\Services\SellerPostService;
use Log;


class FCLSellerPostFactory extends AbstractSellerPostFactory
{

    public function __construct()
    {
        LOG::info('FCLSellerPostFactory __constructer called');
    }


    public function makeAuthorizer()
    {
        return new FCLSellerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new FCLSellerPostTransformer();
    }

    public function makeValidator()
    {
        return new FCLSellerPostValidator();
    }

    public function makeService()
    {
        return new SellerPostService();
    }

    function makeIndexer()
    {
        return new FCLSellerPostIndexer();
    }

    function makeRecommender()
    {
        return new FCLBuyerSellerPostRecommender();
    }


}