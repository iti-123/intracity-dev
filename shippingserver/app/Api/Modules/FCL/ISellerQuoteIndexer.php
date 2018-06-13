<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/30/2017
 * Time: 3:19 PM
 */

namespace Api\Modules\FCL;


use Api\BusinessObjects\SellerQuoteBO;

interface ISellerQuoteIndexer
{
    public function rebuildIndex(SellerQuoteBO $bo);

    public function dropIndex($postId);
}