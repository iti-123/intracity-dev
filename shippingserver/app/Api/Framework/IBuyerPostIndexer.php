<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/9/2017
 * Time: 9:09 PM
 */

namespace Api\Framework;


use Api\BusinessObjects\BuyerPostBO;
use Api\BusinessObjects\BuyerPostSearchBO;

interface IBuyerPostIndexer
{
    public function rebuildIndex(BuyerPostBO $bo);

    public function dropIndex($postId);

    public function searchIndex(BuyerPostSearchBO $bo);

    public function postMasterIndex(BuyerPostSearchBO $bo);


}