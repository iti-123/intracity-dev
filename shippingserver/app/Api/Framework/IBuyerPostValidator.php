<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:34
 */

namespace Api\Framework;


use Api\BusinessObjects\BuyerPostBO;

interface IBuyerPostValidator
{
    function validateGet();

    function validateSave(BuyerPostBO $bo);

    function validateDelete();
}