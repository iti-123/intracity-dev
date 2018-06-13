<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:46
 */

namespace Api\Modules\LCL;

use Api\Framework\AbstractSellerPostFactory;
use Api\Services\SellerPostService;
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