<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 12:33 AM
 */

namespace ApiV2\Services;


use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\SellerPostBO;

interface IBuyerSellerPostRecommender
{

    public function handleBuyerPostAdded(BuyerPostBO $bo);

    public function handleSellerPostAdded(SellerPostBO $bo);

    public function computeSellerLeadsEnquiries($sellerId);

    public function computeBuyerLeadsEnquiries($buyerId);

    public function filterSellerInboundPostMaster($sellerId, $filter);

}