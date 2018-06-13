<?php

namespace Api\Modules\FCL;

use Log;

class FCLCartItemFactory
{
    public function __construct()
    {
        Log::info('FCLCartItemFactory __constructer called');
    }

    public function makeAuthorizer()
    {
        return new FCLCartItemAuthorizer();
    }

    public function makeTransformer()
    {
        return new FCLCartItemTransformer();
    }

    public function makeValidator()
    {
        return new FCLCartItemValidator();
    }

    public function makeService()
    {
        return new FCLCartItemService();
    }

    public function makeOrderService()
    {
        return new FCLOrderService();
    }

}