<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/9/2017
 * Time: 9:09 PM
 */

namespace ApiV2\Framework;


use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\BuyerPostSearchBO;

interface IBuyerPostIndexer
{
    public function rebuildIndex(BuyerPostBO $bo);

    public function dropIndex($postId);

    public function searchIndex(BuyerPostSearchBO $bo);

    public function postMasterIndex(BuyerPostSearchBO $bo);


}