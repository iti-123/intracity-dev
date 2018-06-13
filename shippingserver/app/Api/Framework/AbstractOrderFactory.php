<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 10:39 AM
 */

namespace Api\Framework;


abstract class AbstractOrderFactory
{
    abstract function makeService();

    abstract function makeTransformer();

    abstract function makeStateMachine($bo);

}