<?php

namespace Api\Modules\FCL;

use Api\Framework\AbstractOrderFactory;
use Api\Framework\Workflow\StateMachineFactory;
use Log;

class FCLOrderFactory extends AbstractOrderFactory
{
    public function __construct()
    {
        Log::info('FCLOrderFactory __constructer called');
    }

    public function makeService()
    {
        return new FCLOrderService();
    }

    public function makeTransformer()
    {
        return new FCLOrderTransformer();
    }

    public function makeStateMachine($bo)
    {
        StateMachineFactory::$bo = $bo;
        $name = FCLOrderStateMachine::NAME;
        return StateMachineFactory::get($name);
    }

}
