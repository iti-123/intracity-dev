<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 10:37 AM
 */

namespace ApiV2\Modules\FCL;

use ApiV2\Framework\AbstractSellerQuoteFactory;
use Log;

class FCLSelleQuoteFactory extends AbstractSellerQuoteFactory
{
    public function __construct()
    {
        LOG::info('FCLSellerQuoteFactory __constructer called');
    }

    public function makeTransformer()
    {
        return new FCLSellerQuoteTransformer();
    }

    public function makeValidator()
    {
        return new FCLSellerQuoteValidator();
    }

    public function makeService()
    {
        return new FCLSellerQuoteService();
    }

    function makeIndexer()
    {
        return new FCLSellerQuoteIndexer();
    }
}