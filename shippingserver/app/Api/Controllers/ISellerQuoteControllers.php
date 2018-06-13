<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 9:51 AM
 */

namespace Api\Controllers;

use Illuminate\Http\Request;

interface ISellerQuoteControllers
{
    public function saveOrUpdateQuote(Request $request);

    public function saveOrUpdateTermQuote(Request $request);

    public function getSellerOffersByBuyerPostId($id);

    public function getTermSellerOffersByBuyerPostId($id);
}