<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 6:12 PM
 */

namespace Api\Modules\LCL;

use Api\Framework\AbstractBuyerPostFactory;
use Log;


class LCLBuyerPostFactory extends AbstractBuyerPostFactory
{

    public function __construct()
    {
        LOG::info('LCLBuyerPostFactory __constructer called');
    }

    public function makeAuthorizer()
    {
        return new LCLBuyerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new LCLBuyerPostTransformer();
    }

    public function makeValidator()
    {
        return new LCLBuyerPostValidator();
    }

    public function makeService()
    {
        return new LCLBuyerPostService();
    }

    function makeIndexer()
    {
        return new LCLBuyerPostIndexer();
    }

    function makeRecommender()
    {
        return new LCLBuyerSellerPostRecommender();
    }


}