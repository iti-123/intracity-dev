<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:46
 */

namespace ApiV2\Modules\LCL;

use ApiV2\Framework\AbstractSellerPostFactory;
use ApiV2\Services\SellerPostService;
use Log;


class LCLSellerPostFactory extends AbstractSellerPostFactory
{

    public function __construct()
    {
        LOG::info('LCLSellerPostFactory __constructer called');
    }


    public function makeAuthorizer()
    {
        return new LCLSellerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new LCLSellerPostTransformer();
    }

    public function makeValidator()
    {
        return new LCLSellerPostValidator();
    }

    public function makeService()
    {
        return new SellerPostService();
    }

    function makeIndexer()
    {
        return new LCLSellerPostIndexer();
    }

    function makeRecommender()
    {
        return new LCLBuyerSellerPostRecommender();
    }


}