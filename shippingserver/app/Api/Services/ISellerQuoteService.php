<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 12:17 PM
 */

namespace Api\Services;

use Api\BusinessObjects\SellerQuoteBO;

interface ISellerQuoteService
{
    /**
     * Setter for service factory
     * @param $factory
     * @return mixed
     */
    public function setServiceFactory($factory);

    /**
     * Saves Seller Quote
     * @param SellerQuoteBO $post
     * @return mixed
     */
    public function saveOrUpdate(SellerQuoteBO $post);

    /**
     * Saves Term Seller Quote
     * @param SellerQuoteBO $post
     * @return mixed
     */
    public function saveOrUpdateTermQuotes(array $bos);

    /**
     * Get a Seller Offer for BuyerPost
     * @param $id
     * @return mixed
     */

    public function getSellerOffersByBuyerPostId($id);

    /**
     * Get Seller Inbound BuyerPosts
     * @return mixed
     */
    public function getSellerInboundBuyerPosts();


}