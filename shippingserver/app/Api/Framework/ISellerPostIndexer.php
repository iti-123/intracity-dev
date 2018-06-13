<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/9/2017
 * Time: 9:09 PM
 */

namespace Api\Framework;


use Api\BusinessObjects\SellerPostBO;

interface ISellerPostIndexer
{
    public function rebuildIndex(SellerPostBO $bo);

    public function dropIndex($postId);

}