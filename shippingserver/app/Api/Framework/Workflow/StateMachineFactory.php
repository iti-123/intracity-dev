<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/21/17
 * Time: 10:40 AM
 */

namespace Api\Framework\Workflow;


use App\Exceptions\ApplicationException;

class StateMachineFactory
{

    static $stateMachines = [];

    static $bo = [];

    static function get($stateMachineName)
    {

        if (count(StateMachineFactory::$stateMachines) == 0) {
            self::build($stateMachineName);
        }

        if (!self::$stateMachines[$stateMachineName]) {
            throw new ApplicationException(["stateMachine" => $stateMachineName], ["Invalid state machine"]);
        }

        return self::$stateMachines[$stateMachineName];
    }

    private static function build($stateMachineName)
    {

        if (!self::$bo) {
            throw new ApplicationException(["stateMachine" => $stateMachineName], ["Invalid state machine"]);
        }
        $orderStateMachine = new $stateMachineName(self::$bo);
        self::$stateMachines[$orderStateMachine->getName()] = $orderStateMachine;

    }

}