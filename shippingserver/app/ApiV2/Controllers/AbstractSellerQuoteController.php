<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 9:50 AM
 */

namespace ApiV2\Controllers;

use ApiV2\Framework\AbstractUsecaseController;
use ApiV2\Requests\BaseShippingResponse as shipres;
use Illuminate\Http\Request;
use Log;


class AbstractSellerQuoteController extends AbstractUsecaseController
{
    /**
     * Seller Offer To Buyer Post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrUpdateQuote(Request $request)
    {
        try {
            $payload = $request->getContent();

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload, "spot");

            LOG::info('Getting SellerQuote service instance');
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->saveOrUpdate($bo);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * Seller Offer To Buyer Post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrUpdateTermQuote(Request $request)
    {

        try {

            $payload = $request->getContent();

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload, "term");
            LOG::info('Getting SellerQuote Term service instance');
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->saveOrUpdateTermQuotes($bo);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function getQuoteDetailsByQuoteId($quoteId)
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set buyerQuote Factory
            $this->postService->setServicefactory($this->serviceFactory);

            $sp = $this->postService->getQuoteDetailsByQuoteId($quoteId);

            //Return Response
            LOG::info('response  from seller Quote Service ', (array)$sp);
            return shipres::ok($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    public function getSellerOffersByBuyerPostId($id)
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set buyerQuote Factory
            $this->postService->setServicefactory($this->serviceFactory);

            $sp = $this->postService->getSellerOffersByBuyerPostId($id);

            //Return Response
            LOG::info('response  from seller Quote Service ', (array)$sp);
            return shipres::ok($sp);//response()->json($sp);


        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    public function getTermSellerOffersByBuyerPostId($id)
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set buyerQuote Factory
            $this->postService->setServicefactory($this->serviceFactory);

            $sp = $this->postService->getTermSellerOffersByBuyerPostId($id);

            //Return Response
            LOG::info('response  from seller Quote Service ', (array)$sp);
            return shipres::ok($sp);//response()->json($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

    public function getSellerInboundBuyerPosts()
    {

        try {

            $this->postService = $this->serviceFactory->makeService();

            //Set buyerQuote Factory
            $this->postService->setServicefactory($this->serviceFactory);

            $sp = $this->postService->getSellerInboundBuyerPosts();

            //Return Response
            LOG::info('response  from seller Quote Service ', (array)$sp);
            return shipres::ok($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

}