<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 12:41 PM
 */

namespace Api\Framework;

use Api\BusinessObjects\SellerQuoteBO;

interface ISellerQuoteValidator
{
    function validateGet();

    function validateSave(SellerQuoteBO $bo);

    function validateDelete();
}