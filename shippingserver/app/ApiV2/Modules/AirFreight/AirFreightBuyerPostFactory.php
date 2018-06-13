<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/23/2017
 * Time: 4:04 PM
 */

namespace ApiV2\Modules\AirFreight;

use ApiV2\Framework\AbstractBuyerPostFactory;
use Log;

class AirFreightBuyerPostFactory extends AbstractBuyerPostFactory
{

    public function __construct()
    {
        LOG::info('AirFreightBuyerPostFactory __constructer called');
    }

    public function makeAuthorizer()
    {
        return new AirFreightBuyerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new AirFreightBuyerPostTransformer();
    }

    public function makeValidator()
    {
        return new AirFreightBuyerPostValidator();
    }

    public function makeService()
    {
        return new AirFreightBuyerPostService();
    }

    function makeIndexer()
    {
        return new AirFreightBuyerPostIndexer();
    }

    function makeRecommender()
    {
        return new AirFreightBuyerSellerPostRecommender();
    }


}