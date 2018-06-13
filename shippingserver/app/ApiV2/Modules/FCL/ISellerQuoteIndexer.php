<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 3/30/2017
 * Time: 3:19 PM
 */

namespace ApiV2\Modules\FCL;


use ApiV2\BusinessObjects\SellerQuoteBO;

interface ISellerQuoteIndexer
{
    public function rebuildIndex(SellerQuoteBO $bo);

    public function dropIndex($postId);
}