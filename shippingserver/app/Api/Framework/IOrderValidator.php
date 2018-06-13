<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/23/2017
 * Time: 12:59 PM
 */

namespace Api\Framework;

use Api\BusinessObjects\OrderBO;

interface IOrderValidator
{
    function validateGet();

    function validateSave(OrderBO $bo);

    function validateDelete();
}