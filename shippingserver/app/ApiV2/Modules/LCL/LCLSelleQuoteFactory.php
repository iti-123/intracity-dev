<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 4/10/2017
 * Time: 12:27 PM
 */

namespace ApiV2\Modules\LCL;

use ApiV2\Framework\AbstractSellerQuoteFactory;
use Log;

class LCLSelleQuoteFactory extends AbstractSellerQuoteFactory
{
    public function __construct()
    {
        LOG::info('LCLSellerQuoteFactory __constructer called');
    }

    public function makeTransformer()
    {
        return new LCLSellerQuoteTransformer();
    }

    public function makeValidator()
    {
        return new LCLSellerQuoteValidator();
    }

    public function makeService()
    {
        return new LCLSellerQuoteService();
    }

    function makeIndexer()
    {
        return new LCLSellerQuoteIndexer();
    }
}