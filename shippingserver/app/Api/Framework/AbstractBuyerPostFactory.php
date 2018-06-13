<?php

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:43
 */

namespace Api\Framework;

abstract class AbstractBuyerPostFactory
{

    abstract function makeService();

    abstract function makeAuthorizer();

    abstract function makeTransformer();

    abstract function makeValidator();

    abstract function makeIndexer();

    abstract function makeRecommender();
}