<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:46
 */

namespace ApiV2\Modules\AirFreight;

use ApiV2\Framework\AbstractSellerPostFactory;
use ApiV2\Services\SellerPostService;
use Log;

class AirFreightSellerPostFactory extends AbstractSellerPostFactory
{

    public function __construct()
    {
        LOG::info('AirFreightSellerPostFactory __constructer called');
    }


    public function makeAuthorizer()
    {
        return new AirFreightSellerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new AirFreightSellerPostTransformer();
    }

    public function makeValidator()
    {
        return new AirFreightSellerPostValidator();
    }

    public function makeService()
    {
        return new SellerPostService();
    }

    function makeIndexer()
    {
        return new AirFreightSellerPostIndexer();
    }

    function makeRecommender()
    {
        return new AirFreightBuyerSellerPostRecommender();
    }

}