<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 23/2/17
 * Time: 11:43 PM
 */

namespace ApiV2\Modules\RoRo;

use ApiV2\Framework\AbstractBuyerPostFactory;
use Log;

class RoRoBuyerPostFactory extends AbstractBuyerPostFactory
{

    public function __construct()
    {
        LOG::info('RoRoBuyerPostFactory __constructer called');
    }

    public function makeAuthorizer()
    {
        return new RoRoBuyerPostAuthorizer();
    }

    public function makeTransformer()
    {
        return new RoRoBuyerPostTransformer();
    }

    public function makeValidator()
    {
        return new RoRoBuyerPostValidator();
    }

    public function makeService()
    {
        return new RoRoBuyerPostService();
    }

    function makeIndexer()
    {
        return new RoRoBuyerPostIndexer();
    }


}