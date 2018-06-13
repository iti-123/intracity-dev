<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 14:34
 */

namespace ApiV2\Framework;

use ApiV2\BusinessObjects\SellerPostBO;

interface ISellerPostValidator
{
    function validateGet();

    function validateSave(SellerPostBO $bo);

    function validateDelete();
}