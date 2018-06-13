<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/9/2017
 * Time: 9:09 PM
 */

namespace ApiV2\Framework;


use ApiV2\BusinessObjects\SellerPostBO;

interface ISellerPostIndexer
{
    public function rebuildIndex(SellerPostBO $bo);

    public function dropIndex($postId);

}